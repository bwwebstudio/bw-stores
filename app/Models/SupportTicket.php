<?php

namespace App\Models;

use App\Core\Database;

class SupportTicket
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function getAllForMerchant(int $merchantId): array
    {
        return $this->db->fetchAll("
            SELECT st.*,
                   (SELECT COUNT(*) FROM support_messages WHERE ticket_id = st.id) as message_count,
                   (SELECT MAX(created_at) FROM support_messages WHERE ticket_id = st.id) as last_reply_at
            FROM support_tickets st
            WHERE st.merchant_id = ?
            ORDER BY st.created_at DESC
        ", [$merchantId]);
    }

    public function getAllForAdmin(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND st.status = ?";
            $params[] = $filters['status'];
        }

        $offset = ($page - 1) * $perPage;
        $total = $this->db->fetchColumn("SELECT COUNT(*) FROM support_tickets st WHERE {$where}", $params);

        $tickets = $this->db->fetchAll("
            SELECT st.*, m.business_name, u.name as user_name, u.email as user_email
            FROM support_tickets st
            JOIN merchants m ON m.id = st.merchant_id
            JOIN users u ON u.id = st.user_id
            WHERE {$where}
            ORDER BY st.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $params);

        return [
            'data'     => $tickets,
            'total'    => (int)$total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int)ceil($total / $perPage),
        ];
    }

    public function findByIdAndMerchant(int $id, int $merchantId): ?array
    {
        $ticket = $this->db->fetchOne("
            SELECT st.*, u.name as user_name, u.email as user_email
            FROM support_tickets st
            JOIN users u ON u.id = st.user_id
            WHERE st.id = ? AND st.merchant_id = ?
        ", [$id, $merchantId]);

        if ($ticket) {
            $ticket['messages'] = $this->getMessages($ticket['id']);
        }
        return $ticket;
    }

    public function findByIdForAdmin(int $id): ?array
    {
        $ticket = $this->db->fetchOne("
            SELECT st.*, m.business_name, u.name as user_name, u.email as user_email
            FROM support_tickets st
            JOIN merchants m ON m.id = st.merchant_id
            JOIN users u ON u.id = st.user_id
            WHERE st.id = ?
        ", [$id]);

        if ($ticket) {
            $ticket['messages'] = $this->getMessages($ticket['id']);
        }
        return $ticket;
    }

    public function getMessages(int $ticketId): array
    {
        return $this->db->fetchAll("
            SELECT sm.*, u.name as sender_name, u.role as sender_role
            FROM support_messages sm
            JOIN users u ON u.id = sm.user_id
            WHERE sm.ticket_id = ?
            ORDER BY sm.created_at ASC
        ", [$ticketId]);
    }

    public function createTicket(int $merchantId, int $userId, array $data): int
    {
        $ticketNumber = 'TICK-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        return $this->db->transaction(function (Database $db) use ($merchantId, $userId, $data, $ticketNumber) {
            $ticketId = $db->insert('support_tickets', [
                'merchant_id'   => $merchantId,
                'user_id'       => $userId,
                'ticket_number' => $ticketNumber,
                'subject'       => $data['subject'],
                'category'      => $data['category'] ?? 'General',
                'priority'      => $data['priority'] ?? 'medium',
                'status'        => 'OPEN',
            ]);

            $db->insert('support_messages', [
                'ticket_id'   => $ticketId,
                'user_id'     => $userId,
                'sender_type' => 'merchant',
                'message'     => $data['message'],
            ]);

            return $ticketId;
        });
    }

    public function addMessage(int $ticketId, int $userId, string $senderType, string $message): int
    {
        $msgId = $this->db->insert('support_messages', [
            'ticket_id'   => $ticketId,
            'user_id'     => $userId,
            'sender_type' => $senderType,
            'message'     => $message,
        ]);

        $newStatus = $senderType === 'admin' ? 'WAITING' : 'IN_PROGRESS';
        $this->db->update('support_tickets', ['status' => $newStatus], 'id = ?', [$ticketId]);

        return $msgId;
    }

    public function updateStatus(int $ticketId, string $status): bool
    {
        return $this->db->update('support_tickets', ['status' => $status], 'id = ?', [$ticketId]) >= 0;
    }
}
