<?php

/**
 * Clean Slate Script
 * 
 * Removes all demo merchants, stores, products, orders, and test data.
 * Preserves Master Admin, Default ₹999 SaaS Plan, and Platform Admin Settings.
 */

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

$config = require BASE_PATH . '/config/database.php';

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $config['host'], $config['port'], $config['name'], $config['charset']);
    $pdo = new PDO($dsn, $config['user'], $config['password'], $config['options']);

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Clean all customer and merchant demo data
    $pdo->exec("DELETE FROM `audit_logs` WHERE `user_id` != 1;");
    $pdo->exec("DELETE FROM `support_messages`;");
    $pdo->exec("DELETE FROM `support_tickets`;");
    $pdo->exec("DELETE FROM `notifications`;");
    $pdo->exec("DELETE FROM `coupon_usages`;");
    $pdo->exec("DELETE FROM `coupons`;");
    $pdo->exec("DELETE FROM `inventory_transactions`;");
    $pdo->exec("DELETE FROM `product_variants`;");
    $pdo->exec("DELETE FROM `products`;");
    $pdo->exec("DELETE FROM `categories`;");
    $pdo->exec("DELETE FROM `order_items`;");
    $pdo->exec("DELETE FROM `payments`;");
    $pdo->exec("DELETE FROM `orders`;");
    $pdo->exec("DELETE FROM `customers`;");
    $pdo->exec("DELETE FROM `store_settings`;");
    $pdo->exec("DELETE FROM `stores`;");
    $pdo->exec("DELETE FROM `subscription_payments`;");
    $pdo->exec("DELETE FROM `subscriptions`;");
    $pdo->exec("DELETE FROM `merchants`;");
    $pdo->exec("DELETE FROM `users` WHERE `role` != 'admin';");

    // Reset AUTO_INCREMENT on wiped tables
    $tables = [
        'merchants', 'stores', 'store_settings', 'categories', 'products',
        'product_variants', 'inventory_transactions', 'customers', 'orders',
        'order_items', 'payments', 'coupons', 'coupon_usages', 'support_tickets',
        'support_messages', 'notifications', 'subscription_payments', 'subscriptions'
    ];
    foreach ($tables as $tbl) {
        $pdo->exec("ALTER TABLE `{$tbl}` AUTO_INCREMENT = 1;");
    }

    // Ensure Master Admin exists (admin@bwwebstudio.com / Admin@BWStore2026)
    $adminPassHash = password_hash('Admin@BWStore2026', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("
        INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `mobile`, `role`, `email_verified_at`, `is_active`)
        VALUES (1, 'admin@bwwebstudio.com', ?, 'BW Web Studio Admin', '+91 99999 99999', 'admin', NOW(), 1)
        ON DUPLICATE KEY UPDATE `password_hash` = VALUES(`password_hash`);
    ");
    $stmt->execute([$adminPassHash]);

    // Ensure 3 Plans exist (Starter ₹499, Growth ₹999, Enterprise ₹1,999)
    $pdo->exec("
        INSERT INTO `plans` (`id`, `name`, `slug`, `price`, `currency`, `billing_interval`, `badge`, `description`, `features`, `max_products`, `max_themes`, `priority_support`, `is_active`)
        VALUES 
        (1, 'BW Store Starter', 'starter', 499.00, 'INR', 'monthly', 'Starter Plan', 
         'Ideal for new sellers & creators starting their online boutique journey with essential features.',
         '[\"Up to 10 Products\", \"Modern High-Speed Storefront Theme\", \"Standard Analytics & Insights\", \"Direct Merchant UPI + COD Ready\", \"Instant Order Management\", \"Free SSL & Subdomain Hosting\", \"0% Platform Sales Commission\"]',
         10, 1, 0, 1),
        (2, 'BW Store Growth', 'growth', 999.00, 'INR', 'monthly', 'Most Popular',
         'The complete powerhouse solution for ambitious brands scaling their revenue rapidly.',
         '[\"Unlimited Products & Categories\", \"All 3 Premium Storefront Themes\", \"Advanced Real-Time Sales Analytics\", \"Inventory Tracking & Low Stock Alerts\", \"Coupons & Dynamic Discount Engine\", \"Direct Razorpay Connect + UPI + COD\", \"Priority Email & Ticket Support\", \"0% Platform Sales Commission\"]',
         0, 3, 1, 1),
        (3, 'BW Store Enterprise', 'enterprise', 1999.00, 'INR', 'monthly', 'VIP Business',
         'Engineered for established stores & high-volume merchants demanding VIP performance.',
         '[\"Unlimited Products & Unlimited Traffic\", \"All Themes + Custom Branding Tweaks\", \"Custom Domain DNS Mapping Ready\", \"Advanced Customer & Cart Analytics\", \"Dedicated Priority VIP WhatsApp & Ticket Support\", \"All Payment Gateways + Instant Settlements\", \"Early Access to New SaaS Features\", \"0% Platform Sales Commission\"]',
         0, 3, 1, 1)
        ON DUPLICATE KEY UPDATE 
            `name` = VALUES(`name`),
            `price` = VALUES(`price`),
            `badge` = VALUES(`badge`),
            `description` = VALUES(`description`),
            `features` = VALUES(`features`),
            `max_products` = VALUES(`max_products`),
            `max_themes` = VALUES(`max_themes`),
            `priority_support` = VALUES(`priority_support`),
            `is_active` = 1;
    ");

    // Ensure Admin Settings exist
    $pdo->exec("
        INSERT INTO `admin_settings` (`setting_key`, `setting_value`) VALUES
        ('platform_name', 'BW Store'),
        ('company_name', 'BW Web Studio'),
        ('platform_tagline', 'Your Store. Your Brand. Your Sales.'),
        ('subscription_price', '999.00'),
        ('support_email', 'support@bwwebstudio.com'),
        ('admin_notify_email', 'admin@bwwebstudio.com'),
        ('admin_upi_id', 'bwwebstudio@okhdfcbank')
        ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);
    ");

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    echo "✓ Database wiped cleanly! All demo data removed.\n";
    echo "✓ Master Admin Ready: admin@bwwebstudio.com | Admin@BWStore2026\n";
    echo "✓ Ready for fresh merchant signup and live testing!\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
