<?php

/**
 * Migration: Create subscription_payments table
 */

return [
    'up' => "
        CREATE TABLE subscription_payments (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            subscription_id INT UNSIGNED NOT NULL,
            merchant_id INT UNSIGNED NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            currency VARCHAR(3) NOT NULL DEFAULT 'INR',
            status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
            gateway_payment_id VARCHAR(255) NULL,
            gateway_response JSON NULL,
            paid_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_sub_payments_subscription (subscription_id),
            INDEX idx_sub_payments_merchant (merchant_id),
            INDEX idx_sub_payments_status (status),
            INDEX idx_sub_payments_gateway (gateway_payment_id),
            CONSTRAINT fk_sub_payments_subscription FOREIGN KEY (subscription_id) REFERENCES subscriptions(id),
            CONSTRAINT fk_sub_payments_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'down' => "DROP TABLE IF EXISTS subscription_payments",
];
