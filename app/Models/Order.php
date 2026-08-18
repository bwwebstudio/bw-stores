<?php

namespace App\Models;

use App\Core\Database;

class Order
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function findByIdAndMerchant(int $id, int $merchantId): ?array
    {
        $order = $this->db->fetchOne("
            SELECT o.*, s.name as store_name, s.slug as store_slug
            FROM orders o
            JOIN stores s ON s.id = o.store_id
            WHERE o.id = ? AND o.merchant_id = ?
        ", [$id, $merchantId]);

        if ($order) {
            $order['items'] = $this->getItems($order['id']);
        }

        return $order;
    }

    public function findByOrderNumber(string $orderNumber): ?array
    {
        $order = $this->db->fetchOne("
            SELECT o.*, s.name as store_name, s.slug as store_slug
            FROM orders o
            JOIN stores s ON s.id = o.store_id
            WHERE o.order_number = ?
        ", [$orderNumber]);

        if ($order) {
            $order['items'] = $this->getItems($order['id']);
        }

        return $order;
    }

    public function getItems(int $orderId): array
    {
        return $this->db->fetchAll("
            SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC
        ", [$orderId]);
    }

    public function getAllForMerchant(int $merchantId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = "o.merchant_id = ?";
        $params = [$merchantId];

        if (!empty($filters['search'])) {
            $where .= " AND (o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ? OR o.customer_mobile LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        if (!empty($filters['order_status'])) {
            $where .= " AND o.order_status = ?";
            $params[] = $filters['order_status'];
        }

        if (!empty($filters['payment_status'])) {
            $where .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        $offset = ($page - 1) * $perPage;
        $total = $this->db->fetchColumn("SELECT COUNT(*) FROM orders o WHERE {$where}", $params);

        $orders = $this->db->fetchAll("
            SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as total_items
            FROM orders o
            WHERE {$where}
            ORDER BY o.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);

        return [
            'data'     => $orders,
            'total'    => (int)$total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int)ceil($total / $perPage),
        ];
    }

    public function createOrder(array $orderData, array $items): array
    {
        return $this->db->transaction(function (Database $db) use ($orderData, $items) {
            // Generate unique Order Number: BW-YYYY-XXXXXX
            $year = date('Y');
            $randomNum = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $orderNumber = "BW-{$year}-{$randomNum}";

            // Insert Order
            $orderId = $db->insert('orders', [
                'merchant_id'          => $orderData['merchant_id'],
                'store_id'             => $orderData['store_id'],
                'customer_id'          => $orderData['customer_id'] ?? null,
                'order_number'         => $orderNumber,
                'customer_name'        => $orderData['customer_name'],
                'customer_email'       => strtolower($orderData['customer_email']),
                'customer_mobile'      => $orderData['customer_mobile'],
                'shipping_address'     => $orderData['shipping_address'],
                'shipping_city'        => $orderData['shipping_city'],
                'shipping_state'       => $orderData['shipping_state'],
                'shipping_postal_code' => $orderData['shipping_postal_code'],
                'subtotal'             => $orderData['subtotal'],
                'discount'             => $orderData['discount'] ?? 0.00,
                'tax'                  => $orderData['tax'] ?? 0.00,
                'shipping'             => $orderData['shipping'] ?? 0.00,
                'total'                => $orderData['total'],
                'coupon_code'          => $orderData['coupon_code'] ?? null,
                'order_status'         => 'CONFIRMED',
                'payment_status'       => $orderData['payment_method'] === 'ONLINE' ? 'PAID' : 'PENDING',
                'payment_method'       => $orderData['payment_method'] ?? 'COD',
                'notes'                => $orderData['notes'] ?? null,
            ]);

            // Insert Order Items and deduct stock safely
            foreach ($items as $item) {
                $db->insert('order_items', [
                    'order_id'     => $orderId,
                    'merchant_id'  => $orderData['merchant_id'],
                    'product_id'   => $item['product_id'],
                    'variant_id'   => $item['variant_id'] ?? null,
                    'product_name' => $item['name'],
                    'sku'          => $item['sku'] ?? null,
                    'variant_name' => $item['variant_name'] ?? null,
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'total'        => $item['price'] * $item['quantity'],
                ]);

                // Deduct stock from variant or product
                if (!empty($item['variant_id'])) {
                    $variant = $db->fetchOne("SELECT stock FROM product_variants WHERE id = ?", [$item['variant_id']]);
                    if ($variant) {
                        $newStock = max(0, (int)$variant['stock'] - $item['quantity']);
                        $db->update('product_variants', ['stock' => $newStock], 'id = ?', [$item['variant_id']]);
                    }
                }

                $product = $db->fetchOne("SELECT stock FROM products WHERE id = ?", [$item['product_id']]);
                if ($product) {
                    $newStock = max(0, (int)$product['stock'] - $item['quantity']);
                    $db->update('products', ['stock' => $newStock], 'id = ?', [$item['product_id']]);

                    // Record inventory transaction
                    $db->insert('inventory_transactions', [
                        'merchant_id'    => $orderData['merchant_id'],
                        'product_id'     => $item['product_id'],
                        'variant_id'     => $item['variant_id'] ?? null,
                        'type'           => 'order_placed',
                        'quantity'       => $item['quantity'],
                        'previous_stock' => (int)$product['stock'],
                        'new_stock'      => $newStock,
                        'notes'          => "Customer Order #{$orderNumber}",
                        'reference_id'   => $orderNumber,
                    ]);
                }
            }

            // Record Customer record if doesn't exist
            $existingCustomer = $db->fetchOne("SELECT id FROM customers WHERE email = ? AND merchant_id = ?", [
                strtolower($orderData['customer_email']),
                $orderData['merchant_id']
            ]);

            if (!$existingCustomer) {
                $db->insert('customers', [
                    'merchant_id' => $orderData['merchant_id'],
                    'name'        => $orderData['customer_name'],
                    'email'       => strtolower($orderData['customer_email']),
                    'mobile'      => $orderData['customer_mobile'],
                ]);
            }

            // Record Notification for Merchant
            $db->insert('notifications', [
                'merchant_id' => $orderData['merchant_id'],
                'title'       => "New Order #{$orderNumber}",
                'message'     => "Order of ₹" . number_format($orderData['total'], 2) . " received from " . e($orderData['customer_name']),
                'type'        => 'order',
                'link'        => url('dashboard/orders/' . $orderId),
            ]);

            return [
                'order_id'     => $orderId,
                'order_number' => $orderNumber,
            ];
        });
    }

    public function getTodaySales(int $merchantId): float
    {
        $sales = $this->db->fetchColumn("
            SELECT COALESCE(SUM(total), 0)
            FROM orders
            WHERE merchant_id = ? AND DATE(created_at) = CURDATE() AND payment_status = 'PAID'
        ", [$merchantId]);

        return (float)($sales ?: 0);
    }

    public function countByMerchant(int $merchantId): int
    {
        return (int)$this->db->fetchColumn("SELECT COUNT(*) FROM orders WHERE merchant_id = ?", [$merchantId]);
    }

    public function findByMerchant(int $merchantId, array $filters = [], int $limit = 5, int $offset = 0): array
    {
        return $this->db->fetchAll("
            SELECT * FROM orders WHERE merchant_id = ? ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}
        ", [$merchantId]);
    }

    public function updateStatus(int $orderId, int $merchantId, string $orderStatus, ?string $paymentStatus = null): bool
    {
        $data = ['order_status' => $orderStatus];
        if ($paymentStatus) {
            $data['payment_status'] = $paymentStatus;
        }

        return $this->db->update('orders', $data, 'id = ? AND merchant_id = ?', [$orderId, $merchantId]) >= 0;
    }
}
