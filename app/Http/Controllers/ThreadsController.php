<?php

namespace App\Http\Controllers;

use App\Models\Core\File;
use App\Models\MediaPlatform;
use BeePost\SocialPoster\Enums\AccountType;
use BeePost\SocialPoster\Enums\ConnectionType;
use BeePost\SocialPoster\Enums\PostType;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use BeePost\SocialPoster\Services\Account\threads\Account as ThreadsAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ThreadsController extends Controller
{
    public function redirectToThreads()
    {
        try {
            $platform = MediaPlatform::where('slug', 'threads')->firstOrFail();
            $url = ThreadsAccount::authRedirect($platform);

            return redirect()->away($url);
        } catch (Exception $e) {
            Log::error('Threads auth redirect failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Error connecting account. Please try again later.');
        }
    }

    public function handleThreadsCallback(Request $request)
    {
        if ($request->has('error')) {
            Log::warning('Threads auth error returned from provider', [
                'error' => $request->input('error'),
                'error_description' => $request->input('error_description'),
                'error_reason' => $request->input('error_reason'),
            ]);

            return redirect('/')->with('error', 'Threads authentication was cancelled or failed.');
        }

        try {
            $platform = MediaPlatform::where('slug', 'threads')->firstOrFail();
            $code = $request->input('code');
            if (! $code) {
                Log::error('Threads callback missing authorization code', ['request_all' => $request->all()]);

                return redirect('/')->with('error', 'Invalid response from Threads.');
            }

            Log::info('Threads callback received code', ['request' => $request->all(), 'code_length' => strlen($code)]);

            $tokenResponse = ThreadsAccount::getAccessToken($code, $platform);
            Log::info('Threads token response received', ['tokenResponse' => $tokenResponse]);

            $token = $tokenResponse['access_token'];
            $pages = ThreadsAccount::getAcccount($token, $platform);
            Log::info('Threads pages received', ['pages' => $pages]);

            ThreadsAccount::saveThAccount(
                $tokenResponse,
                'web',
                $platform,
                (string) AccountType::PROFILE->value,
                (string) ConnectionType::OFFICIAL->value,
                null
            );

            return redirect('/')->with('success', 'Threads account connected successfully!');
        } catch (Exception $e) {
            Log::error('Threads callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'An error occurred while connecting the account. Please try again.');
        }
    }

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
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('images'), $filename);

                $fileModel = new File([
                    'path' => 'images/'.$filename,
                ]);
                $post->setRelation('file', collect([$fileModel]));
            }

            $threadsApi = new ThreadsAccount;
            $result = $threadsApi->send($post);

            Log::info('Threads post upload result', ['result' => $result]);

            if (isset($result['status']) && $result['status'] === true) {
                return back()->with('success', $result['response'] ?? 'Posted successfully!')->with('url', $result['url'] ?? null);
            } else {
                Log::error('Threads post upload failed at provider', ['result' => $result, 'post_content' => $request->input('content')]);

                return back()->with('error', 'Upload failed: '.($result['response'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            Log::error('Threads post upload exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('image'),
            ]);

            return back()->with('error', 'An unexpected error occurred during upload.');
        }
    }
}
