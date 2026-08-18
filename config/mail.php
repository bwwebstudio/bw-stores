<?php

/**
 * Mail Configuration
 */

return [
    'host'       => $_ENV['MAIL_HOST'] ?? '',
    'port'       => (int) ($_ENV['MAIL_PORT'] ?? 587),
    'username'   => $_ENV['MAIL_USERNAME'] ?? '',
    'password'   => $_ENV['MAIL_PASSWORD'] ?? '',
    'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
    'from' => [
        'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@example.com',
        'name'    => $_ENV['MAIL_FROM_NAME'] ?? 'BW Store',
    ],
];
