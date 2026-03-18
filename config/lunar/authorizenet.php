<?php

return [
    // API Credentials
    'api_login_id' => env('AUTHORIZENET_API_LOGIN_ID'),
    'transaction_key' => env('AUTHORIZENET_TRANSACTION_KEY'),
    'public_client_key' => env('AUTHORIZENET_PUBLIC_CLIENT_KEY'),

    // Environment: 'sandbox' or 'production'
    'environment' => env('AUTHORIZENET_ENV', 'sandbox'),

    // Webhook Configuration
    'webhook_path' => 'authorizenet/webhook',
    'webhook_signature_key' => env('AUTHORIZENET_WEBHOOK_SIGNATURE_KEY'),

    // Transaction Policy: 'auth_capture' or 'auth_only'
    'transaction_policy' => env('AUTHORIZENET_POLICY', 'auth_capture'),

    // Accept.js URLs
    'accept_js' => [
        'sandbox_url' => 'https://jstest.authorize.net/v1/Accept.js',
        'production_url' => 'https://js.authorize.net/v1/Accept.js',
    ],

    // Response Code Mapping
    'response_codes' => [
        1 => 'approved',
        2 => 'declined',
        3 => 'error',
        4 => 'held_for_review',
    ],

    // Logging
    'logging' => [
        'enabled' => env('AUTHORIZENET_LOGGING', true),
        'channel' => env('AUTHORIZENET_LOG_CHANNEL', 'stack'),
    ],
];
