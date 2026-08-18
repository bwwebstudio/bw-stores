<?php

/**
 * Migration: 012_add_merchant_upi_to_store_settings
 */
return [
    'up' => "
        ALTER TABLE `store_settings` 
        ADD COLUMN `merchant_upi_id` VARCHAR(100) NULL AFTER `razorpay_connected`,
        ADD COLUMN `merchant_upi_name` VARCHAR(150) NULL AFTER `merchant_upi_id`,
        ADD COLUMN `upi_enabled` TINYINT(1) NOT NULL DEFAULT 1 AFTER `merchant_upi_name`
    ",
    'down' => "
        ALTER TABLE `store_settings`
        DROP COLUMN `merchant_upi_id`,
        DROP COLUMN `merchant_upi_name`,
        DROP COLUMN `upi_enabled`
    ",
];
