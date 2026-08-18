<?php

/**
 * Seeder: BW Store Tier Plans with Flat Yearly Pricing
 * 
 * Seeds 3 subscription plans:
 * - Starter: ₹499/mo | ₹5,888/yr (Save ₹100 Flat)
 * - Growth: ₹999/mo | ₹11,788/yr (Save ₹200 Flat)
 * - Enterprise: ₹2,999/mo | ₹34,988/yr (Save ₹1,000 Flat)
 */

return function (PDO $pdo) {
    $plans = [
        [
            'id' => 1,
            'name' => 'BW Store Starter',
            'slug' => 'starter',
            'price' => 499.00,
            'yearly_price' => 5888.00, // (499 * 12) - 100 = 5888
            'yearly_discount' => 100.00,
            'currency' => 'INR',
            'billing_interval' => 'monthly',
            'badge' => 'Starter',
            'description' => 'Ideal for new sellers & creators starting their online boutique journey with essential features.',
            'features' => json_encode([
                'Up to 10 Products',
                'Modern High-Speed Storefront Theme',
                'Standard Analytics & Insights',
                'Direct Merchant UPI + COD Ready',
                'Instant Order Management',
                'Free SSL & Subdomain Hosting',
                '0% Platform Sales Commission'
            ]),
            'max_products' => 10,
            'max_themes' => 1,
            'priority_support' => 0,
            'trial_days' => 7,
        ],
        [
            'id' => 2,
            'name' => 'BW Store Growth',
            'slug' => 'growth',
            'price' => 999.00,
            'yearly_price' => 11788.00, // (999 * 12) - 200 = 11788
            'yearly_discount' => 200.00,
            'currency' => 'INR',
            'billing_interval' => 'monthly',
            'badge' => 'Recommended',
            'description' => 'The complete powerhouse solution for ambitious brands scaling their revenue rapidly.',
            'features' => json_encode([
                'Unlimited Products & Categories',
                'All 3 Premium Storefront Themes',
                'Advanced Real-Time Sales Analytics',
                'Inventory Tracking & Low Stock Alerts',
                'Coupons & Dynamic Discount Engine',
                'Direct Razorpay Connect + UPI + COD',
                'Priority Email & Ticket Support',
                '0% Platform Sales Commission'
            ]),
            'max_products' => 0,
            'max_themes' => 3,
            'priority_support' => 1,
            'trial_days' => 7,
        ],
        [
            'id' => 3,
            'name' => 'BW Store Enterprise',
            'slug' => 'enterprise',
            'price' => 2999.00,
            'yearly_price' => 34988.00, // (2999 * 12) - 1000 = 34988
            'yearly_discount' => 1000.00,
            'currency' => 'INR',
            'billing_interval' => 'monthly',
            'badge' => 'VIP Business',
            'description' => 'Engineered for established stores & high-volume merchants demanding VIP performance.',
            'features' => json_encode([
                'Unlimited Products & Unlimited Traffic',
                'All Themes + Custom Branding & CSS',
                'Custom Domain DNS Mapping Ready',
                'Advanced Customer & Cart Analytics',
                'Dedicated Priority VIP 24/7 WhatsApp & Ticket Support',
                'All Payment Gateways + Instant Settlements',
                'Early Access to New SaaS Features',
                '0% Platform Sales Commission'
            ]),
            'max_products' => 0,
            'max_themes' => 3,
            'priority_support' => 1,
            'trial_days' => 7,
        ]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO plans (id, name, slug, price, yearly_price, yearly_discount, currency, billing_interval, badge, description, features, max_products, max_themes, priority_support, trial_days, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            price = VALUES(price),
            yearly_price = VALUES(yearly_price),
            yearly_discount = VALUES(yearly_discount),
            badge = VALUES(badge),
            description = VALUES(description),
            features = VALUES(features),
            max_products = VALUES(max_products),
            max_themes = VALUES(max_themes),
            priority_support = VALUES(priority_support),
            trial_days = VALUES(trial_days),
            is_active = 1
    ");

    foreach ($plans as $p) {
        $stmt->execute([
            $p['id'],
            $p['name'],
            $p['slug'],
            $p['price'],
            $p['yearly_price'],
            $p['yearly_discount'],
            $p['currency'],
            $p['billing_interval'],
            $p['badge'],
            $p['description'],
            $p['features'],
            $p['max_products'],
            $p['max_themes'],
            $p['priority_support'],
            $p['trial_days'],
        ]);
    }
};
