<?php

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo = new PDO(
    "mysql:host=" . ($_ENV['DB_HOST'] ?? '127.0.0.1') . ";port=" . ($_ENV['DB_PORT'] ?? 3306) . ";dbname=" . ($_ENV['DB_NAME'] ?? 'bw_store'),
    $_ENV['DB_USER'] ?? 'root',
    $_ENV['DB_PASSWORD'] ?? '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

$admins = $pdo->query("SELECT id, name, email, role, password_hash, is_active FROM users WHERE role = 'admin'")->fetchAll();
echo "Found " . count($admins) . " admin users:\n";
foreach ($admins as $a) {
    $matches = password_verify('Admin@BWStore2026', $a['password_hash']) ? 'YES' : 'NO';
    echo "ID: {$a['id']}, Name: {$a['name']}, Email: {$a['email']}, Active: {$a['is_active']}, Password matches Admin@BWStore2026: {$matches}\n";
}
