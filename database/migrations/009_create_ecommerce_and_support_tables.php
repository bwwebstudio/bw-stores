<?php

/**
 * Migration: Create all e-commerce, customer, store settings, support and promo tables
 */

return [
    'up' => "
        -- Categories
        CREATE TABLE IF NOT EXISTS categories (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            merchant_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            description TEXT NULL,
            image VARCHAR(500) NULL,
            sort_order INT NOT NULL DEFAULT 0,
            status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_categories_merchant (merchant_id),
            INDEX idx_categories_slug (slug),
            CONSTRAINT fk_categories_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Products
        CREATE TABLE IF NOT EXISTS products (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            merchant_id INT UNSIGNED NOT NULL,
            category_id INT UNSIGNED NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            sku VARCHAR(100) NULL,
            description LONGTEXT NULL,
            short_description TEXT NULL,
            price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            compare_price DECIMAL(10,2) NULL,
            stock INT NOT NULL DEFAULT 0,
            low_stock_limit INT NOT NULL DEFAULT 5,
            brand VARCHAR(100) NULL,
            weight DECIMAL(8,2) NULL,
            status ENUM('published', 'draft', 'archived') NOT NULL DEFAULT 'published',
            is_featured TINYINT(1) NOT NULL DEFAULT 0,
            images JSON NULL,
            seo_title VARCHAR(255) NULL,
            seo_description TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_products_merchant (merchant_id),
            INDEX idx_products_category (category_id),
            INDEX idx_products_slug (slug),
            INDEX idx_products_status (status),
            CONSTRAINT fk_products_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
            CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Product Variants
        CREATE TABLE IF NOT EXISTS product_variants (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id INT UNSIGNED NOT NULL,
            merchant_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            sku VARCHAR(100) NULL,
            price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            stock INT NOT NULL DEFAULT 0,
            options JSON NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_variants_product (product_id),
            INDEX idx_variants_merchant (merchant_id),
            CONSTRAINT fk_variants_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            CONSTRAINT fk_variants_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Inventory Transactions
        CREATE TABLE IF NOT EXISTS inventory_transactions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            merchant_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            variant_id INT UNSIGNED NULL,
            type ENUM('addition', 'deduction', 'adjustment', 'order_placed', 'order_cancelled') NOT NULL,
            quantity INT NOT NULL,
            previous_stock INT NOT NULL,
            new_stock INT NOT NULL,
            notes VARCHAR(255) NULL,
            reference_id VARCHAR(100) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_inv_merchant (merchant_id),
            INDEX idx_inv_product (product_id),
            CONSTRAINT fk_inv_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
            CONSTRAINT fk_inv_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Customers (Merchant scoped)
        CREATE TABLE IF NOT EXISTS customers (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            merchant_id INT UNSIGNED NOT NULL,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(255) NOT NULL,
            mobile VARCHAR(30) NULL,
            password_hash VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_customers_merchant (merchant_id),
            INDEX idx_customers_email (email),
            CONSTRAINT fk_customers_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Orders
        CREATE TABLE IF NOT EXISTS orders (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            merchant_id INT UNSIGNED NOT NULL,
            store_id INT UNSIGNED NOT NULL,
            customer_id INT UNSIGNED NULL,
            order_number VARCHAR(64) NOT NULL,
            customer_name VARCHAR(150) NOT NULL,
            customer_email VARCHAR(255) NOT NULL,
            customer_mobile VARCHAR(30) NOT NULL,
            shipping_address TEXT NOT NULL,
            shipping_city VARCHAR(100) NOT NULL,
            shipping_state VARCHAR(100) NOT NULL,
            shipping_postal_code VARCHAR(20) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            tax DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            shipping DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            coupon_code VARCHAR(50) NULL,
            order_status ENUM('PENDING', 'CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'CANCELLED', 'RETURNED', 'REFUNDED') NOT NULL DEFAULT 'PENDING',
            payment_status ENUM('PENDING', 'PAID', 'FAILED', 'REFUNDED') NOT NULL DEFAULT 'PENDING',
            payment_method ENUM('COD', 'ONLINE') NOT NULL DEFAULT 'COD',
            notes TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_orders_number (order_number),
            INDEX idx_orders_merchant (merchant_id),
            INDEX idx_orders_store (store_id),
            INDEX idx_orders_customer (customer_id),
            INDEX idx_orders_status (order_status),
            CONSTRAINT fk_orders_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
            CONSTRAINT fk_orders_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Order Items (Snapshot)
        CREATE TABLE IF NOT EXISTS order_items (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id INT UNSIGNED NOT NULL,
            merchant_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NULL,
            variant_id INT UNSIGNED NULL,
            product_name VARCHAR(255) NOT NULL,
            sku VARCHAR(100) NULL,
            variant_name VARCHAR(255) NULL,
            price DECIMAL(10,2) NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            total DECIMAL(10,2) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_order_items_order (order_id),
            INDEX idx_order_items_merchant (merchant_id),
            CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            CONSTRAINT fk_order_items_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Customer Payments
        CREATE TABLE IF NOT EXISTS payments (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            merchant_id INT UNSIGNED NOT NULL,
            order_id INT UNSIGNED NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            currency VARCHAR(3) NOT NULL DEFAULT 'INR',
            status ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
            gateway VARCHAR(50) NOT NULL DEFAULT 'COD',
            gateway_payment_id VARCHAR(255) NULL,
            gateway_response JSON NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_payments_order (order_id),
            INDEX idx_payments_merchant (merchant_id),
            CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            CONSTRAINT fk_payments_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Coupons
        CREATE TABLE IF NOT EXISTS coupons (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            merchant_id INT UNSIGNED NOT NULL,
            code VARCHAR(50) NOT NULL,
            type ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
            value DECIMAL(10,2) NOT NULL,
            min_order DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            max_discount DECIMAL(10,2) NULL,
            usage_limit INT NULL,
            usage_count INT NOT NULL DEFAULT 0,
            expires_at DATE NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_coupons_merchant (merchant_id),
            INDEX idx_coupons_code (code),
            CONSTRAINT fk_coupons_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Coupon Usages
        CREATE TABLE IF NOT EXISTS coupon_usages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            coupon_id INT UNSIGNED NOT NULL,
            order_id INT UNSIGNED NOT NULL,
            merchant_id INT UNSIGNED NOT NULL,
            customer_email VARCHAR(255) NOT NULL,
            discount_amount DECIMAL(10,2) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_coupon_usage_coupon (coupon_id),
            INDEX idx_coupon_usage_order (order_id),
            CONSTRAINT fk_coupon_usage_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
            CONSTRAINT fk_coupon_usage_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Store Settings & Customization
        CREATE TABLE IF NOT EXISTS store_settings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            store_id INT UNSIGNED NOT NULL,
            merchant_id INT UNSIGNED NOT NULL,
            logo VARCHAR(500) NULL,
            favicon VARCHAR(500) NULL,
            primary_color VARCHAR(20) NOT NULL DEFAULT '#2563EB',
            secondary_color VARCHAR(20) NOT NULL DEFAULT '#0F172A',
            hero_title VARCHAR(255) NULL,
            hero_subtitle TEXT NULL,
            hero_image VARCHAR(500) NULL,
            whatsapp_number VARCHAR(30) NULL,
            contact_email VARCHAR(255) NULL,
            contact_phone VARCHAR(30) NULL,
            business_address TEXT NULL,
            facebook_url VARCHAR(255) NULL,
            instagram_url VARCHAR(255) NULL,
            twitter_url VARCHAR(255) NULL,
            footer_text TEXT NULL,
            theme_name ENUM('modern', 'fashion', 'business') NOT NULL DEFAULT 'modern',
            razorpay_key_id VARCHAR(255) NULL,
            razorpay_key_secret VARCHAR(255) NULL,
            razorpay_connected TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_store_settings_store (store_id),
            INDEX idx_store_settings_merchant (merchant_id),
            CONSTRAINT fk_store_settings_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
            CONSTRAINT fk_store_settings_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Support Tickets
        CREATE TABLE IF NOT EXISTS support_tickets (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            merchant_id INT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NOT NULL,
            ticket_number VARCHAR(50) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL DEFAULT 'General',
            priority ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
            status ENUM('OPEN', 'IN_PROGRESS', 'WAITING', 'RESOLVED', 'CLOSED') NOT NULL DEFAULT 'OPEN',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_tickets_number (ticket_number),
            INDEX idx_tickets_merchant (merchant_id),
            INDEX idx_tickets_status (status),
            CONSTRAINT fk_tickets_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
            CONSTRAINT fk_tickets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Support Messages
        CREATE TABLE IF NOT EXISTS support_messages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ticket_id INT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NOT NULL,
            sender_type ENUM('merchant', 'admin') NOT NULL DEFAULT 'merchant',
            message LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_support_ticket (ticket_id),
            CONSTRAINT fk_support_ticket FOREIGN KEY (ticket_id) REFERENCES support_tickets(id) ON DELETE CASCADE,
            CONSTRAINT fk_support_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Admin Announcements
        CREATE TABLE IF NOT EXISTS announcements (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type ENUM('info', 'warning', 'success', 'danger') NOT NULL DEFAULT 'info',
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Notifications
        CREATE TABLE IF NOT EXISTS notifications (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NULL,
            merchant_id INT UNSIGNED NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(50) NOT NULL DEFAULT 'info',
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            link VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_notif_user (user_id),
            INDEX idx_notif_merchant (merchant_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ",
    'down' => "
        DROP TABLE IF EXISTS notifications;
        DROP TABLE IF EXISTS announcements;
        DROP TABLE IF EXISTS support_messages;
        DROP TABLE IF EXISTS support_tickets;
        DROP TABLE IF EXISTS store_settings;
        DROP TABLE IF EXISTS coupon_usages;
        DROP TABLE IF EXISTS coupons;
        DROP TABLE IF EXISTS payments;
        DROP TABLE IF EXISTS order_items;
        DROP TABLE IF EXISTS orders;
        DROP TABLE IF EXISTS customers;
        DROP TABLE IF EXISTS inventory_transactions;
        DROP TABLE IF EXISTS product_variants;
        DROP TABLE IF EXISTS products;
        DROP TABLE IF EXISTS categories;
    ",
];
