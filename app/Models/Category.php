<?php

namespace App\Models;

use App\Core\Database;

class Category
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function findByIdAndMerchant(int $id, int $merchantId): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM categories WHERE id = ? AND merchant_id = ?",
            [$id, $merchantId]
        );
    }

    public function findBySlugAndMerchant(string $slug, int $merchantId): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM categories WHERE slug = ? AND merchant_id = ?",
            [$slug, $merchantId]
        );
    }

    public function getAllForMerchant(int $merchantId, bool $activeOnly = false): array
    {
        $where = "merchant_id = ?";
        $params = [$merchantId];

        if ($activeOnly) {
            $where .= " AND status = 'active'";
        }

        return $this->db->fetchAll("
            SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id AND merchant_id = c.merchant_id) as product_count
            FROM categories c
            WHERE {$where}
            ORDER BY c.sort_order ASC, c.name ASC
        ", $params);
    }

    public function create(int $merchantId, array $data): int
    {
        return $this->db->insert('categories', [
            'merchant_id' => $merchantId,
            'name'        => $data['name'],
            'slug'        => $data['slug'] ?: slugify($data['name']),
            'description' => $data['description'] ?? null,
            'image'       => $data['image'] ?? null,
            'sort_order'  => (int)($data['sort_order'] ?? 0),
            'status'      => $data['status'] ?? 'active',
        ]);
    }

    public function update(int $id, int $merchantId, array $data): bool
    {
        $allowed = ['name', 'slug', 'description', 'image', 'sort_order', 'status'];
        $cleanData = array_intersect_key($data, array_flip($allowed));

        if (isset($cleanData['name']) && empty($cleanData['slug'])) {
            $cleanData['slug'] = slugify($cleanData['name']);
        }

        return $this->db->update(
            'categories',
            $cleanData,
            'id = ? AND merchant_id = ?',
            [$id, $merchantId]
        ) >= 0;
    }

    public function delete(int $id, int $merchantId): bool
    {
        // Reassign products to null category first
        $this->db->update('products', ['category_id' => null], 'category_id = ? AND merchant_id = ?', [$id, $merchantId]);

        return $this->db->delete('categories', 'id = ? AND merchant_id = ?', [$id, $merchantId]) > 0;
    }
}
