<?php

namespace App\Models;

use App\Core\Database;

class Notification
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function getUnreadForMerchant(int $merchantId): array
    {
        return $this->db->fetchAll("
            SELECT * FROM notifications
            WHERE merchant_id = ?
            ORDER BY created_at DESC
            LIMIT 10
        ", [$merchantId]);
    }

    public function markAllAsRead(int $merchantId): bool
    {
        return $this->db->update('notifications', ['is_read' => 1], 'merchant_id = ?', [$merchantId]) >= 0;
    }
}
