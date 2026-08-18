<?php

namespace App\Models;

use App\Core\Database;

class Announcement
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function getActive(): array
    {
        return $this->db->fetchAll("
            SELECT * FROM announcements WHERE is_active = 1 ORDER BY created_at DESC
        ");
    }

    public function getAll(): array
    {
        return $this->db->fetchAll("
            SELECT * FROM announcements ORDER BY created_at DESC
        ");
    }

    public function create(array $data): int
    {
        return $this->db->insert('announcements', [
            'title'     => $data['title'],
            'message'   => $data['message'],
            'type'      => $data['type'] ?? 'info',
            'is_active' => !empty($data['is_active']) ? 1 : 0,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->update('announcements', [
            'title'     => $data['title'],
            'message'   => $data['message'],
            'type'      => $data['type'] ?? 'info',
            'is_active' => !empty($data['is_active']) ? 1 : 0,
        ], 'id = ?', [$id]) >= 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->delete('announcements', 'id = ?', [$id]) > 0;
    }
}
