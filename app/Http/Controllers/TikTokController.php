<?php

namespace App\Http\Controllers;

use App\Models\MediaPlatform;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use Illuminate\Http\Request;
use BeePost\SocialPoster\Services\Account\tiktok\Account as TikTokAccount;
use Exception;
use File;


class TikTokController extends Controller
{
    public function redirectToTikTok()
    {
        try {
            $platform = MediaPlatform::where('slug', 'tiktok')->firstOrFail();
            $url = TikTokAccount::authRedirect($platform);

            return redirect()->away($url);

        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error connecting account: '.$e->getMessage());
        }
    }

    public function handleTikTokCallback(Request $request)
    {
        try {
            $platform = MediaPlatform::where('slug', 'tiktok')->firstOrFail();
            $code = $request->input('code');
            info('TikTok code is '.$code);

            $tokenResponse = TikTokAccount::getAccessToken($code, $platform);
            info('TikTok Token response is');
            info($tokenResponse);

            $token = $tokenResponse['access_token'];
            info('TikTok token is '.$token);

            $tiktokAccount = new TikTokAccount();

            $pages = $tiktokAccount->getAccount($token, $platform);
            info('TikTok pages are');
            info($pages);

            
            return redirect('/')->with('success', 'TikTok account connected successfully!');
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error connecting account: '.$e->getMessage());
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
            info('The result after calling the youtube send method');
            info(json_encode($result));

            if ($result['status'] === true) {
                return back()->with('success', $result['response'])->with('url', $result['url'] ?? null);
            } else {
                return back()->with('error', 'Upload failed: '.($result['response'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error uploading video: '.$e->getMessage());
        }
    }

    public function showUploadForm()
    {
        return view('upload-tiktok');
    }
}
