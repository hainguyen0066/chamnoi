<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // API Kick & Tra cứu Người Chơi GameServer
    'game_kick' => [
        'url' => env('GAME_KICK_API_URL', 'http://103.206.216.8:8090/v2/kick.php'),
        'status_url' => env('GAME_KICK_STATUS_API_URL', 'http://103.206.216.8:8090/v2/kick_status.php'),
        'lookup_url' => env('GAME_LOOKUP_API_URL', 'http://103.206.216.8:8090/v2/lookup.php'),
        'key' => env('GAME_KICK_KEY', env('KICK_KEY', '')),
        'timeout' => (int) env('GAME_KICK_TIMEOUT', 30),
    ],

];


