-- BW Store SaaS - Complete Production Database Schema
-- Ready for Import into MySQL / phpMyAdmin (InfinityFree, cPanel, VPS)

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `mobile` VARCHAR(20) NULL,
    `role` ENUM('admin', 'merchant') NOT NULL DEFAULT 'merchant',
    `email_verified_at` TIMESTAMP NULL,
    `email_verify_token` VARCHAR(64) NULL,
    `password_reset_token` VARCHAR(64) NULL,
    `password_reset_expires_at` TIMESTAMP NULL,
    `failed_logins` INT NOT NULL DEFAULT 0,
    `locked_until` TIMESTAMP NULL,
    `last_login_at` TIMESTAMP NULL,
    `last_login_ip` VARCHAR(45) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Merchants
CREATE TABLE IF NOT EXISTS `merchants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `business_name` VARCHAR(255) NULL,
    `business_category` VARCHAR(100) NULL,
    `onboarding_step` INT NOT NULL DEFAULT 1,
    `onboarding_completed` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('active', 'pending', 'suspended') NOT NULL DEFAULT 'pending',
    `suspension_reason` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Stores
CREATE TABLE IF NOT EXISTS `stores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `merchant_id` INT NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `logo` VARCHAR(500) NULL,
    `favicon` VARCHAR(500) NULL,
    `description` TEXT NULL,
    `status` ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Plans
CREATE TABLE IF NOT EXISTS `plans` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 999.00,
    `yearly_price` DECIMAL(10,2) NULL,
    `yearly_discount` DECIMAL(10,2) NULL DEFAULT 0.00,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'INR',
    `billing_interval` ENUM('monthly', 'yearly') NOT NULL DEFAULT 'monthly',
    `badge` VARCHAR(50) NULL,
    `description` TEXT NULL,
    `features` LONGTEXT NULL,
    `max_products` INT NOT NULL DEFAULT 0,
    `max_themes` INT NOT NULL DEFAULT 3,
    `priority_support` TINYINT(1) NOT NULL DEFAULT 0,
    `trial_days` INT NOT NULL DEFAULT 7,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Subscriptions
CREATE TABLE IF NOT EXISTS `subscriptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `merchant_id` INT NOT NULL,
    `plan_id` INT NOT NULL,
    `status` ENUM('active', 'past_due', 'canceled', 'trialing') NOT NULL DEFAULT 'active',
    `current_period_start` TIMESTAMP NOT NULL,
    `current_period_end` TIMESTAMP NOT NULL,
    `expires_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`plan_id`) REFERENCES `plans`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Subscription Payments
CREATE TABLE IF NOT EXISTS `subscription_payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `subscription_id` INT NOT NULL,
    `merchant_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'INR',
    `payment_method` VARCHAR(50) NOT NULL DEFAULT 'RAZORPAY',
    `status` ENUM('pending', 'pending_verification', 'paid', 'failed', 'refunded', 'rejected') NOT NULL DEFAULT 'pending',
    `gateway_payment_id` VARCHAR(255) NULL,
    `transaction_ref` VARCHAR(100) NULL,
    `paid_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Categories
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `merchant_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `image` VARCHAR(500) NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `merchant_category_slug` (`merchant_id`, `slug`),
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Products
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `merchant_id` INT NOT NULL,
    `category_id` INT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `sku` VARCHAR(100) NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `compare_price` DECIMAL(10,2) NULL,
    `stock` INT NOT NULL DEFAULT 0,
    `low_stock_limit` INT NOT NULL DEFAULT 5,
    `brand` VARCHAR(100) NULL,
    `short_description` TEXT NULL,
    `description` LONGTEXT NULL,
    `images` LONGTEXT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'published',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `merchant_product_slug` (`merchant_id`, `slug`),
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Product Variants
CREATE TABLE IF NOT EXISTS `product_variants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `merchant_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `sku` VARCHAR(100) NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `stock` INT NOT NULL DEFAULT 0,
    `options` LONGTEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Inventory Transactions
CREATE TABLE IF NOT EXISTS `inventory_transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `merchant_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `variant_id` INT NULL,
    `type` ENUM('order_placed', 'manual_adjustment', 'addition', 'deduction', 'refund') NOT NULL,
    `quantity` INT NOT NULL,
    `previous_stock` INT NOT NULL,
    `new_stock` INT NOT NULL,
    `notes` VARCHAR(255) NULL,
    `reference_id` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Customers
CREATE TABLE IF NOT EXISTS `customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `merchant_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `mobile` VARCHAR(20) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `merchant_customer_email` (`merchant_id`, `email`),
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Orders
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `merchant_id` INT NOT NULL,
    `store_id` INT NOT NULL,
    `customer_id` INT NULL,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `customer_name` VARCHAR(255) NOT NULL,
    `customer_email` VARCHAR(255) NOT NULL,
    `customer_mobile` VARCHAR(20) NOT NULL,
    `shipping_address` TEXT NOT NULL,
    `shipping_city` VARCHAR(100) NOT NULL,
    `shipping_state` VARCHAR(100) NOT NULL,
    `shipping_postal_code` VARCHAR(20) NOT NULL,
    `notes` TEXT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `discount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `coupon_code` VARCHAR(50) NULL,
    `shipping` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10,2) NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL DEFAULT 'COD',
    `payment_status` ENUM('PENDING', 'PAID', 'FAILED', 'REFUNDED') NOT NULL DEFAULT 'PENDING',
    `order_status` ENUM('PENDING', 'CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'CANCELLED', 'RETURNED', 'REFUNDED') NOT NULL DEFAULT 'PENDING',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Order Items
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `variant_id` INT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `sku` VARCHAR(100) NULL,
    `variant_name` VARCHAR(255) NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `total` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Coupons
CREATE TABLE IF NOT EXISTS `coupons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `merchant_id` INT NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `type` ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
    `value` DECIMAL(10,2) NOT NULL,
    `min_order` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `max_discount` DECIMAL(10,2) NULL,
    `usage_limit` INT NULL,
    `usage_count` INT NOT NULL DEFAULT 0,
    `expires_at` DATE NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `merchant_coupon_code` (`merchant_id`, `code`),
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Store Settings
CREATE TABLE IF NOT EXISTS `store_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `store_id` INT NOT NULL UNIQUE,
    `merchant_id` INT NOT NULL,
    `theme_name` VARCHAR(50) NOT NULL DEFAULT 'modern',
    `primary_color` VARCHAR(10) NOT NULL DEFAULT '#2563EB',
    `secondary_color` VARCHAR(10) NOT NULL DEFAULT '#0F172A',
    `hero_title` VARCHAR(255) NULL,
    `hero_subtitle` TEXT NULL,
    `hero_image` VARCHAR(500) NULL,
    `whatsapp_number` VARCHAR(20) NULL,
    `contact_email` VARCHAR(255) NULL,
    `business_address` TEXT NULL,
    `footer_text` VARCHAR(255) NULL,
    `instagram_url` VARCHAR(255) NULL,
    `facebook_url` VARCHAR(255) NULL,
    `razorpay_key_id` VARCHAR(255) NULL,
    `razorpay_key_secret` VARCHAR(255) NULL,
    `razorpay_connected` TINYINT(1) NOT NULL DEFAULT 0,
    `merchant_upi_id` VARCHAR(100) NULL,
    `merchant_upi_name` VARCHAR(150) NULL,
    `upi_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `cod_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Support Tickets
CREATE TABLE IF NOT EXISTS `support_tickets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `merchant_id` INT NOT NULL,
    `ticket_number` VARCHAR(50) NOT NULL UNIQUE,
    `subject` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) NOT NULL DEFAULT 'General',
    `priority` ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    `status` ENUM('OPEN', 'IN_PROGRESS', 'WAITING', 'RESOLVED', 'CLOSED') NOT NULL DEFAULT 'OPEN',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Support Messages
CREATE TABLE IF NOT EXISTS `support_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `sender_type` ENUM('admin', 'merchant') NOT NULL DEFAULT 'merchant',
    `message` TEXT NOT NULL,
    `attachments` LONGTEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. Announcements
CREATE TABLE IF NOT EXISTS `announcements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info', 'warning', 'success', 'danger') NOT NULL DEFAULT 'info',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. Admin Settings
CREATE TABLE IF NOT EXISTS `admin_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` LONGTEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. Notifications
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `merchant_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` VARCHAR(50) NOT NULL DEFAULT 'system',
    `link` VARCHAR(500) NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`merchant_id`) REFERENCES `merchants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Seed 3 Tier Plans (Starter ₹499/₹5888, Growth ₹999/₹11788, Enterprise ₹2,999/₹34988)
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

-- Seed Master Admin Account (admin@bwwebstudio.com / Admin@BWStore2026)
INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `mobile`, `role`, `email_verified_at`, `is_active`)
VALUES (1, 'admin@bwwebstudio.com', '$2y$12$N7xkWxV8rS2qYJmY1Qk1iOlD0nO3.Q7m4U4F9P2jV1L0K3r9Q1X2i', 'BW Web Studio Admin', '+91 99999 99999', 'admin', NOW(), 1)
ON DUPLICATE KEY UPDATE `email` = 'admin@bwwebstudio.com';

-- Seed Admin Settings
INSERT INTO `admin_settings` (`setting_key`, `setting_value`) VALUES
('platform_name', 'BW Store'),
('company_name', 'BW Web Studio'),
('platform_tagline', 'Your Store. Your Brand. Your Sales.'),
('subscription_price', '999.00'),
('support_email', 'support@bwwebstudio.com'),
('admin_notify_email', 'admin@bwwebstudio.com')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);
