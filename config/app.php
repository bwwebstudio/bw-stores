<?php

/**
 * Application Configuration
 */

return [
    'name'     => env('APP_NAME', 'BW Store'),
    'env'      => env('APP_ENV', 'production'),
    'url'      => env('APP_URL', 'http://localhost'),
    'debug'    => (bool) env('APP_DEBUG', true),
    'timezone' => env('APP_TIMEZONE', 'Asia/Kolkata'),
    'key'      => env('APP_KEY', 'bw_store_secret_2026_key'),
    
    // Brand
    'brand' => [
        'company' => 'BW Web Studio',
        'product' => 'BW Store',
        'tagline' => 'Your Store. Your Brand. Your Sales.',
    ],
];
