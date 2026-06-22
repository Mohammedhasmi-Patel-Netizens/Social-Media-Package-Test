<?php

return [
    'youtube' => [
        'client_id' => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'graph_api_url' => env('FACEBOOK_GRAPH_API_URL', 'https://graph.facebook.com'),
        'app_version' => env('FACEBOOK_APP_VERSION', 'v20.0'),
    ],
];