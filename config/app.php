<?php

return [
    'name' => env('APP_NAME', 'VillaStudio'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'https://villahandle.com'),
    'key' => env('APP_KEY'),
    'timezone' => 'UTC',

    // Service configurations
    'services' => [
        'flutterwave' => [
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
        ],
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'mode' => env('PAYPAL_MODE', 'live'), // 'sandbox' or 'live'
        ],
    ],
];