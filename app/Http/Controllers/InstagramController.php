<?php

namespace App\Http\Controllers;

use App\Models\MediaPlatform;
use BeePost\SocialPoster\Enums\AccountType;
use BeePost\SocialPoster\Enums\ConnectionType;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use BeePost\SocialPoster\Services\Account\instagram\Account as InstagramAccount;
use Illuminate\Http\Request;
use Exception;
use File;

class InstagramController extends Controller
{
    public function redirectToInstagram()
    {
        try {
            $platform = MediaPlatform::where('slug', 'instagram')->firstOrFail();
            $url = InstagramAccount::authRedirect($platform);

            return redirect()->away($url);
        }catch(Exception $e){
            return redirect('/')->with('error', 'Error connecting account: '.$e->getMessage());
        }
    }

    public function handleInstagramCallback(Request $request)
    {
        try {
            $platform = MediaPlatform::where('slug', 'instagram')->firstOrFail();
            $code = $request->input('code');
            info('code is '.$code);
            $tokenResponse = InstagramAccount::getAccessToken($code, $platform);
            info('Token response is');
            info($tokenResponse);

            $token = $tokenResponse['access_token'];
            
            info('token is '.$token);
            
            $pages = InstagramAccount::getAccounts($token, $platform);
            info('pages are');
            info($pages);

            InstagramAccount::saveIgAccount(
                $pages,
                'web',
                $platform,
                (string) AccountType::PROFILE->value,
                (string) ConnectionType::OFFICIAL->value,
                $token,
                null
            );

            return redirect('/')->with('success', 'Instagram account connected successfully!');
        }catch(Exception $e){
            return redirect('/')->with('error', 'Error connecting account: '.$e->getMessage());
        }
    }

    public function showUploadForm()
    {
        return view('upload-ig');
    }

    public function uploadPost(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:10000',
        ]);

        try {
            $platform = MediaPlatform::where('slug', 'instagram')->firstOrFail();
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

            $instagramApi = new InstagramAccount;
            $result = $instagramApi->send($post);

            info('The result after calling the instagram send method');
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
