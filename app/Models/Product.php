<?php

namespace App\Models;

use App\Core\Database;

class Product
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function findByIdAndMerchant(int $id, int $merchantId): ?array
    {
        $product = $this->db->fetchOne("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.id = ? AND p.merchant_id = ?
        ", [$id, $merchantId]);

        if ($product) {
            $product['images'] = json_decode($product['images'] ?? '[]', true) ?: [];
            $product['variants'] = $this->getVariants($product['id'], $merchantId);
        }

        return $product;
    }

    public function findBySlugAndMerchant(string $slug, int $merchantId): ?array
    {
        $product = $this->db->fetchOne("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.slug = ? AND p.merchant_id = ?
        ", [$slug, $merchantId]);

        if ($product) {
            $product['images'] = json_decode($product['images'] ?? '[]', true) ?: [];
            $product['variants'] = $this->getVariants($product['id'], $merchantId);
        }

        return $product;
    }

    public function getVariants(int $productId, int $merchantId): array
    {
        return $this->db->fetchAll("
            SELECT * FROM product_variants
            WHERE product_id = ? AND merchant_id = ?
            ORDER BY id ASC
        ", [$productId, $merchantId]);
    }

    public function getAllForMerchant(int $merchantId, array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = "p.merchant_id = ?";
        $params = [$merchantId];

        if (!empty($filters['search'])) {
            $where .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        if (!empty($filters['category_id'])) {
            $where .= " AND p.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['status'])) {
            $where .= " AND p.status = ?";
            $params[] = $filters['status'];
        }

        $offset = ($page - 1) * $perPage;
        $total = $this->db->fetchColumn("SELECT COUNT(*) FROM products p WHERE {$where}", $params);

        $products = $this->db->fetchAll("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE {$where}
            ORDER BY p.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);

        foreach ($products as &$p) {
            $p['images'] = json_decode($p['images'] ?? '[]', true) ?: [];
        }

        return [
            'data'     => $products,
            'total'    => (int)$total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int)ceil($total / $perPage),
        ];
    }

    public function create(int $merchantId, array $data): int
    {
        $slug = !empty($data['slug']) ? slugify($data['slug']) : slugify($data['name']);
        
        // Ensure unique slug for merchant
        $originalSlug = $slug;
        $counter = 1;
        while ($this->db->exists('products', 'slug = ? AND merchant_id = ?', [$slug, $merchantId])) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $images = is_array($data['images'] ?? null) ? json_encode($data['images']) : json_encode([]);

        $productId = $this->db->insert('products', [
            'merchant_id'       => $merchantId,
            'category_id'       => !empty($data['category_id']) ? (int)$data['category_id'] : null,
            'name'              => $data['name'],
            'slug'              => $slug,
            'sku'               => $data['sku'] ?? null,
            'description'       => $data['description'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'price'             => (float)($data['price'] ?? 0),
            'compare_price'     => !empty($data['compare_price']) ? (float)$data['compare_price'] : null,
            'stock'             => (int)($data['stock'] ?? 0),
            'low_stock_limit'   => (int)($data['low_stock_limit'] ?? 5),
            'brand'             => $data['brand'] ?? null,
            'weight'            => !empty($data['weight']) ? (float)$data['weight'] : null,
            'status'            => $data['status'] ?? 'published',
            'is_featured'       => !empty($data['is_featured']) ? 1 : 0,
            'images'            => $images,
            'seo_title'         => $data['seo_title'] ?? null,
            'seo_description'   => $data['seo_description'] ?? null,
        ]);

        // Save inventory transaction if initial stock > 0
        if (!empty($data['stock']) && (int)$data['stock'] > 0) {
            $this->db->insert('inventory_transactions', [
                'merchant_id'    => $merchantId,
                'product_id'     => $productId,
                'type'           => 'addition',
                'quantity'       => (int)$data['stock'],
                'previous_stock' => 0,
                'new_stock'      => (int)$data['stock'],
                'notes'          => 'Initial product creation stock',
            ]);
        }

        // Save variants if provided
        if (!empty($data['variants']) && is_array($data['variants'])) {
            $this->saveVariants($productId, $merchantId, $data['variants']);
        }

        return $productId;
    }

    public function update(int $id, int $merchantId, array $data): bool
    {
        $allowed = [
            'category_id', 'name', 'slug', 'sku', 'description',
            'short_description', 'price', 'compare_price', 'stock',
            'low_stock_limit', 'brand', 'weight', 'status', 'is_featured',
            'images', 'seo_title', 'seo_description'
        ];

        $cleanData = array_intersect_key($data, array_flip($allowed));

        if (isset($cleanData['images']) && is_array($cleanData['images'])) {
            $cleanData['images'] = json_encode($cleanData['images']);
        }

        if (isset($cleanData['category_id']) && empty($cleanData['category_id'])) {
            $cleanData['category_id'] = null;
        }

        $res = $this->db->update('products', $cleanData, 'id = ? AND merchant_id = ?', [$id, $merchantId]);

        if (isset($data['variants']) && is_array($data['variants'])) {
            $this->saveVariants($id, $merchantId, $data['variants']);
        }

        return $res >= 0;
    }

    public function saveVariants(int $productId, int $merchantId, array $variants): void
    {
        $this->db->delete('product_variants', 'product_id = ? AND merchant_id = ?', [$productId, $merchantId]);

        foreach ($variants as $v) {
            if (empty($v['name'])) continue;

            $this->db->insert('product_variants', [
                'product_id'  => $productId,
                'merchant_id' => $merchantId,
                'name'        => $v['name'],
                'sku'         => $v['sku'] ?? null,
                'price'       => (float)($v['price'] ?? 0),
                'stock'       => (int)($v['stock'] ?? 0),
                'options'     => !empty($v['options']) ? json_encode($v['options']) : null,
            ]);
        }
    }

    public function delete(int $id, int $merchantId): bool
    {
        return $this->db->delete('products', 'id = ? AND merchant_id = ?', [$id, $merchantId]) > 0;
    }

    public function adjustStock(int $productId, int $merchantId, int $newStock, string $reason, ?int $variantId = null): bool
    {
        if ($variantId) {
            $currentVariant = $this->db->fetchOne("SELECT stock FROM product_variants WHERE id = ? AND product_id = ? AND merchant_id = ?", [$variantId, $productId, $merchantId]);
            if (!$currentVariant) return false;

            $prevStock = (int)$currentVariant['stock'];
            $diff = $newStock - $prevStock;
            $type = $diff >= 0 ? 'addition' : 'deduction';

            $this->db->update('product_variants', ['stock' => $newStock], 'id = ?', [$variantId]);
            $this->db->insert('inventory_transactions', [
                'merchant_id'    => $merchantId,
                'product_id'     => $productId,
                'variant_id'     => $variantId,
                'type'           => $type,
                'quantity'       => abs($diff),
                'previous_stock' => $prevStock,
                'new_stock'      => $newStock,
                'notes'          => $reason ?: 'Manual stock adjustment',
            ]);
            return true;
        }

        $current = $this->findByIdAndMerchant($productId, $merchantId);
        if (!$current) return false;

        $prevStock = (int)$current['stock'];
        $diff = $newStock - $prevStock;
        $type = $diff >= 0 ? 'addition' : 'deduction';

        $this->db->update('products', ['stock' => $newStock], 'id = ? AND merchant_id = ?', [$productId, $merchantId]);

        $this->db->insert('inventory_transactions', [
            'merchant_id'    => $merchantId,
            'product_id'     => $productId,
            'type'           => $type,
            'quantity'       => abs($diff),
            'previous_stock' => $prevStock,
            'new_stock'      => $newStock,
            'notes'          => $reason ?: 'Manual stock adjustment',
        ]);

        return true;
    }

    public function getInventoryLog(int $merchantId, int $limit = 50): array
    {
        return $this->db->fetchAll("
            SELECT it.*, p.name as product_name, pv.name as variant_name
            FROM inventory_transactions it
            JOIN products p ON p.id = it.product_id
            LEFT JOIN product_variants pv ON pv.id = it.variant_id
            WHERE it.merchant_id = ?
            ORDER BY it.created_at DESC
            LIMIT {$limit}
        ", [$merchantId]);
    }

    public function countByMerchant(int $merchantId): int
    {
        return (int)$this->db->fetchColumn("SELECT COUNT(*) FROM products WHERE merchant_id = ?", [$merchantId]);
    }
}
