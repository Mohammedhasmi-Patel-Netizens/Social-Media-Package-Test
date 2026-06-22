<?php

namespace App\Http\Controllers;

use App\Models\Core\File;
use App\Models\MediaPlatform;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use BeePost\SocialPoster\Services\Account\tiktok\Account as TikTokAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TikTokController extends Controller
{
    public function redirectToTikTok()
    {
        try {
            $platform = MediaPlatform::where('slug', 'tiktok')->firstOrFail();
            $url = TikTokAccount::authRedirect($platform);

            return redirect()->away($url);

        } catch (Exception $e) {
            Log::error('TikTok auth redirect failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Error connecting account. Please try again later.');
        }
    }

    public function handleTikTokCallback(Request $request)
    {
        if ($request->has('error')) {
            Log::warning('TikTok auth error returned from provider', [
                'error' => $request->input('error'),
                'error_description' => $request->input('error_description'),
                'error_reason' => $request->input('error_reason'),
            ]);

            return redirect('/')->with('error', 'TikTok authentication was cancelled or failed.');
        }

        try {
            $platform = MediaPlatform::where('slug', 'tiktok')->firstOrFail();
            $code = $request->input('code');
            if (! $code) {
                Log::error('TikTok callback missing authorization code', ['request_all' => $request->all()]);

                return redirect('/')->with('error', 'Invalid response from TikTok.');
            }

            Log::info('TikTok callback received code', ['request' => $request->all(), 'code_length' => strlen($code)]);

            $tokenResponse = TikTokAccount::getAccessToken($code, $platform);
            Log::info('TikTok token response received', ['tokenResponse' => $tokenResponse]);

            $token = $tokenResponse['access_token'];

            $tiktokAccount = new TikTokAccount;

            $pages = $tiktokAccount->getAccount($token, $platform);
            Log::info('TikTok pages received', ['pages' => $pages]);

            return redirect('/')->with('success', 'TikTok account connected successfully!');
        } catch (Exception $e) {
            Log::error('TikTok callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'An error occurred while connecting the account. Please try again.');
        }
    }

    public function uploadVideo(Request $request)
    {
        try {
            $file = $request->file('video');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('videos'), $filename);

            $platform = MediaPlatform::where('slug', 'tiktok')->firstOrFail();
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

            $tiktokApi = new TikTokAccount;
            $result = $tiktokApi->send($post);
            Log::info('TikTok video upload result', ['result' => $result]);

            if ($result['status'] === true) {
                return back()->with('success', $result['response'])->with('url', $result['url'] ?? null);
            } else {
                Log::error('TikTok video upload failed at provider', ['result' => $result, 'post_content' => $request->input('title')]);

                return back()->with('error', 'Upload failed: '.($result['response'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            Log::error('TikTok video upload exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('video'),
            ]);

            return redirect('/')->with('error', 'An unexpected error occurred during upload.');
        }
    }

    public function showUploadForm()
    {
        return view('upload-tiktok');
    }
}
