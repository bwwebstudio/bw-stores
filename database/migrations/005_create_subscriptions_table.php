<?php

/**
 * Migration: Create subscriptions table
 */

return [
    'up' => "
        CREATE TABLE subscriptions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            merchant_id INT UNSIGNED NOT NULL,
            plan_id INT UNSIGNED NOT NULL,
            status ENUM('pending', 'active', 'past_due', 'grace_period', 'cancelled', 'expired', 'suspended') NOT NULL DEFAULT 'pending',
            gateway_subscription_id VARCHAR(255) NULL,
            current_period_start DATETIME NULL,
            current_period_end DATETIME NULL,
            trial_ends_at DATETIME NULL,
            cancelled_at DATETIME NULL,
            expires_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_subscriptions_merchant (merchant_id),
            INDEX idx_subscriptions_status (status),
            INDEX idx_subscriptions_gateway (gateway_subscription_id),
            INDEX idx_subscriptions_period_end (current_period_end),
            CONSTRAINT fk_subscriptions_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
            CONSTRAINT fk_subscriptions_plan FOREIGN KEY (plan_id) REFERENCES plans(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS subscriptions",
];
