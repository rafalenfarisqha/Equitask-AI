<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more.
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

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'dify' => [
        'base_url' => env(
            'DIFY_BASE_URL',
            'https://api.dify.ai/v1'
        ),

        'api_key' => env('DIFY_API_KEY'),

        'file_variable' => env(
            'DIFY_FILE_VARIABLE',
            'materi_ajar'
        ),

        // 60 menit = 3600 detik
        'timeout' => (int) env('DIFY_TIMEOUT', 3600),
    ],

];
