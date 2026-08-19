<?php

/**
 * Database Configuration
 * 
 * Supports standard environment variables and all Railway MySQL variables
 * (MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD, MYSQL_URL,
 *  MYSQL_PRIVATE_URL, MYSQL_PUBLIC_URL, DATABASE_URL).
 */

$host = env('DB_HOST', env('MYSQLHOST', '127.0.0.1'));
$port = env('DB_PORT', env('MYSQLPORT', '3306'));
$name = env('DB_NAME', env('MYSQLDATABASE', 'bw_store'));
$user = env('DB_USER', env('MYSQLUSER', 'root'));
$password = env('DB_PASSWORD', env('MYSQLPASSWORD', ''));

// Handle full connection URL (e.g., from Railway MYSQL_URL / MYSQL_PRIVATE_URL / MYSQL_PUBLIC_URL / DATABASE_URL)
$dbUrl = env('MYSQL_URL', env('MYSQL_PRIVATE_URL', env('MYSQL_PUBLIC_URL', env('DATABASE_URL'))));
if (!empty($dbUrl)) {
    $parsedUrl = parse_url($dbUrl);
    if (!empty($parsedUrl['host'])) $host = $parsedUrl['host'];
    if (!empty($parsedUrl['port'])) $port = (string) $parsedUrl['port'];
    if (!empty($parsedUrl['path'])) $name = ltrim($parsedUrl['path'], '/');
    if (!empty($parsedUrl['user'])) $user = urldecode($parsedUrl['user']);
    if (isset($parsedUrl['pass'])) $password = urldecode($parsedUrl['pass']);
}

return [
    'host'      => $host,
    'port'      => $port,
    'name'      => $name,
    'user'      => $user,
    'password'  => $password,
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    
    // PDO options
    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ],
];

