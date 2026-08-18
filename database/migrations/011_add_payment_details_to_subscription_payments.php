<?php

/**
 * Migration: 011_add_payment_details_to_subscription_payments
 */
return [
    'up' => "
        ALTER TABLE `subscription_payments` 
        ADD COLUMN `payment_method` VARCHAR(50) NOT NULL DEFAULT 'RAZORPAY' AFTER `currency`,
        ADD COLUMN `transaction_ref` VARCHAR(100) NULL AFTER `gateway_payment_id`,
        MODIFY COLUMN `status` ENUM('pending', 'pending_verification', 'paid', 'failed', 'refunded', 'rejected') NOT NULL DEFAULT 'pending'
    ",
    'down' => "
        ALTER TABLE `subscription_payments`
        DROP COLUMN `payment_method`,
        DROP COLUMN `transaction_ref`
    ",
];
