<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $db = $this->app->getDatabase();

        $range = $request->query('range', '30'); // 'today', '7', '30', 'all'

        $dateCondition = match ($range) {
            'today' => "AND DATE(created_at) = CURDATE()",
            '7'     => "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
            '30'    => "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            default => "",
        };

        // Key Metric Cards
        $totalSales = (float)$db->fetchColumn("SELECT COALESCE(SUM(total), 0) FROM orders WHERE merchant_id = ? AND payment_status = 'PAID' {$dateCondition}", [$merchantId]);
        $totalOrders = (int)$db->fetchColumn("SELECT COUNT(*) FROM orders WHERE merchant_id = ? {$dateCondition}", [$merchantId]);
        $avgOrderValue = $totalOrders > 0 ? ($totalSales / $totalOrders) : 0;
        $totalCustomers = (int)$db->fetchColumn("SELECT COUNT(DISTINCT customer_email) FROM orders WHERE merchant_id = ? {$dateCondition}", [$merchantId]);

        // Daily Revenue Data (Last 14 days)
        $dailySales = $db->fetchAll("
            SELECT DATE(created_at) as sale_date, COALESCE(SUM(total), 0) as revenue, COUNT(*) as orders_count
            FROM orders
            WHERE merchant_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
            GROUP BY DATE(created_at)
            ORDER BY sale_date ASC
        ", [$merchantId]);

        // Top Selling Products
        $topProducts = $db->fetchAll("
            SELECT oi.product_name, SUM(oi.quantity) as total_qty, SUM(oi.total) as total_revenue
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            WHERE oi.merchant_id = ? AND o.payment_status = 'PAID'
            GROUP BY oi.product_name
            ORDER BY total_qty DESC
            LIMIT 5
        ", [$merchantId]);

        // Recent Orders
        $recentOrders = $db->fetchAll("
            SELECT * FROM orders
            WHERE merchant_id = ?
            ORDER BY created_at DESC
            LIMIT 5
        ", [$merchantId]);

        $this->view('merchant.analytics.index', [
            'totalSales'     => $totalSales,
            'totalOrders'    => $totalOrders,
            'avgOrderValue'  => $avgOrderValue,
            'totalCustomers' => $totalCustomers,
            'dailySales'     => $dailySales,
            'topProducts'    => $topProducts,
            'recentOrders'   => $recentOrders,
            'range'          => $range,
        ]);
    }
}
