<?php

namespace App\Http\Controllers;

use App\Models\Core\File;
use App\Models\MediaPlatform;
use BeePost\SocialPoster\Enums\AccountType;
use BeePost\SocialPoster\Enums\ConnectionType;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use BeePost\SocialPoster\Services\Account\linkedin\Account as LinkedInAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;


class LinkedInController extends Controller
{
    public function redirectToLinkedIn()
    {
        try {
            $platform = MediaPlatform::where('slug', 'linkedin')->firstOrFail();
            $url = LinkedInAccount::authRedirect($platform);

            return redirect()->away($url);
        } catch (Exception $e) {
            Log::error('LinkedIn auth redirect failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Error connecting account. Please try again later.');
        }
    }

    public function handleLinkedInCallback(Request $request)
    {
        if ($request->has('error')) {
            Log::warning('LinkedIn auth error returned from provider', [
                'error' => $request->input('error'),
                'error_description' => $request->input('error_description'),
                'error_reason' => $request->input('error_reason'),
            ]);

            return redirect('/')->with('error', 'LinkedIn authentication was cancelled or failed.');
        }

        try {
            $platform = MediaPlatform::where('slug', 'linkedin')->firstOrFail();

            $code = $request->input('code');
            if (!$code) {
                Log::error('LinkedIn callback missing authorization code', ['request_all' => $request->all()]);

                return redirect('/')->with('error', 'Invalid response from LinkedIn.');
            }

            Log::info('LinkedIn callback received code', ['request' => $request->all(), 'code_length' => strlen($code)]);

            $tokenResponse = LinkedInAccount::getAccessToken($code, $platform);
            Log::info('LinkedIn token response received', ['tokenResponse' => $tokenResponse->json()]);

            $accessToken = $tokenResponse->json('access_token');
            $userResponse = LinkedInAccount::getAccount($accessToken, $platform);
            Log::info('LinkedIn user response received', ['userResponse' => $userResponse->json()]);

            LinkedInAccount::saveLdAccount(
                $userResponse->json(),
                'web',
                $platform,
                (string) AccountType::PROFILE->value,
                (string) ConnectionType::OFFICIAL->value,
                $accessToken,
                $tokenResponse->json('expires_in'),
                null
            );

            return redirect('/')->with('success', 'LinkedIn account connected successfully!');
        } catch (Exception $e) {
            Log::error('LinkedIn callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'An error occurred while connecting the account. Please try again.');
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
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);

                $fileModel = new File([
                    'path' => 'images/' . $filename,
                ]);
                $post->setRelation('file', collect([$fileModel]));
            }

            $linkedinApi = new LinkedInAccount;
            $result = $linkedinApi->send($post);

            Log::info('LinkedIn post upload result', ['result' => $result]);

            if (isset($result['status']) && $result['status'] === true) {
                return back()->with('success', $result['response'] ?? 'Posted successfully!')->with('url', $result['url'] ?? null);
            } else {
                Log::error('LinkedIn post upload failed at provider', ['result' => $result, 'post_content' => $request->input('content')]);

                return back()->with('error', 'Upload failed: ' . ($result['response'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            Log::error('LinkedIn post upload exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('image'),
                'line_number' => $e->getLine(),
                'file_path' => $e->getFile(),
            ]);

            return back()->with('error', 'An unexpected error occurred during upload.');
        }
    }
}
