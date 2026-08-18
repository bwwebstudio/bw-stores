<?php

/**
 * Seeder: Admin User
 * 
 * Creates the initial admin account using credentials from .env
 * NEVER hard-code admin passwords.
 */

return function (PDO $pdo) {
    $email = $_ENV['ADMIN_EMAIL'] ?? 'admin@bwwebstudio.com';
    $password = $_ENV['ADMIN_PASSWORD'] ?? '';
    $name = $_ENV['ADMIN_NAME'] ?? 'BW Admin';

    if (empty($password)) {
        echo "(skipped - ADMIN_PASSWORD not set in .env) ";
        return;
    }

    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo "(already exists) ";
        return;
    }

    $stmt = $pdo->prepare("
        INSERT INTO users (email, password_hash, name, role, email_verified_at, is_active)
        VALUES (?, ?, ?, 'admin', NOW(), 1)
    ");

    $stmt->execute([
        $email,
        password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
        $name,
    ]);
};
