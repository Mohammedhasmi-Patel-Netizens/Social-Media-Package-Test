<?php

namespace App\Http\Controllers;

use BeePost\SocialPoster\Enums\AccountType;
use BeePost\SocialPoster\Enums\ConnectionType;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use Illuminate\Http\Request;
use App\Models\MediaPlatform;
use BeePost\SocialPoster\Services\Account\linkedin\Account as LinkedInAccount;
use Exception;
use File;


class LinkedInController extends Controller
{
    public function redirectToLinkedIn()
    {
        try {
            $platform = MediaPlatform::where('slug', 'linkedin')->firstOrFail();
            $url = LinkedInAccount::authRedirect($platform);

            return redirect()->away($url);
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error connecting account: '.$e->getMessage());
        }
    }

    public function handleLinkedInCallback(Request $request)
    {
        try {
            $platform = MediaPlatform::where('slug', 'linkedin')->firstOrFail();
            $code = $request->input('code');


            $tokenResponse = LinkedInAccount::getAccessToken($code, $platform);
            info($tokenResponse);

           LinkedInAccount::saveLdAccount(
                $tokenResponse,
                'web',
                $platform,
                (string) AccountType::PROFILE->value,
                (string) ConnectionType::OFFICIAL->value,
                $tokenResponse->json('access_token'),
                $tokenResponse->json('expires_in'),
                null
            );

            return redirect('/')->with('success', 'LinkedIn account connected successfully!');
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error connecting account: '.$e->getMessage());
        }
    }

    public function showUploadForm()
    {
        return view('upload-linkedin');
    }

    public function uploadPost(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:10000',
        ]);

        try {
            $platform = MediaPlatform::where('slug', 'linkedin')->firstOrFail();
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

            $linkedinApi = new LinkedInAccount;
            $result = $linkedinApi->send($post);

            info('The result after calling the linkedin send method');
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
