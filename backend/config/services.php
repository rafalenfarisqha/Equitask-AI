<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Konfigurasi layanan eksternal EquiTask AI.
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

    /*
    |--------------------------------------------------------------------------
    | Google Gemini
    |--------------------------------------------------------------------------
    */

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dify - Generate Asesmen
    |--------------------------------------------------------------------------
    |
    | Workflow untuk menghasilkan asesmen. Tetap terpisah dari Analytics.
    |
    */

    'dify' => [
        'base_url' => env(
            'DIFY_BASE_URL',
            'https://api.dify.ai/v1'
        ),
        'api_key' => env('DIFY_API_KEY'),
        'file_variable' => env('DIFY_FILE_VARIABLE', 'materi_ajar'),
        'timeout' => (int) env('DIFY_TIMEOUT', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dify - AI Analytics dan AI Essay Grading
    |--------------------------------------------------------------------------
    |
    | Workflow bersama untuk analitik siswa, analitik guru, dan penilaian esai.
    |
    | Input analitik : jenis_analisis, laporan_json
    | Input grading  : jenis_analisis, laporan_json, esai_json
    | Output analitik: analisis_json
    | Output grading : penilaian_esai_json
    |
    | DifyEssayGradingService menggunakan konfigurasi dify_analytics ini.
    | Nama input/output di atas harus sesuai dengan workflow Dify.
    |
    */

    'dify_analytics' => [
        'base_url' => env(
            'DIFY_ANALYTICS_BASE_URL',
            'https://api.dify.ai/v1'
        ),
        'api_key' => env('DIFY_ANALYTICS_API_KEY'),
        'timeout' => (int) env('DIFY_ANALYTICS_TIMEOUT', 90),
    ],

];
