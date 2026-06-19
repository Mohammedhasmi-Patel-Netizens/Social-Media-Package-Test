<?php

namespace App\Http\Controllers;

use App\Models\MediaPlatform;
use BeePost\SocialPoster\Services\Account\twitter\Account as TwitterAccount;
use Illuminate\Http\Request;
use Exception;

class TwitterController extends Controller
{
    public function redirectToTwitter()
    {
        try {
            $platform = MediaPlatform::where('slug', 'twitter')->firstOrFail();
            $url = TwitterAccount::authRedirect($platform);

            return redirect()->away($url);

        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error connecting account: ' . $e->getMessage());
        }
    }

    public function handleTwitterCallback(Request $request)
    {
        try {
            $platform = MediaPlatform::where('slug', 'twitter')->firstOrFail();
            $code = $request->input('code');
            info('Twitter code is ' . $code);

            $tokenResponse = TwitterAccount::getAccessToken($code, $platform);
            info('Twitter Token response is');
            info($tokenResponse);

            $token = $tokenResponse['access_token'];
            info('Twitter token is ' . $token);

            $twitterAccount = new TwitterAccount();

            $pages = $twitterAccount->getAccount($token, $platform);
            info('Twitter pages are');
            info($pages);


            return redirect('/')->with('success', 'Twitter account connected successfully!');
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error connecting account: ' . $e->getMessage());
        }
    }

    public function showUploadForm()
    {
        return view('upload-twitter');
    }

    public function uploadPost(Request $request)
    {
        try {
            $platform = MediaPlatform::where('slug', 'twitter')->firstOrFail();
            $account = \BeePost\SocialPoster\Models\SocialAccount::where('platform_id', $platform->id)->firstOrFail();

            $post = new \BeePost\SocialPoster\Models\SocialPost([
                'content' => $request->input('content'),
                'post_type' => \BeePost\SocialPoster\Enums\PostType::POST->value,
                'account_id' => $account->id,
            ]);

            $post->setRelation('account', $account);

            $fileModels = collect([]);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);

                $fileModel = new \File([
                    'path' => 'images/' . $filename,
                ]);
                $fileModels->push($fileModel);
            }
            $post->setRelation('file', $fileModels);

            $twitterApi = new TwitterAccount;
            $result = $twitterApi->send($post);
            info('The result after calling the twitter send method');
            info(json_encode($result));

            if ($result['status'] === true) {
                return back()->with('success', $result['response'])->with('url', $result['url'] ?? null);
            } else {
                return back()->with('error', 'Upload failed: ' . ($result['response'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error uploading post: ' . $e->getMessage());
        }
    }
}
