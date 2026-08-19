<?php

/**
 * Database Configuration
 * 
 * Supports standard environment variables and all Railway MySQL variables
 * (MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD, MYSQL_URL,
 *  MYSQL_PRIVATE_URL, MYSQL_PUBLIC_URL, DATABASE_URL).
 */

$host = env('DB_HOST', env('MYSQLHOST'));
$port = env('DB_PORT', env('MYSQLPORT'));
$name = env('DB_NAME', env('MYSQLDATABASE'));
$user = env('DB_USER', env('MYSQLUSER'));
$password = env('DB_PASSWORD', env('MYSQLPASSWORD'));

// If individual vars were not present, extract from URL string
if (empty($host) || empty($user) || empty($name)) {
    $dbUrl = env('MYSQL_URL', env('MYSQL_PRIVATE_URL', env('MYSQL_PUBLIC_URL', env('DATABASE_URL', env('DATABASE_PUBLIC_URL', env('DATABASE_PRIVATE_URL', env('DB_URL')))))));
    if (!empty($dbUrl)) {
        if (preg_match('|^mysqls?://([^:]+):(.*)@([^:/]+)(?::(\d+))?/(.+)$|', $dbUrl, $matches)) {
            $user = urldecode($matches[1]);
            $password = urldecode($matches[2]);
            $host = $matches[3];
            $port = !empty($matches[4]) ? $matches[4] : ($port ?: '3306');
            $name = explode('?', $matches[5])[0];
        } else {
            $parsedUrl = parse_url($dbUrl);
            if (!empty($parsedUrl['host'])) $host = $parsedUrl['host'];
            if (!empty($parsedUrl['port'])) $port = (string) $parsedUrl['port'];
            if (!empty($parsedUrl['path'])) $name = ltrim(explode('?', $parsedUrl['path'])[0], '/');
            if (!empty($parsedUrl['user'])) $user = urldecode($parsedUrl['user']);
            if (isset($parsedUrl['pass'])) $password = urldecode($parsedUrl['pass']);
        }
    }
}

// Fallbacks for local development
$host = !empty($host) ? $host : '127.0.0.1';
$port = !empty($port) ? (string)$port : '3306';
$name = !empty($name) ? $name : 'bw_store';
$user = !empty($user) ? $user : 'root';
$password = $password !== null ? $password : '';

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

