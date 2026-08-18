<?php

/**
 * Migration: 010_add_cod_enabled_to_store_settings
 */
return [
    'up' => "ALTER TABLE `store_settings` ADD COLUMN `cod_enabled` TINYINT(1) NOT NULL DEFAULT 1 AFTER `razorpay_connected`",
    'down' => "ALTER TABLE `store_settings` DROP COLUMN `cod_enabled`",
];
