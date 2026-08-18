<?php

namespace App\Services;

use App\Core\Application;
use App\Core\Database;

/**
 * Audit Service
 * 
 * Logs important actions for security and compliance.
 * Never logs passwords or secret credentials.
 */
class AuditService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Application::getInstance()->getDatabase();
    }

    /**
     * Log an audit event.
     */
    public function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            // Strip sensitive fields from values
            $oldValues = $this->sanitizeValues($oldValues);
            $newValues = $this->sanitizeValues($newValues);

            $this->db->insert('audit_logs', [
                'user_id'     => current_user_id(),
                'action'      => $action,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'description' => $description,
                'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent'  => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
                'old_values'  => $oldValues ? json_encode($oldValues) : null,
                'new_values'  => $newValues ? json_encode($newValues) : null,
            ]);
        } catch (\Throwable $e) {
            // Never let audit logging break the application
            app_log("Audit log failed: {$action} - " . $e->getMessage(), 'ERROR');
        }
    }

    /**
     * Get audit logs with pagination.
     */
    public function getAll(int $page = 1, int $perPage = 50, ?int $userId = null, ?string $action = null): array
    {
        $where = '1=1';
        $params = [];

        if ($userId) {
            $where .= ' AND al.user_id = ?';
            $params[] = $userId;
        }

        if ($action) {
            $where .= ' AND al.action = ?';
            $params[] = $action;
        }

        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM audit_logs al WHERE {$where}",
            $params
        );

        $logs = $this->db->fetchAll("
            SELECT al.*, u.name as user_name, u.email as user_email
            FROM audit_logs al
            LEFT JOIN users u ON u.id = al.user_id
            WHERE {$where}
            ORDER BY al.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);

        return [
            'data'     => $logs,
            'total'    => (int) $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Remove sensitive fields from logged values.
     */
    private function sanitizeValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $sensitiveKeys = [
            'password', 'password_hash', 'password_confirmation',
            'secret', 'api_key', 'api_secret', 'token',
            'payment_key', 'payment_secret', 'webhook_secret',
            'reset_token', 'verification_token', '_csrf_token',
        ];

        foreach ($sensitiveKeys as $key) {
            if (isset($values[$key])) {
                $values[$key] = '***REDACTED***';
            }
        }

        return $values;
    }
}
