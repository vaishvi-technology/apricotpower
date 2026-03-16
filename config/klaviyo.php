<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Klaviyo API Credentials
    |--------------------------------------------------------------------------
    */

    'private_key' => env('KLAVIYO_PRIVATE_KEY'),
    'public_key' => env('KLAVIYO_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default List ID
    |--------------------------------------------------------------------------
    |
    | The Klaviyo list ID to subscribe customers to by default.
    |
    */

    'list_id' => env('KLAVIYO_LIST_ID'),

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Klaviyo Integration
    |--------------------------------------------------------------------------
    |
    | Set to false to disable all Klaviyo API calls (useful for local dev).
    |
    */

    'enabled' => env('KLAVIYO_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    */

    'base_url' => env('KLAVIYO_BASE_URL', 'https://a.klaviyo.com/api'),

    /*
    |--------------------------------------------------------------------------
    | API Revision
    |--------------------------------------------------------------------------
    |
    | Klaviyo API revision date. Update when upgrading API version.
    |
    */

    'revision' => env('KLAVIYO_API_REVISION', '2024-10-15'),

];
