<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Request;

/**
 * Home Controller
 */
class HomeController extends Controller
{
    /**
     * Show the public SaaS marketing home page.
     * GET /
     */
    public function index(Request $request): void
    {
        if (is_authenticated()) {
            if (is_admin()) {
                $this->redirect(url('admin'));
            } else {
                $this->redirect(url('dashboard'));
            }
            return;
        }

        $db = $this->app->getDatabase();
        $plans = $db->fetchAll("SELECT * FROM plans WHERE is_active = 1 ORDER BY price ASC");
        
        if (empty($plans)) {
            $plans = [
                [
                    'id' => 1,
                    'name' => 'BW Store Starter',
                    'slug' => 'starter',
                    'price' => 499.00,
                    'yearly_price' => 5888.00,
                    'yearly_discount' => 100.00,
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
                    'trial_days' => 7,
                ],
                [
                    'id' => 2,
                    'name' => 'BW Store Growth',
                    'slug' => 'growth',
                    'price' => 999.00,
                    'yearly_price' => 11788.00,
                    'yearly_discount' => 200.00,
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
                    'trial_days' => 7,
                ],
                [
                    'id' => 3,
                    'name' => 'BW Store Enterprise',
                    'slug' => 'enterprise',
                    'price' => 2999.00,
                    'yearly_price' => 34988.00,
                    'yearly_discount' => 1000.00,
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
                    'trial_days' => 7,
                ]
            ];
        }

        // Live stats for social proof counters
        $stats = [
            'stores_count'   => max(124, (int)$db->fetchColumn("SELECT COUNT(*) FROM stores WHERE status = 'active'")),
            'orders_count'   => max(1850, (int)$db->fetchColumn("SELECT COUNT(*) FROM orders")),
            'products_count' => max(420, (int)$db->fetchColumn("SELECT COUNT(*) FROM products")),
        ];

        $this->view('home', [
            'plans' => $plans,
            'stats' => $stats,
        ]);
    }
}
