<?php

/**
 * Migration: Create stores table
 */

return [
    'up' => "
        CREATE TABLE stores (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            merchant_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            logo VARCHAR(500) NULL,
            favicon VARCHAR(500) NULL,
            description TEXT NULL,
            status ENUM('active', 'suspended', 'maintenance') NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_stores_merchant_id (merchant_id),
            UNIQUE KEY uk_stores_slug (slug),
            INDEX idx_stores_status (status),
            CONSTRAINT fk_stores_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS stores",
];
