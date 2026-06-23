<?php

namespace App\Http\Controllers;

use App\Models\Core\File;
use App\Models\MediaPlatform;
use BeePost\SocialPoster\Enums\AccountType;
use BeePost\SocialPoster\Enums\ConnectionType;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use BeePost\SocialPoster\Services\Account\twitter\Account as TwitterAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TwitterController extends Controller
{
    public function redirectToTwitter()
    {
        try {
            $platform = MediaPlatform::where('slug', 'twitter')->firstOrFail();
            $url = TwitterAccount::authRedirect($platform);

            return redirect()->away($url);

        } catch (Exception $e) {
            Log::error('Twitter auth redirect failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Error connecting account. Please try again later.');
        }
    }

    public function handleTwitterCallback(Request $request)
    {
        if ($request->has('error')) {
            Log::warning('Twitter auth error returned from provider', [
                'error' => $request->input('error'),
                'error_description' => $request->input('error_description'),
                'error_reason' => $request->input('error_reason'),
            ]);

            return redirect('/')->with('error', 'Twitter authentication was cancelled or failed.');
        }

        try {
            $platform = MediaPlatform::where('slug', 'twitter')->firstOrFail();
            $code = $request->input('code');
            if (! $code) {
                Log::error('Twitter callback missing authorization code', ['request_all' => $request->all()]);

                return redirect('/')->with('error', 'Invalid response from Twitter.');
            }

            Log::info('Twitter callback received code', ['request' => $request->all(), 'code_length' => strlen($code)]);

            $tokenResponse = TwitterAccount::getAccessToken($code, $platform);
            Log::info('Twitter token response received', ['tokenResponse' => $tokenResponse->json()]);

            TwitterAccount::saveTwAccount(
                $tokenResponse->json(),
                'web',
                $platform,
                (string) AccountType::PROFILE->value,
                (string) ConnectionType::OFFICIAL->value
            );

            return redirect('/')->with('success', 'Twitter account connected successfully!');
        } catch (Exception $e) {
            Log::error('Twitter callback processing failed', [

                'exception_line' => $e->getLine(),
                'exception_file' => $e->getFile(),
                'exception_message' => $e->getMessage(),

            ]);

            return redirect('/')->with('error', 'An error occurred while connecting the account. Please try again.');
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
            $account = SocialAccount::where('platform_id', $platform->id)->firstOrFail();

            $post = new SocialPost([
                'content' => $request->input('content'),
                'post_type' => PostType::FEED->value,
                'account_id' => $account->id,
            ]);

            $post->setRelation('account', $account);

            $fileModels = collect([]);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('images'), $filename);

                $fileModel = new File([
                    'path' => 'images/'.$filename,
                ]);
                $fileModels->push($fileModel);
            }
            $post->setRelation('file', $fileModels);

            $twitterApi = new TwitterAccount;
            $result = $twitterApi->send($post);
            Log::info('Twitter post upload result', ['result' => $result]);

            if ($result['status'] === true) {
                return back()->with('success', $result['response'])->with('url', $result['url'] ?? null);
            } else {
                Log::error('Twitter post upload failed at provider', ['result' => $result, 'post_content' => $request->input('content')]);

                return back()->with('error', 'Upload failed: '.($result['response'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            Log::error('Twitter post upload exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('image'),
            ]);

            return redirect('/')->with('error', 'An unexpected error occurred during upload.');
        }
    }
}
