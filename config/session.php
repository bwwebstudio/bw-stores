<?php

/**
 * Session Configuration
 */

return [
    'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120), // minutes
    'name'     => $_ENV['SESSION_NAME'] ?? 'bw_store_session',
    'secure'   => filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'domain'   => $_ENV['SESSION_DOMAIN'] ?? '',
    'path'     => '/',
    'httponly'  => true,
    'samesite'  => 'Lax',
];
