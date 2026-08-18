<?php

/**
 * Migration: Create merchants table
 */

return [
    'up' => "
        CREATE TABLE merchants (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            business_name VARCHAR(255) NULL,
            business_category VARCHAR(100) NULL,
            onboarding_completed TINYINT(1) NOT NULL DEFAULT 0,
            onboarding_step INT NOT NULL DEFAULT 0,
            status ENUM('active', 'suspended', 'pending') NOT NULL DEFAULT 'pending',
            suspension_reason TEXT NULL,
            suspended_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_merchants_user_id (user_id),
            INDEX idx_merchants_status (status),
            CONSTRAINT fk_merchants_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS merchants",
];
