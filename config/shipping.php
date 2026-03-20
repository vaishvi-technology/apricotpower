<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shipping Provider
    |--------------------------------------------------------------------------
    |
    | The shipping provider to use for rate calculations and order submission.
    | Currently only 'shipstation' is supported.
    |
    */
    'provider' => env('SHIPPING_PROVIDER', 'shipstation'),

    /*
    |--------------------------------------------------------------------------
    | ShipStation Configuration
    |--------------------------------------------------------------------------
    |
    | API credentials and settings for ShipStation integration.
    | Get your API keys from ShipStation Settings > Account > API Settings.
    |
    */
    'shipstation' => [
        'api_key' => env('SHIPSTATION_API_KEY'),
        'api_secret' => env('SHIPSTATION_API_SECRET'),
        'base_url' => env('SHIPSTATION_BASE_URL', 'https://ssapi.shipstation.com'),
        'webhook_secret' => env('SHIPSTATION_WEBHOOK_SECRET'),
        'timeout' => env('SHIPSTATION_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Origin Address (Warehouse)
    |--------------------------------------------------------------------------
    |
    | The origin address used for shipping rate calculations.
    | This should be your warehouse or fulfillment center address.
    |
    */
    'origin' => [
        'postal_code' => env('SHIPPING_ORIGIN_ZIP', '78676'),
        'city' => env('SHIPPING_ORIGIN_CITY', 'Wimberley'),
        'state' => env('SHIPPING_ORIGIN_STATE', 'TX'),
        'country' => env('SHIPPING_ORIGIN_COUNTRY', 'US'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Carriers
    |--------------------------------------------------------------------------
    |
    | List of carrier codes to fetch rates from. Leave empty to get all.
    | Common codes: 'ups', 'usps', 'fedex', 'stamps_com'
    |
    */
    'carriers' => [
        'ups',
        'stamps_com', // USPS via Stamps.com
    ],

    /*
    |--------------------------------------------------------------------------
    | Weight Limits
    |--------------------------------------------------------------------------
    |
    | Maximum weight limits for domestic and international shipments (in ounces).
    |
    */
    'weight_limits' => [
        'domestic' => (int) env('SHIPPING_WEIGHT_LIMIT_DOMESTIC', 2400), // 150 lbs in ounces
        'international' => (int) env('SHIPPING_WEIGHT_LIMIT_INTERNATIONAL', 960), // 60 lbs in ounces
    ],

    /*
    |--------------------------------------------------------------------------
    | Free Shipping Thresholds
    |--------------------------------------------------------------------------
    |
    | Weight thresholds for free shipping eligibility (in ounces).
    |
    */
    'free_shipping' => [
        'enabled' => env('SHIPPING_FREE_ENABLED', true),
        'consumer_weight_limit' => (int) env('SHIPPING_FREE_WEIGHT_LIMIT', 1040), // 65 lbs in ounces
        'retailer_weight_limit' => (int) env('SHIPPING_FREE_WEIGHT_LIMIT_RETAILER', 1120), // 70 lbs in ounces
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | TTL (time-to-live) for cached data in seconds.
    |
    */
    'cache' => [
        'rates_ttl' => (int) env('SHIPPING_CACHE_RATES_TTL', 900), // 15 minutes
        'carriers_ttl' => (int) env('SHIPPING_CACHE_CARRIERS_TTL', 86400), // 24 hours
        'services_ttl' => (int) env('SHIPPING_CACHE_SERVICES_TTL', 86400), // 24 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Package Dimensions
    |--------------------------------------------------------------------------
    |
    | Default package dimensions if not specified (in inches).
    |
    */
    'default_dimensions' => [
        'length' => (int) env('SHIPPING_DEFAULT_LENGTH', 12),
        'width' => (int) env('SHIPPING_DEFAULT_WIDTH', 12),
        'height' => (int) env('SHIPPING_DEFAULT_HEIGHT', 12),
    ],

    /*
    |--------------------------------------------------------------------------
    | Confirmation Options
    |--------------------------------------------------------------------------
    |
    | Default shipping confirmation type.
    | Options: 'none', 'delivery', 'signature', 'adult_signature'
    |
    */
    'confirmation' => env('SHIPPING_CONFIRMATION', 'delivery'),

    /*
    |--------------------------------------------------------------------------
    | Residential Address Default
    |--------------------------------------------------------------------------
    |
    | Whether to default addresses as residential for rate calculations.
    |
    */
    'residential_default' => env('SHIPPING_RESIDENTIAL_DEFAULT', true),
];
