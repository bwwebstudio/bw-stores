<?php

namespace App\Models;

use App\Core\Database;

class Coupon
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function getAllForMerchant(int $merchantId): array
    {
        return $this->db->fetchAll("
            SELECT * FROM coupons WHERE merchant_id = ? ORDER BY created_at DESC
        ", [$merchantId]);
    }

    public function findByIdAndMerchant(int $id, int $merchantId): ?array
    {
        return $this->db->fetchOne("SELECT * FROM coupons WHERE id = ? AND merchant_id = ?", [$id, $merchantId]);
    }

    public function findByCodeAndMerchant(string $code, int $merchantId): ?array
    {
        return $this->db->fetchOne("SELECT * FROM coupons WHERE UPPER(code) = ? AND merchant_id = ?", [
            strtoupper(trim($code)),
            $merchantId
        ]);
    }

    public function create(int $merchantId, array $data): int
    {
        return $this->db->insert('coupons', [
            'merchant_id'  => $merchantId,
            'code'         => strtoupper(trim($data['code'])),
            'type'         => $data['type'] ?? 'percentage',
            'value'        => (float)$data['value'],
            'min_order'    => (float)($data['min_order'] ?? 0),
            'max_discount' => !empty($data['max_discount']) ? (float)$data['max_discount'] : null,
            'usage_limit'  => !empty($data['usage_limit']) ? (int)$data['usage_limit'] : null,
            'expires_at'   => !empty($data['expires_at']) ? $data['expires_at'] : null,
            'is_active'    => !empty($data['is_active']) ? 1 : 0,
        ]);
    }

    public function update(int $id, int $merchantId, array $data): bool
    {
        $allowed = ['code', 'type', 'value', 'min_order', 'max_discount', 'usage_limit', 'expires_at', 'is_active'];
        $cleanData = array_intersect_key($data, array_flip($allowed));

        if (isset($cleanData['code'])) {
            $cleanData['code'] = strtoupper(trim($cleanData['code']));
        }

        return $this->db->update('coupons', $cleanData, 'id = ? AND merchant_id = ?', [$id, $merchantId]) >= 0;
    }

    public function delete(int $id, int $merchantId): bool
    {
        return $this->db->delete('coupons', 'id = ? AND merchant_id = ?', [$id, $merchantId]) > 0;
    }

    public function validateAndCalculate(string $code, int $merchantId, float $subtotal): array
    {
        $coupon = $this->findByCodeAndMerchant($code, $merchantId);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Coupon code does not exist.'];
        }

        if (!$coupon['is_active']) {
            return ['valid' => false, 'message' => 'This coupon is currently inactive.'];
        }

        if (!empty($coupon['expires_at']) && strtotime($coupon['expires_at']) < strtotime(date('Y-m-d'))) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        if (!empty($coupon['usage_limit']) && (int)$coupon['usage_count'] >= (int)$coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'Coupon usage limit has been reached.'];
        }

        if ($subtotal < (float)$coupon['min_order']) {
            return ['valid' => false, 'message' => 'Minimum order amount of ₹' . number_format($coupon['min_order'], 2) . ' required for this coupon.'];
        }

        $discount = 0.0;
        if ($coupon['type'] === 'percentage') {
            $discount = ($subtotal * (float)$coupon['value']) / 100;
            if (!empty($coupon['max_discount']) && $discount > (float)$coupon['max_discount']) {
                $discount = (float)$coupon['max_discount'];
            }
        } else {
            $discount = min($subtotal, (float)$coupon['value']);
        }

        return [
            'valid'           => true,
            'coupon'          => $coupon,
            'discount_amount' => round($discount, 2),
            'message'         => 'Coupon applied successfully!',
        ];
    }
}
