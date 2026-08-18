<?php

/**
 * Mail Configuration
 */

return [
    'host'       => env('MAIL_HOST', ''),
    'port'       => (int) env('MAIL_PORT', 587),
    'username'   => env('MAIL_USERNAME', ''),
    'password'   => env('MAIL_PASSWORD', ''),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
        'name'    => env('MAIL_FROM_NAME', 'BW Store'),
    ],
];
