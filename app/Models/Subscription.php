<?php

namespace App\Models;

use App\Core\Database;

class Subscription
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    /**
     * Get all active subscription plans
     */
    public function getAllPlans(): array
    {
        return $this->db->fetchAll("
            SELECT * FROM plans 
            WHERE is_active = 1 
            ORDER BY price ASC
        ");
    }

    /**
     * Find plan by ID
     */
    public function findPlanById(int $planId): ?array
    {
        return $this->db->fetchOne("SELECT * FROM plans WHERE id = ?", [$planId]);
    }

    /**
     * Find plan by Slug
     */
    public function findPlanBySlug(string $slug): ?array
    {
        return $this->db->fetchOne("SELECT * FROM plans WHERE slug = ?", [$slug]);
    }

    /**
     * Get merchant's current subscription details with full plan metadata
     */
    public function findByMerchantId(int $merchantId): ?array
    {
        return $this->db->fetchOne("
            SELECT s.*, 
                   p.name as plan_name, 
                   p.slug as plan_slug,
                   p.price as plan_price, 
                   p.currency as plan_currency, 
                   p.billing_interval,
                   p.badge as plan_badge,
                   p.description as plan_description,
                   p.features as plan_features,
                   p.max_products as plan_max_products,
                   p.max_themes as plan_max_themes,
                   p.priority_support as plan_priority_support
            FROM subscriptions s
            JOIN plans p ON p.id = s.plan_id
            WHERE s.merchant_id = ?
            ORDER BY s.id DESC
            LIMIT 1
        ", [$merchantId]);
    }

    /**
     * Get all payment transaction history for a merchant
     */
    public function getPayments(int $merchantId): array
    {
        return $this->db->fetchAll("
            SELECT * FROM subscription_payments
            WHERE merchant_id = ?
            ORDER BY created_at DESC
        ", [$merchantId]);
    }

    /**
     * Create or update a pending subscription for UPI verification
     */
    public function createPending(int $merchantId, int $planId): int
    {
        $existing = $this->findByMerchantId($merchantId);
        $start = date('Y-m-d H:i:s');
        $end = date('Y-m-d H:i:s', strtotime("+30 days"));

        if ($existing) {
            $this->db->update('subscriptions', [
                'plan_id'              => $planId,
                'status'               => 'pending',
                'current_period_start' => $start,
                'current_period_end'   => $end,
            ], 'id = ?', [$existing['id']]);

            return (int)$existing['id'];
        }

        return (int)$this->db->insert('subscriptions', [
            'merchant_id'          => $merchantId,
            'plan_id'              => $planId,
            'status'               => 'pending',
            'current_period_start' => $start,
            'current_period_end'   => $end,
        ]);
    }

    /**
     * Activate subscription for specified period (default 30 days)
     */
    public function createOrActivate(int $merchantId, int $planId, int $periodDays = 30): int
    {
        $existing = $this->findByMerchantId($merchantId);
        $start = date('Y-m-d H:i:s');
        $end = date('Y-m-d H:i:s', strtotime("+{$periodDays} days"));

        if ($existing) {
            $this->db->update('subscriptions', [
                'plan_id'              => $planId,
                'status'               => 'active',
                'current_period_start' => $start,
                'current_period_end'   => $end,
                'expires_at'           => $end,
            ], 'id = ?', [$existing['id']]);

            return (int)$existing['id'];
        }

        return (int)$this->db->insert('subscriptions', [
            'merchant_id'          => $merchantId,
            'plan_id'              => $planId,
            'status'               => 'active',
            'current_period_start' => $start,
            'current_period_end'   => $end,
            'expires_at'           => $end,
        ]);
    }

    /**
     * Check if merchant has an active, unexpired subscription
     */
    public function isSubscriptionActive(int $merchantId): bool
    {
        $sub = $this->findByMerchantId($merchantId);
        if (!$sub) {
            return false;
        }

        if ($sub['status'] !== 'active') {
            return false;
        }

        if (!empty($sub['current_period_end']) && strtotime($sub['current_period_end']) < time()) {
            return false;
        }

        return true;
    }

    /**
     * Get all subscriptions for Admin panel with pagination and filters
     */
    public function getAllForAdmin(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND s.status = ?";
            $params[] = $filters['status'];
        }

        $offset = ($page - 1) * $perPage;
        $total = $this->db->fetchColumn("SELECT COUNT(*) FROM subscriptions s WHERE {$where}", $params);

        $subs = $this->db->fetchAll("
            SELECT s.*, 
                   p.name as plan_name, 
                   p.price as plan_price, 
                   p.badge as plan_badge,
                   m.business_name, 
                   u.email as merchant_email, 
                   u.name as merchant_name
            FROM subscriptions s
            JOIN plans p ON p.id = s.plan_id
            JOIN merchants m ON m.id = s.merchant_id
            JOIN users u ON u.id = m.user_id
            WHERE {$where}
            ORDER BY s.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);

        return [
            'data'     => $subs,
            'total'    => (int)$total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int)ceil($total / $perPage),
        ];
    }
}
