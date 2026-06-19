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
use Illuminate\Http\Request;
use Exception;


class OAuthController extends Controller
{
    public function redirectToYoutube()
    {
        try {

            $platform = MediaPlatform::where('slug', 'youtube')->firstOrFail();

            $url = YoutubeAccount::authRedirect($platform);

            return redirect()->away($url);
        } catch (Exception $e) {
            dd($e->getMessage());
        }

    }

    public function handleYoutubeCallback(Request $request)
    {
        // 1. Fetch the YouTube platform
        $platform = MediaPlatform::query()->where(['slug' => 'youtube'])->firstOrFail();

        // 2. Get the authorization code returned by Google
        $code = $request->input('code');
        info('code is '.$code);

        if (! $code) {
            return redirect('/')->with('error', 'YouTube Authentication failed or was cancelled.');
        }

        try {
            // 3. Exchange the code for the access token & refresh token
            $tokenResponse = YoutubeAccount::getAccessToken($code, $platform);

            info('Tokenn response is');
            info($tokenResponse);

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
            // Handle any API errors
            return redirect('/')->with('error', 'Error connecting account: '.$e->getMessage());
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
            // Increase max limit as needed (50000 = 50MB)
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
                return back()->with('error', 'Upload failed: '.($result['response'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
