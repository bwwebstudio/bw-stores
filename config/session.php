<?php

/**
 * Session Configuration
 */

return [
    'lifetime' => (int) env('SESSION_LIFETIME', 120), // minutes
    'name'     => env('SESSION_NAME', 'bw_store_session'),
    'secure'   => (bool) env('SESSION_SECURE', false),
    'domain'   => env('SESSION_DOMAIN', ''),
    'path'     => '/',
    'httponly'  => true,
    'samesite'  => 'Lax',
];
