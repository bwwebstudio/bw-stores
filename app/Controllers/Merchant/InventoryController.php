<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Product;

class InventoryController extends Controller
{
    private Product $productModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->productModel = new Product($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $db = $this->app->getDatabase();

        $search = sanitize_input($request->query('search'));
        $where = "p.merchant_id = ?";
        $params = [$merchantId];

        if ($search) {
            $where .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $products = $db->fetchAll("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE {$where}
            ORDER BY p.stock ASC, p.name ASC
        ", $params);

        foreach ($products as &$p) {
            $p['variants'] = $this->productModel->getVariants($p['id'], $merchantId);
        }

        $logs = $this->productModel->getInventoryLog($merchantId, 30);

        $this->view('merchant.inventory.index', [
            'products' => $products,
            'logs'     => $logs,
            'search'   => $search,
        ]);
    }

    public function adjust(Request $request): void
    {
        $merchantId = current_merchant_id();
        $productId = (int)$request->input('product_id');
        $variantId = $request->input('variant_id') ? (int)$request->input('variant_id') : null;
        $newStock = max(0, (int)$request->input('new_stock'));
        $reason = sanitize_input($request->input('reason'));

        $this->productModel->adjustStock($productId, $merchantId, $newStock, $reason, $variantId);

        flash('success', 'Stock level adjusted successfully.');
        $this->redirect(url('dashboard/inventory'));
    }
}
