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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        try {
            $platform = MediaPlatform::where('slug', 'facebook')->firstOrFail();
            $url = FacebookAccount::authRedirect($platform);

            return redirect()->away($url);

        } catch (Exception $e) {
            Log::error('Facebook auth redirect failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Error connecting account. Please try again later.');
        }
    }

    public function handleFacebookCallback(Request $request)
    {
        if ($request->has('error')) {
            Log::warning('Facebook auth error returned from provider', [
                'error' => $request->input('error'),
                'error_description' => $request->input('error_description'),
                'error_reason' => $request->input('error_reason'),
            ]);

            return redirect('/')->with('error', 'Facebook authentication was cancelled or failed.');
        }

        try {
            // 1. Fetch the Facebook platform
            $platform = MediaPlatform::where('slug', 'facebook')->firstOrFail();

            // 2. Get the authorization code returned by Facebook
            $code = $request->input('code');
            if (!$code) {
                Log::error('Facebook callback missing authorization code', ['request_all' => $request->all()]);

                return redirect('/')->with('error', 'Invalid response from Facebook.');
            }

            Log::info('Facebook callback received code', ['request' => $request->all(), 'code_length' => strlen($code)]);

            $tokenResponse = FacebookAccount::getAccessToken($code, $platform);
            $token = $tokenResponse->json('access_token');

            Log::info('Facebook token response received', ['tokenResponse' => $tokenResponse->json()]);

            $pages = FacebookAccount::getPagesInfo(['id,name,username,picture{url},access_token'], $platform, $token);

            FacebookAccount::saveFbAccount(
                $pages,
                'web',
                $platform,
                (string) AccountType::PAGE->value,
                (string) ConnectionType::OFFICIAL->value
            );

            return redirect('/')->with('success', 'Facebook account connected successfully!');
        } catch (Exception $e) {
            Log::error('Facebook callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'An error occurred while connecting the account. Please try again.');
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
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);

                $fileModel = new File([
                    'path' => 'images/' . $filename,
                ]);
                $post->setRelation('file', collect([$fileModel]));
            }

            $facebookApi = new FacebookAccount;
            $result = $facebookApi->send($post);

            Log::info('Facebook post upload result', ['result' => $result]);

            if (isset($result['status']) && $result['status'] === true) {
                return back()->with('success', $result['response'] ?? 'Posted successfully!')->with('url', $result['url'] ?? null);
            } else {
                Log::error('Facebook post upload failed at provider', ['result' => $result, 'post_content' => $request->input('content')]);

                return back()->with('error', 'Upload failed: ' . ($result['response'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            Log::error('Facebook post upload exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('image'),
            ]);

            return back()->with('error', 'An unexpected error occurred during upload.');
        }
    }
}
