<?php

/**
 * Database Configuration
 */

return [
    'host'     => $_ENV['DB_HOST'] ?? $_ENV['MYSQLHOST'] ?? '127.0.0.1',
    'port'     => $_ENV['DB_PORT'] ?? $_ENV['MYSQLPORT'] ?? '3306',
    'name'     => $_ENV['DB_NAME'] ?? $_ENV['MYSQLDATABASE'] ?? 'bw_store',
    'user'     => $_ENV['DB_USER'] ?? $_ENV['MYSQLUSER'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? $_ENV['MYSQLPASSWORD'] ?? '',
    'charset'  => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    
    // PDO options
    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ],
];
