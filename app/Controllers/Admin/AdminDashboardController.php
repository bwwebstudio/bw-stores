<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Merchant;

/**
 * Admin Dashboard Controller
 */
class AdminDashboardController extends Controller
{
    /**
     * Show admin dashboard.
     * GET /admin
     */
    public function index(Request $request): void
    {
        $db = $this->app->getDatabase();
        $merchantModel = new Merchant($db);

        // Gather comprehensive dashboard statistics
        $activeMRR = (float)$db->fetchColumn("
            SELECT COALESCE(SUM(p.price), 0)
            FROM subscriptions s
            JOIN plans p ON p.id = s.plan_id
            WHERE s.status = 'active' AND (s.current_period_end IS NULL OR s.current_period_end > NOW())
        ");

        $totalRevenue = (float)$db->fetchColumn("
            SELECT COALESCE(SUM(amount), 0)
            FROM subscription_payments
            WHERE status = 'paid'
        ");

        $pendingPaymentsCount = (int)$db->fetchColumn("
            SELECT COUNT(*) 
            FROM subscription_payments 
            WHERE status = 'pending_verification'
        ");

        // Plan wise distribution
        $planBreakdown = $db->fetchAll("
            SELECT p.name, p.slug, p.price, p.badge, COUNT(s.id) as subscriber_count
            FROM plans p
            LEFT JOIN subscriptions s ON s.plan_id = p.id AND s.status = 'active'
            GROUP BY p.id
            ORDER BY p.price ASC
        ");

        $stats = [
            'total_merchants'      => $merchantModel->countByStatus(),
            'active_merchants'     => $merchantModel->countByStatus('active'),
            'pending_merchants'    => $merchantModel->countByStatus('pending'),
            'suspended_merchants'  => $merchantModel->countByStatus('suspended'),
            'total_stores'         => (int)$db->fetchColumn("SELECT COUNT(*) FROM stores"),
            'active_subscriptions' => (int)$db->fetchColumn("SELECT COUNT(*) FROM subscriptions WHERE status = 'active'"),
            'mrr'                  => $activeMRR,
            'total_revenue'        => $totalRevenue,
            'pending_payments'     => $pendingPaymentsCount,
            'plan_breakdown'       => $planBreakdown,
        ];

        // Recent merchants
        $recentMerchants = $db->fetchAll("
            SELECT m.*, u.name, u.email, u.created_at as registered_at, p.name as plan_name, p.badge as plan_badge
            FROM merchants m
            JOIN users u ON u.id = m.user_id
            LEFT JOIN subscriptions s ON s.merchant_id = m.id
            LEFT JOIN plans p ON p.id = s.plan_id
            ORDER BY m.created_at DESC
            LIMIT 5
        ");

        // Recent payments awaiting verification
        $pendingPayments = $db->fetchAll("
            SELECT sp.*, m.business_name, u.name as user_name, u.email as user_email, p.name as plan_name
            FROM subscription_payments sp
            JOIN merchants m ON m.id = sp.merchant_id
            JOIN users u ON u.id = m.user_id
            LEFT JOIN subscriptions s ON s.id = sp.subscription_id
            LEFT JOIN plans p ON p.id = s.plan_id
            WHERE sp.status = 'pending_verification'
            ORDER BY sp.created_at DESC
            LIMIT 5
        ");

        $this->view('admin.dashboard', [
            'stats'            => $stats,
            'recentMerchants'  => $recentMerchants,
            'pendingPayments'  => $pendingPayments,
        ]);
    }
}
