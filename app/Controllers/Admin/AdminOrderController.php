<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request): void
    {
        $db = $this->app->getDatabase();
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $total = $db->fetchColumn("SELECT COUNT(*) FROM orders");

        $orders = $db->fetchAll("
            SELECT o.*, s.name as store_name, s.slug as store_slug, m.business_name
            FROM orders o
            JOIN stores s ON s.id = o.store_id
            JOIN merchants m ON m.id = o.merchant_id
            ORDER BY o.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");

        $this->view('admin.orders.index', [
            'orders'  => $orders,
            'total'   => (int)$total,
            'page'    => $page,
            'pages'   => (int)ceil($total / $perPage),
        ]);
    }
}
