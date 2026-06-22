<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MediaPlatform;

class MediaPlatformSeeder extends Seeder
{

    public function run(): void
    {
        MediaPlatform::updateOrCreate(
            ['slug' => 'youtube'],
            [
                'name' => 'YouTube',
                'configuration' => [
                    'client_id' => config('platforms.youtube.client_id'),
                    'client_secret' => config('platforms.youtube.client_secret'),
                ]
            ]
        );

        // 2. Facebook
        MediaPlatform::updateOrCreate(
            ['slug' => 'facebook'],
            [
                'name' => 'Facebook',
                'configuration' => [
                    'client_id' => config('platforms.facebook.client_id'),
                    'client_secret' => config('platforms.facebook.client_secret'),
                    'graph_api_url' => config('platforms.facebook.graph_api_url'),
                    'app_version' => config('platforms.facebook.app_version'),
                ]
            ]
        );
    }
}
