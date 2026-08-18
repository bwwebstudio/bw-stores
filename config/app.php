<?php

/**
 * Application Configuration
 */

return [
    'name'     => $_ENV['APP_NAME'] ?? 'BW Store',
    'env'      => $_ENV['APP_ENV'] ?? 'production',
    'url'      => $_ENV['APP_URL'] ?? 'http://localhost',
    'debug'    => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Asia/Kolkata',
    'key'      => $_ENV['APP_KEY'] ?? '',
    
    // Brand
    'brand' => [
        'company' => 'BW Web Studio',
        'product' => 'BW Store',
        'tagline' => 'Your Store. Your Brand. Your Sales.',
    ],
];
