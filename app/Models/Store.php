<?php

namespace App\Models;

use App\Core\Database;

class Store
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM stores WHERE id = ?", [$id]);
    }

    public function findByMerchantId(int $merchantId): ?array
    {
        return $this->db->fetchOne("SELECT * FROM stores WHERE merchant_id = ?", [$merchantId]);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetchOne("SELECT * FROM stores WHERE slug = ?", [$slug]);
    }

    public function slugExists(string $slug, ?int $excludeStoreId = null): bool
    {
        if ($excludeStoreId) {
            return $this->db->exists('stores', 'slug = ? AND id != ?', [$slug, $excludeStoreId]);
        }
        return $this->db->exists('stores', 'slug = ?', [$slug]);
    }

    public function create(array $data): int
    {
        return $this->db->insert('stores', [
            'merchant_id' => $data['merchant_id'],
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'logo'        => $data['logo'] ?? null,
            'favicon'     => $data['favicon'] ?? null,
            'description' => $data['description'] ?? null,
            'status'      => $data['status'] ?? 'active',
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $allowed = ['name', 'slug', 'logo', 'favicon', 'description', 'status'];
        $updateData = array_intersect_key($data, array_flip($allowed));

        if (empty($updateData)) {
            return false;
        }

        return $this->db->update('stores', $updateData, 'id = ?', [$id]) >= 0;
    }

    public function getAll(int $page = 1, int $perPage = 20, ?string $search = null): array
    {
        $where = '1=1';
        $params = [];

        if ($search) {
            $where .= ' AND (s.name LIKE ? OR s.slug LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $offset = ($page - 1) * $perPage;
        $total = $this->db->fetchColumn("SELECT COUNT(*) FROM stores s WHERE {$where}", $params);

        $stores = $this->db->fetchAll("
            SELECT s.*, m.business_name, u.email as merchant_email, u.name as merchant_name,
                   (SELECT COUNT(*) FROM products WHERE merchant_id = s.merchant_id) as total_products,
                   (SELECT COUNT(*) FROM orders WHERE store_id = s.id) as total_orders
            FROM stores s
            JOIN merchants m ON m.id = s.merchant_id
            JOIN users u ON u.id = m.user_id
            WHERE {$where}
            ORDER BY s.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);

        return [
            'data'     => $stores,
            'total'    => (int) $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int) ceil($total / $perPage),
        ];
    }
}
