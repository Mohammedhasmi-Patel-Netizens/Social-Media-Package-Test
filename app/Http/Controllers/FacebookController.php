<?php

namespace App\Http\Controllers;

use App\Models\Core\File;
use App\Models\MediaPlatform;
use BeePost\SocialPoster\Enums\AccountType;
use BeePost\SocialPoster\Enums\ConnectionType;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use BeePost\SocialPoster\Services\Account\facebook\Account as FacebookAccount;
use Illuminate\Http\Request;
use Exception;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        try {
            $platform = MediaPlatform::where('slug', 'facebook')->firstOrFail();
            $url = FacebookAccount::authRedirect($platform);

            return redirect()->away($url);

        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error connecting account: '.$e->getMessage());
        }
    }

    public function handleFacebookCallback(Request $request)
    {
        // 1. Fetch the Facebook platform
        $platform = MediaPlatform::query()->where(['slug' => 'facebook'])->firstOrFail();

        // 2. Get the authorization code returned by Facebook
        $code = $request->input('code');
        info('code is '.$code);

        try {
            // 3. Exchange the code for the access token
            $tokenResponse = FacebookAccount::getAccessToken($code, $platform);

            info('Token response is');
            info($tokenResponse);

            // 4. Save the Facebook Account into your database using the package's method
            FacebookAccount::saveFbAccount(
                $tokenResponse,
                'web',
                $platform,
                (string) AccountType::PROFILE->value,
                (string) ConnectionType::OFFICIAL->value
            );

            return redirect('/')->with('success', 'Facebook account connected successfully!');
        } catch (Exception $e) {
            // Handle any API errors
            return redirect('/')->with('error', 'Error connecting account: '.$e->getMessage());
        }
    }

    public function showUploadForm()
    {
        return view('upload-fb');
    }

    public function uploadPost(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:10000',
        ]);

        try {
            $platform = MediaPlatform::where('slug', 'facebook')->firstOrFail();
            $account = SocialAccount::where('platform_id', $platform->id)->firstOrFail();

            $post = new SocialPost([
                'content' => $request->input('content'),
                'post_type' => PostType::FEED->value,
                'account_id' => $account->id,
            ]);
            $post->setRelation('account', $account);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('images'), $filename);

                $fileModel = new File([
                    'path' => 'images/'.$filename,
                ]);
                $post->setRelation('file', collect([$fileModel]));
            }

            $facebookApi = new FacebookAccount;
            $result = $facebookApi->send($post);

            info('The result after calling the facebook send method');
            info(json_encode($result));

            if (isset($result['status']) && $result['status'] === true) {
                return back()->with('success', $result['response'] ?? 'Posted successfully!')->with('url', $result['url'] ?? null);
            } else {
                return back()->with('error', 'Upload failed: '.($result['response'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
