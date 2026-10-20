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

    'dify' => [
    'base_url' => env('DIFY_BASE_URL', 'https://api.dify.ai/v1'),
    'api_key' => env('DIFY_API_KEY'),
    'file_variable' => env('DIFY_FILE_VARIABLE', 'materi_ajar'),
    'timeout' => (int) env('DIFY_TIMEOUT', 600),
    ],
];
