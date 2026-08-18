<?php

namespace App\Models;

use App\Core\Database;

class Customer
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function getAllForMerchant(int $merchantId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = "c.merchant_id = ?";
        $params = [$merchantId];

        if (!empty($filters['search'])) {
            $where .= " AND (c.name LIKE ? OR c.email LIKE ? OR c.mobile LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        $offset = ($page - 1) * $perPage;
        $total = $this->db->fetchColumn("SELECT COUNT(*) FROM customers c WHERE {$where}", $params);

        $customers = $this->db->fetchAll("
            SELECT c.*,
                   (SELECT COUNT(*) FROM orders WHERE customer_email = c.email AND merchant_id = c.merchant_id) as total_orders,
                   (SELECT COALESCE(SUM(total), 0) FROM orders WHERE customer_email = c.email AND merchant_id = c.merchant_id AND payment_status = 'PAID') as total_spent,
                   (SELECT MAX(created_at) FROM orders WHERE customer_email = c.email AND merchant_id = c.merchant_id) as last_order_at
            FROM customers c
            WHERE {$where}
            ORDER BY c.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);

        return [
            'data'     => $customers,
            'total'    => (int)$total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int)ceil($total / $perPage),
        ];
    }

    public function findByIdAndMerchant(int $id, int $merchantId): ?array
    {
        $customer = $this->db->fetchOne("SELECT * FROM customers WHERE id = ? AND merchant_id = ?", [$id, $merchantId]);
        if ($customer) {
            $customer['orders'] = $this->db->fetchAll("
                SELECT * FROM orders WHERE customer_email = ? AND merchant_id = ? ORDER BY created_at DESC
            ", [$customer['email'], $merchantId]);
        }
        return $customer;
    }
}
