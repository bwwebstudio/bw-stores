<?php

namespace App\Models;

use App\Core\Database;

/**
 * Merchant Model
 * 
 * Handles all database operations for the merchants table.
 * Every query involving merchant data must scope to the correct merchant.
 */
class Merchant
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance() ?? throw new \RuntimeException('Database not initialized');
    }

    /**
     * Find a merchant by ID.
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM merchants WHERE id = ?", [$id]);
    }

    /**
     * Find a merchant by user ID.
     */
    public function findByUserId(int $userId): ?array
    {
        return $this->db->fetchOne("SELECT * FROM merchants WHERE user_id = ?", [$userId]);
    }

    /**
     * Create a new merchant record.
     */
    public function create(array $data): int
    {
        return $this->db->insert('merchants', [
            'user_id'           => $data['user_id'],
            'business_name'     => $data['business_name'] ?? null,
            'business_category' => $data['business_category'] ?? null,
            'status'            => 'pending',
        ]);
    }

    /**
     * Update merchant profile.
     */
    public function update(int $id, array $data): bool
    {
        $allowed = [
            'business_name', 'business_category', 'onboarding_completed',
            'onboarding_step', 'status', 'suspension_reason', 'suspended_at'
        ];
        $updateData = array_intersect_key($data, array_flip($allowed));

        if (empty($updateData)) {
            return false;
        }

        $rows = $this->db->update('merchants', $updateData, 'id = ?', [$id]);
        return $rows > 0;
    }

    /**
     * Complete onboarding for a merchant.
     */
    public function completeOnboarding(int $id): bool
    {
        return $this->update($id, [
            'onboarding_completed' => 1,
            'status' => 'active',
        ]);
    }

    /**
     * Update onboarding step.
     */
    public function updateOnboardingStep(int $id, int $step): bool
    {
        return $this->update($id, ['onboarding_step' => $step]);
    }

    /**
     * Suspend a merchant.
     */
    public function suspend(int $id, string $reason): bool
    {
        return $this->update($id, [
            'status'            => 'suspended',
            'suspension_reason' => $reason,
            'suspended_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Activate a merchant (restore from suspension).
     */
    public function activate(int $id): bool
    {
        return $this->update($id, [
            'status'            => 'active',
            'suspension_reason' => null,
            'suspended_at'      => null,
        ]);
    }

    /**
     * Get merchant with user data (joined).
     */
    public function findWithUser(int $id): ?array
    {
        return $this->db->fetchOne("
            SELECT m.*, u.name, u.email, u.mobile, u.last_login_at, u.email_verified_at, u.is_active as user_active
            FROM merchants m
            JOIN users u ON u.id = m.user_id
            WHERE m.id = ?
        ", [$id]);
    }

    /**
     * Get all merchants with pagination (admin use).
     */
    public function getAllPaginated(int $page = 1, int $perPage = 20, ?string $status = null): array
    {
        $where = '1=1';
        $params = [];

        if ($status) {
            $where .= ' AND m.status = ?';
            $params[] = $status;
        }

        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM merchants m WHERE {$where}",
            $params
        );

        $merchants = $this->db->fetchAll("
            SELECT m.*, u.name, u.email, u.mobile, u.last_login_at
            FROM merchants m
            JOIN users u ON u.id = m.user_id
            WHERE {$where}
            ORDER BY m.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);

        return [
            'data'     => $merchants,
            'total'    => (int) $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Count merchants by status.
     */
    public function countByStatus(?string $status = null): int
    {
        if ($status) {
            return $this->db->count('merchants', 'status = ?', [$status]);
        }
        return $this->db->count('merchants');
    }
}
