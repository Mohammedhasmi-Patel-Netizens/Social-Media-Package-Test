<?php

namespace App\Http\Controllers;

use App\Models\MediaPlatform;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use Illuminate\Http\Request;
use BeePost\SocialPoster\Services\Account\threads\Account as ThreadsAccount;
use BeePost\SocialPoster\Enums\AccountType;
use BeePost\SocialPoster\Enums\ConnectionType;
use Exception;

class ThreadsController extends Controller
{
    public function redirectToThreads()
    {
        try {
            $platform = MediaPlatform::where('slug', 'threads')->firstOrFail();
            $url = ThreadsAccount::authRedirect($platform);

            return redirect()->away($url);
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error connecting account: ' . $e->getMessage());
        }
    }

    // public function handleThreadsCallback(Request $request)
    // {
    //     try {
    //         $platform = MediaPlatform::where('slug', 'threads')->firstOrFail();
    //         $code = $request->input('code');
    //         info('Threads code is ' . $code);

    //         $tokenResponse = ThreadsAccount::getAccessToken($code, $platform);
    //         info('Threads Token response is');
    //         info($tokenResponse);

    //         $token = $tokenResponse['access_token'];
    //         info('Threads token is ' . $token);

    //         $pages = ThreadsAccount::getAcccount($token, $platform);
    //         info('Threads pages are');
    //         info($pages);


    //         ThreadsAccount::saveThAccount(
    //            $tokenResponse,
    //            "web",
    //            $platform,
    //            AccountType::PROFILE->value,
    //            ConnectionType::OFFICIAL->value,
    //            null
    //         );

    //         return redirect('/')->with('success', 'Threads account connected successfully!');
    //     } catch (Exception $e) {
    //         return redirect('/')->with('error', 'Error connecting account: ' . $e->getMessage());
    //     }
    // }

    public function showUploadForm()
    {
        return view('upload-threads');
    }

    public function uploadPost(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:10000',
        ]);

        try {
            $platform = MediaPlatform::where('slug', 'threads')->firstOrFail();
            $account = SocialAccount::where('platform_id', $platform->id)->firstOrFail();

            $post = new SocialPost([
                'content' => $request->input('content'),
                'post_type' => PostType::FEED->value,
                'account_id' => $account->id,
            ]);
            $post->setRelation('account', $account);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);

                $fileModel = new File([
                    'path' => 'images/' . $filename,
                ]);
                $post->setRelation('file', collect([$fileModel]));
            }

            $threadsApi = new ThreadsAccount;
            $result = $threadsApi->send($post);

            info('The result after calling the threads send method');
            info(json_encode($result));

            if (isset($result['status']) && $result['status'] === true) {
                return back()->with('success', $result['response'] ?? 'Posted successfully!')->with('url', $result['url'] ?? null);
            } else {
                return back()->with('error', 'Upload failed: ' . ($result['response'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
