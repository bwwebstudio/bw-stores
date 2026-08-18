<?php

/**
 * Migration: Create users table
 */

return [
    'up' => "
        CREATE TABLE users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            name VARCHAR(100) NOT NULL,
            mobile VARCHAR(20) NULL,
            role ENUM('merchant', 'admin') NOT NULL DEFAULT 'merchant',
            email_verified_at DATETIME NULL,
            verification_token VARCHAR(64) NULL,
            reset_token VARCHAR(64) NULL,
            reset_token_expires DATETIME NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            login_attempts INT NOT NULL DEFAULT 0,
            locked_until DATETIME NULL,
            last_login_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_users_email (email),
            INDEX idx_users_verification_token (verification_token),
            INDEX idx_users_reset_token (reset_token),
            INDEX idx_users_role (role)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS users",
];
