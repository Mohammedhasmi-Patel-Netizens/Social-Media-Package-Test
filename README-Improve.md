# The Definitive Step-by-Step Guide: Integrating BeePost Social Poster

Welcome to the complete integration guide for the **BeePost Social Poster** package. This guide is designed to take you from a fresh installation to successfully publishing posts on social networks, explaining every detail along the way so that anyone—regardless of experience level—can successfully use it.

---

## Step 1: Obtain API Credentials & Configure `.env`
To communicate with social networks, you must register your application on their respective developer portals (e.g., Google Cloud Console, Meta for Developers, TikTok for Developers).

When registering your app, you will receive a **Client ID** (or Key) and a **Client Secret**. You will also be asked to provide an **OAuth Redirect URI** (the URL the social network sends the user back to after they log in).

Add your credentials to your application's `.env` file:

```env
# Facebook / Instagram Credentials
FACEBOOK_CLIENT_ID="your-facebook-app-id"
FACEBOOK_CLIENT_SECRET="your-facebook-app-secret"

# Twitter (X) Credentials
TWITTER_CLIENT_ID="your-twitter-client-id"
TWITTER_CLIENT_SECRET="your-twitter-client-secret"

# YouTube Credentials
YOUTUBE_CLIENT_ID="your-youtube-client-id"
YOUTUBE_CLIENT_SECRET="your-youtube-client-secret"

# TikTok Credentials
TIKTOK_CLIENT_KEY="your-tiktok-client-key"
TIKTOK_CLIENT_SECRET="your-tiktok-client-secret"
```

> **Crucial Detail for Developer Consoles:**
> When registering your app on the social platforms, you **must** whitelist your Redirect URIs. If your local app is `http://localhost:8000`, your whitelisted redirect URIs should look exactly like this:
> - `http://localhost:8000/auth/youtube/callback`
> - `http://localhost:8000/auth/tiktok/callback`

---

## Step 2: Seed the Database with Your Configurations

**Why do we do this?** 
Unlike simple packages that read directly from `.env`, BeePost Social Poster is built for scale. It reads OAuth keys from the `configuration` JSON column inside your `media_platforms` database table. This allows for multi-tenant setups where different users might have their own API keys.

You must save your `.env` keys into the database. We will create a Seeder to automate this.

Run this command in your terminal:
```bash
php artisan make:seeder MediaPlatformSeeder
```

Open `database/seeders/MediaPlatformSeeder.php` and replace its contents with this highly detailed seeder:

```php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MediaPlatform;

class MediaPlatformSeeder extends Seeder
{
    public function run()
    {
        // 1. YouTube
        MediaPlatform::updateOrCreate(
            ['slug' => 'youtube'],
            [
                'name' => 'YouTube',
                'configuration' => [
                    'client_id' => env('YOUTUBE_CLIENT_ID'),
                    'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
                ]
            ]
        );

        // 2. Facebook
        MediaPlatform::updateOrCreate(
            ['slug' => 'facebook'],
            [
                'name' => 'Facebook',
                'configuration' => [
                    'client_id' => env('FACEBOOK_CLIENT_ID'),
                    'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
                ]
            ]
        );

        // 3. Twitter
        MediaPlatform::updateOrCreate(
            ['slug' => 'twitter'],
            [
                'name' => 'Twitter',
                'configuration' => [
                    'client_id' => env('TWITTER_CLIENT_ID'),
                    'client_secret' => env('TWITTER_CLIENT_SECRET'),
                ]
            ]
        );

        // 4. TikTok
        // WATCH OUT: TikTok's API specifically requires the key to be named 'client_key' instead of 'client_id'
        MediaPlatform::updateOrCreate(
            ['slug' => 'tiktok'],
            [
                'name' => 'TikTok',
                'configuration' => [
                    'client_key' => env('TIKTOK_CLIENT_KEY'),
                    'client_secret' => env('TIKTOK_CLIENT_SECRET'),
                ]
            ]
        );
    }
}
```

Run the seeder to populate the database:
```bash
php artisan db:seed --class=MediaPlatformSeeder
```

---

## Step 3: Add the `filePath()` Global Helper (Mandatory)

**Why is this needed?**
When you post an image or a video, the package must locate the file on your server. Because every Laravel application handles file uploads and database records differently, the package expects **you** to define a global `filePath()` helper function. If you skip this step, uploads will crash with a "Call to undefined function filePath()" error.

**How to add it correctly:**
1. Open your `composer.json` file and add a `files` array under `autoload`:
```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/helpers.php"
    ]
},
```

2. Create a new file at `app/helpers.php` and paste this code:
```php
<?php

if (! function_exists('filePath')) {
    /**
     * Resolve the exact file path for the Social Poster package.
     *
     * @param  mixed  $file    Your application's File model instance
     * @param  string|null  $context 
     * @return string
     */
    function filePath($file, $context = null)
    {
        // This function receives your database's File model.
        // You must return the relative path to the file. 
        // Example: If your File model has a 'path' column, return it like this:
        return $file->path; 
    }
}
```

3. Run this terminal command to reload Composer:
```bash
composer dump-autoload
```

---

## Step 4: The OAuth Flow (Connecting User Accounts)

To allow a user to connect their YouTube or TikTok account to your app, you need an OAuth flow. This consists of two routes:
1. **Redirect:** Sends the user to the platform's login page.
2. **Callback:** Catches the user when the platform sends them back, exchanges their authorization code for a secret Access Token, and saves it.

### A. Define the Routes
Open `routes/web.php` and add:
```php
use App\Http\Controllers\OAuthController;

// The {platform} parameter makes this dynamic (e.g. /auth/youtube/redirect or /auth/tiktok/redirect)
Route::get('/auth/{platform}/redirect', [OAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{platform}/callback', [OAuthController::class, 'callback'])->name('social.callback');
```

### B. Create the Controller
Create `app/Http/Controllers/OAuthController.php`:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MediaPlatform;
use Exception;

class OAuthController extends Controller
{
    /**
     * Resolves the correct package class based on the platform slug.
     */
    private function getPlatformClass($slug)
    {
        $classes = [
            'youtube' => \BeePost\SocialPoster\Services\Account\youtube\Account::class,
            'tiktok' => \BeePost\SocialPoster\Services\Account\tiktok\Account::class,
            'facebook' => \BeePost\SocialPoster\Services\Account\facebook\Account::class,
            'twitter' => \BeePost\SocialPoster\Services\Account\twitter\Account::class,
        ];

        if (!array_key_exists($slug, $classes)) {
            abort(404, "Platform not supported.");
        }

        return new $classes[$slug]();
    }

    public function redirect($platformSlug)
    {
        try {
            $platform = MediaPlatform::where('slug', $platformSlug)->firstOrFail();
            
            // Get the class (e.g., YouTubeAccount)
            $platformApi = $this->getPlatformClass($platformSlug);
            
            // Generate the secure OAuth URL and redirect the user
            $url = $platformApi::authRedirect($platform);
            
            return redirect()->away($url);
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Error initializing connection: ' . $e->getMessage());
        }
    }

    public function callback(Request $request, $platformSlug)
    {
        try {
            $platform = MediaPlatform::where('slug', $platformSlug)->firstOrFail();
            $platformApi = $this->getPlatformClass($platformSlug);
            
            // This exchanges the code for a token and saves the account securely to the database
            $response = $platformApi->connect($platform, $request->all());

            return redirect('/')->with('success', ucfirst($platformSlug) . ' account connected successfully!');
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Failed to connect: ' . $e->getMessage());
        }
    }
}
```

---

## Step 5: Publishing Your First Post

You have your keys, the helper is loaded, and the account is connected. You are ready to post! 

Here is a highly detailed controller method showing exactly how to upload a video to YouTube.

1. Add a route in `routes/web.php`:
```php
Route::post('/upload/{platform}', [App\Http\Controllers\PostController::class, 'upload'])->name('social.upload');
```

2. Create `app/Http/Controllers/PostController.php`:
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BeePost\SocialPoster\Models\SocialAccount;
use BeePost\SocialPoster\Models\SocialPost;
use BeePost\SocialPoster\Enums\PostType;
use App\Models\MediaPlatform;
use Exception;

class PostController extends Controller
{
    public function upload(Request $request, $platformSlug)
    {
        // Ensure you validate the request (omitted here for brevity)

        try {
            // 1. Resolve the platform and the connected account
            $platform = MediaPlatform::where('slug', $platformSlug)->firstOrFail();
            $account = SocialAccount::where('platform_id', $platform->id)->firstOrFail();

            // 2. Handle the File Upload
            // You must save the uploaded file to your server and create a File model record.
            $uploadedFile = $request->file('video');
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $uploadedFile->move(public_path('uploads'), $filename);

            // Create your application's File model record
            // (The `filePath` helper you created in Step 3 will read the 'path' from this model)
            $fileModel = new \App\Models\File([
                'path' => 'uploads/' . $filename,
            ]);
            $fileModel->save();

            // 3. Create the SocialPost Record
            $post = new SocialPost([
                'content' => $request->input('description', 'My Awesome Post!'),
                'post_type' => PostType::SHORTS->value, // Or PostType::POST->value depending on your needs
                'account_id' => $account->id,
            ]);

            // 4. Bind the Relations (Crucial Step!)
            // The package needs the account and file models attached to the post dynamically
            $post->setRelation('account', $account);
            $post->setRelation('file', collect([$fileModel])); 

            // 5. Fire it off to the social network!
            // We use the same dynamic class resolution method shown in Step 4
            $platformApiClass = "\\BeePost\\SocialPoster\\Services\\Account\\{$platformSlug}\\Account";
            $api = new $platformApiClass();
            
            $result = $api->send($post);

            // 6. Handle the Response
            if ($result['status'] === true) {
                return back()->with('success', 'Posted successfully! View here: ' . ($result['url'] ?? 'Processing...'));
            } else {
                return back()->with('error', 'API Error: ' . ($result['response'] ?? 'Unknown error occurred.'));
            }

        } catch (Exception $e) {
            return back()->with('error', 'System Error: ' . $e->getMessage());
        }
    }
}
```

### You're Done!
By following these 5 steps, you have securely configured your OAuth application, connected a user to a platform, and dispatched media successfully! You can easily extend the `OAuthController` and `PostController` to handle any other platform included in the BeePost package.
