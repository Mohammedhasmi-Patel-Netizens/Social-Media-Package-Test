<?php

namespace Database\Seeders;

use App\Models\MediaPlatform;
use Illuminate\Database\Seeder;

class MediaPlatformSeeder extends Seeder
{
    public function run(): void
    {
        // MediaPlatform::updateOrCreate(
        //     ['slug' => 'youtube'],
        //     [
        //         'name' => 'YouTube',
        //         'configuration' => [
        //             'client_id' => config('platforms.youtube.client_id'),
        //             'client_secret' => config('platforms.youtube.client_secret'),
        //         ]
        //     ]
        // );

        // 2. Facebook
        // MediaPlatform::updateOrCreate(
        //     ['slug' => 'facebook'],
        //     [
        //         'name' => 'Facebook',
        //         'configuration' => [
        //             'client_id' => config('platforms.facebook.client_id'),
        //             'client_secret' => config('platforms.facebook.client_secret'),
        //             'graph_api_url' => config('platforms.facebook.graph_api_url'),
        //             'app_version' => config('platforms.facebook.app_version'),
        //         ]
        //     ]
        // );

        // 3.linkedin
        // MediaPlatform::updateOrCreate(
        //     ['slug' => 'linkedin'],
        //     [
        //         'name' => 'LinkedIn',
        //         'configuration' => [
        //             'client_id' => config('platforms.linkedin.client_id'),
        //             'client_secret' => config('platforms.linkedin.client_secret'),
        //             'graph_api_url' => config('platforms.linkedin.graph_api_url'),
        //             'api_version' => config('platforms.linkedin.api_version', '202606'),
        //         ],
        //     ]
        // );

        // 4. Twitter (X)
        // MediaPlatform::updateOrCreate(
        //     ['slug' => 'twitter'],
        //     [
        //         'name' => 'Twitter',
        //         'configuration' => [
        //             'client_id' => config('platforms.twitter.client_id'),
        //             'client_secret' => config('platforms.twitter.client_secret'),
        //         ],
        //     ]
        // );

        // 5. Instagram
        MediaPlatform::updateOrCreate(
            ['slug' => 'instagram'],
            [
                'name' => 'Instagram',
                'configuration' => [
                    'client_id' => config('platforms.instagram.client_id'),
                    'client_secret' => config('platforms.instagram.client_secret'),
                    'graph_api_url' => config('platforms.instagram.graph_api_url'),
                    'app_version' => config('platforms.instagram.app_version', 'v20.0'),
                ],
            ]
        );
    }
}
