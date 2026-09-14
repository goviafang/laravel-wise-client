<?php

declare(strict_types=1);

return [

    // 'sandbox' hits wise-sandbox.com, 'production' hits wise.com
    'environment' => env('WISE_ENVIRONMENT', 'sandbox'),

    'servers' => [
        'sandbox' => 'https://api.wise-sandbox.com',
        'production' => 'https://api.wise.com',
    ],

    // Wise versions its API by quarter, bump this when they roll a new one
    'api_version' => env('WISE_API_VERSION', '2026Q3'),

    'auth' => [
        // one of personal_token / user_token / client_credentials, matches the Auth\ strategies
        'driver' => env('WISE_AUTH_DRIVER', 'personal_token'),

        'personal_token' => [
            'token' => env('WISE_PERSONAL_TOKEN'),
        ],

        'client_credentials' => [
            'client_id' => env('WISE_CLIENT_ID'),
            'client_secret' => env('WISE_CLIENT_SECRET'),
        ],

        'user_token' => [
            'client_id' => env('WISE_CLIENT_ID'),
            'client_secret' => env('WISE_CLIENT_SECRET'),
            'redirect_uri' => env('WISE_REDIRECT_URI'),
            // service container key for your TokenRepositoryInterface binding
            'token_repository' => env('WISE_TOKEN_REPOSITORY'),
        ],
    ],

    'webhook' => [
        // public key from Wise's subscription setup, used to verify X-Signature-SHA256
        'public_key' => env('WISE_WEBHOOK_PUBLIC_KEY'),
    ],

    'http' => [
        'timeout' => (int) env('WISE_HTTP_TIMEOUT', 30),

        // auto-retry on 429 using Retry-After; keep off for write endpoints
        'retry' => [
            'enabled' => (bool) env('WISE_RETRY_ON_RATE_LIMIT', false),
            'max_attempts' => (int) env('WISE_RETRY_MAX_ATTEMPTS', 3),
        ],
    ],

];
