<?php

/**
 * Migration: Add Tier Plans with Flat Yearly Discounts & 7-Day Trial Support
 */

return [
    'up' => "
        -- Add tier & billing columns to plans table if not already existing
        ALTER TABLE `plans`
            ADD COLUMN IF NOT EXISTS `badge` VARCHAR(50) NULL AFTER `billing_interval`,
            ADD COLUMN IF NOT EXISTS `description` TEXT NULL AFTER `badge`,
            ADD COLUMN IF NOT EXISTS `features` LONGTEXT NULL AFTER `description`,
            ADD COLUMN IF NOT EXISTS `yearly_price` DECIMAL(10,2) NULL AFTER `price`,
            ADD COLUMN IF NOT EXISTS `yearly_discount` DECIMAL(10,2) NULL DEFAULT 0.00 AFTER `yearly_price`,
            ADD COLUMN IF NOT EXISTS `max_products` INT NOT NULL DEFAULT 0 AFTER `features`,
            ADD COLUMN IF NOT EXISTS `max_themes` INT NOT NULL DEFAULT 3 AFTER `max_products`,
            ADD COLUMN IF NOT EXISTS `priority_support` TINYINT(1) NOT NULL DEFAULT 0 AFTER `max_themes`,
            ADD COLUMN IF NOT EXISTS `trial_days` INT NOT NULL DEFAULT 7 AFTER `priority_support`;

        -- Seed / Update 3 Tiers (Starter ₹499/₹5888, Growth ₹999/₹11788, Enterprise ₹2999/₹34988)
        INSERT INTO `plans` (`id`, `name`, `slug`, `price`, `yearly_price`, `yearly_discount`, `currency`, `billing_interval`, `badge`, `description`, `features`, `max_products`, `max_themes`, `priority_support`, `trial_days`, `is_active`)
        VALUES 
        (1, 'BW Store Starter', 'starter', 499.00, 5888.00, 100.00, 'INR', 'monthly', 'Starter', 
         'Ideal for new sellers & creators starting their online boutique journey with essential features.',
         '[\"Up to 10 Products\", \"Modern High-Speed Storefront Theme\", \"Standard Analytics & Insights\", \"Direct Merchant UPI + COD Ready\", \"Instant Order Management\", \"Free SSL & Subdomain Hosting\", \"0% Platform Sales Commission\"]',
         10, 1, 0, 7, 1),
        
        (2, 'BW Store Growth', 'growth', 999.00, 11788.00, 200.00, 'INR', 'monthly', 'Recommended',
         'The complete powerhouse solution for ambitious brands scaling their revenue rapidly.',
         '[\"Unlimited Products & Categories\", \"All 3 Premium Storefront Themes\", \"Advanced Real-Time Sales Analytics\", \"Inventory Tracking & Low Stock Alerts\", \"Coupons & Dynamic Discount Engine\", \"Direct Razorpay Connect + UPI + COD\", \"Priority Email & Ticket Support\", \"0% Platform Sales Commission\"]',
         0, 3, 1, 7, 1),

        (3, 'BW Store Enterprise', 'enterprise', 2999.00, 34988.00, 1000.00, 'INR', 'monthly', 'VIP Business',
         'Engineered for established stores & high-volume merchants demanding VIP performance.',
         '[\"Unlimited Products & Unlimited Traffic\", \"All Themes + Custom Branding & CSS\", \"Custom Domain DNS Mapping Ready\", \"Advanced Customer & Cart Analytics\", \"Dedicated Priority VIP 24/7 WhatsApp & Ticket Support\", \"All Payment Gateways + Instant Settlements\", \"Early Access to New SaaS Features\", \"0% Platform Sales Commission\"]',
         0, 3, 1, 7, 1)
        ON DUPLICATE KEY UPDATE 
            `name` = VALUES(`name`),
            `price` = VALUES(`price`),
            `yearly_price` = VALUES(`yearly_price`),
            `yearly_discount` = VALUES(`yearly_discount`),
            `badge` = VALUES(`badge`),
            `description` = VALUES(`description`),
            `features` = VALUES(`features`),
            `max_products` = VALUES(`max_products`),
            `max_themes` = VALUES(`max_themes`),
            `priority_support` = VALUES(`priority_support`),
            `trial_days` = VALUES(`trial_days`),
            `is_active` = 1;
    ",
    'down' => "
        -- Keep table intact
    ",
];
