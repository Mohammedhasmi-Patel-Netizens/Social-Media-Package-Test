<?php

namespace App\Http\Controllers;

use App\Models\Core\File;
use App\Models\MediaPlatform;
use BeePost\SocialPoster\Enums\AccountType;
use BeePost\SocialPoster\Enums\ConnectionType;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use BeePost\SocialPoster\Services\Account\youtube\Account as YoutubeAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class YoutubeController extends Controller
{
    public function redirectToYoutube()
    {
        try {
            $platform = MediaPlatform::where('slug', 'youtube')->firstOrFail();
            $url = YoutubeAccount::authRedirect($platform);

            return redirect()->away($url);
        } catch (Exception $e) {
            Log::error('YouTube auth redirect failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Error connecting account. Please try again later.');
        }

    }

    public function handleYoutubeCallback(Request $request)
    {
        if ($request->has('error')) {
            Log::warning('YouTube auth error returned from provider', [
                'error' => $request->input('error'),
                'error_description' => $request->input('error_description'),
                'error_reason' => $request->input('error_reason'),
            ]);

            return redirect('/')->with('error', 'YouTube authentication was cancelled or failed.');
        }

        try {
            // 1. Fetch the YouTube platform
            $platform = MediaPlatform::where('slug', 'youtube')->firstOrFail();

            // 2. Get the authorization code returned by Google
            $code = $request->input('code');
            if (! $code) {
                Log::error('YouTube callback missing authorization code', ['request_all' => $request->all()]);

                return redirect('/')->with('error', 'Invalid response from YouTube.');
            }

            Log::info('YouTube callback received code', ['request' => $request->all(), 'code_length' => strlen($code)]);

            // 3. Exchange the code for the access token & refresh token
            $tokenResponse = YoutubeAccount::getAccessToken($code, $platform);
            Log::info('YouTube token response received', ['tokenResponse' => $tokenResponse]);

            // 4. Save the YouTube Account into your database using the package's method
            YoutubeAccount::saveYtAccount(
                $tokenResponse,
                'web', // The authentication guard (change to 'admin' if your users are admins)
                $platform,
                (string) AccountType::PROFILE->value,
                (string) ConnectionType::OFFICIAL->value
            );

            return redirect('/')->with('success', 'YouTube account connected successfully!');
        } catch (Exception $e) {
            Log::error('YouTube callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Handle any API errors
            return redirect('/')->with('error', 'An error occurred while connecting the account. Please try again.');
        }
    }

    public function showUploadForm()
    {
        return view('upload');
    }

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'video' => 'required|mimes:mp4,mov,avi,webm|max:50000',
        ]);

        try {
            $file = $request->file('video');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('videos'), $filename);

            $platform = MediaPlatform::where('slug', 'youtube')->firstOrFail();
            $account = SocialAccount::where('platform_id', $platform->id)->firstOrFail();

            // 3. Create a fake File model for the package to use
            // (The package calls filePath($file) which we defined in web.php to return ->path)
            $fileModel = new File([
                'path' => 'videos/'.$filename,
            ]);

            $post = new SocialPost([
                'content' => $request->input('title'),
                'post_type' => PostType::SHORTS->value,
                'account_id' => $account->id,
            ]);

            $post->setRelation('account', $account);
            $post->setRelation('file', collect([$fileModel]));

            $youtubeApi = new YoutubeAccount;
            $result = $youtubeApi->send($post);
            info('The result after calling the youtube send method');
            info(json_encode($result));

            if ($result['status'] === true) {
                return back()->with('success', $result['response'])->with('url', $result['url'] ?? null);
            } else {
                Log::error('YouTube video upload failed at provider', ['result' => $result, 'post_content' => $request->input('title')]);

                return back()->with('error', 'Upload failed: '.($result['response'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            Log::error('YouTube video upload exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('video'),
            ]);

            return back()->with('error', 'An unexpected error occurred during upload.');
        }
    }
}
