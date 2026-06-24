<?php

namespace App\Http\Controllers;

use App\Models\MediaPlatform;
use BeePost\SocialPoster\Enums\AccountType;
use BeePost\SocialPoster\Enums\ConnectionType;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use BeePost\SocialPoster\Services\Account\instagram\Account as InstagramAccount;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstagramController extends Controller
{
    public function redirectToInstagram()
    {
        try {
            $platform = MediaPlatform::where('slug', 'instagram')->firstOrFail();
            $url = InstagramAccount::authRedirect($platform);

            return redirect()->away($url);
        } catch (Exception $e) {
            Log::error('Instagram auth redirect failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Error connecting account. Please try again later.');
        }
    }

    public function handleInstagramCallback(Request $request)
    {
        try {

            if ($request->has('error')) {
                Log::warning('Instagram auth error returned from provider', [
                    'error' => $request->input('error'),
                    'error_description' => $request->input('error_description'),
                    'error_reason' => $request->input('error_reason'),
                ]);

                return redirect('/')->with('error', 'Instagram authentication was cancelled or failed.');
            }
            $platform = MediaPlatform::where('slug', 'instagram')->firstOrFail();
            $code = $request->input('code');
            if (! $code) {
                Log::error('Instagram callback missing authorization code', ['request_all' => $request->all()]);

                return redirect('/')->with('error', 'Invalid response from Instagram.');
            }
            $tokenResponse = InstagramAccount::getAccessToken($code, $platform);
            $token = $tokenResponse->json('access_token');
            $pagesResponse = InstagramAccount::getAccounts(['connected_instagram_account,name,access_token'], $platform, $token);
            $pages = $pagesResponse->json('data') ?? [];

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
        } catch (Exception $e) {
            Log::error('Instagram callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'An error occurred while connecting the account. Please try again.');
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

            Log::info('Instagram post upload result', ['result' => $result]);

            if (isset($result['status']) && $result['status'] === true) {
                return back()->with('success', $result['response'] ?? 'Posted successfully!')->with('url', $result['url'] ?? null);
            } else {
                Log::error('Instagram post upload failed at provider', ['result' => $result, 'post_content' => $request->input('content')]);

                return back()->with('error', 'Upload failed: '.($result['response'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            Log::error('Instagram post upload exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('image'),
            ]);

            return back()->with('error', 'An unexpected error occurred during upload.');
        }
    }
}
