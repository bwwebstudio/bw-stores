<?php

/**
 * Migration: Create plans table
 */

return [
    'up' => "
        CREATE TABLE plans (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            currency VARCHAR(3) NOT NULL DEFAULT 'INR',
            billing_interval ENUM('monthly', 'yearly') NOT NULL DEFAULT 'monthly',
            description TEXT NULL,
            features JSON NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            gateway_plan_id VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_plans_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS plans",
];
