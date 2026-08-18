<?php

namespace App\Models;

use App\Core\Database;

/**
 * User Model
 * 
 * Handles all database operations for the users table.
 */
class User
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance() ?? throw new \RuntimeException('Database not initialized');
    }

    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne("SELECT * FROM users WHERE email = ?", [strtolower($email)]);
    }

    /**
     * Find a user by verification token.
     */
    public function findByVerificationToken(string $token): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE verification_token = ? AND email_verified_at IS NULL",
            [$token]
        );
    }

    /**
     * Find a user by reset token.
     */
    public function findByResetToken(string $token): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE reset_token = ? AND reset_token_expires > NOW()",
            [$token]
        );
    }

    /**
     * Create a new user.
     */
    public function create(array $data): int
    {
        return $this->db->insert('users', [
            'email'              => strtolower($data['email']),
            'password_hash'      => hash_password($data['password']),
            'name'               => $data['name'],
            'mobile'             => $data['mobile'] ?? null,
            'role'               => $data['role'] ?? 'merchant',
            'verification_token' => $data['verification_token'] ?? generate_token(),
        ]);
    }

    /**
     * Update a user's password.
     */
    public function updatePassword(int $id, string $newPassword): bool
    {
        $rows = $this->db->update('users', [
            'password_hash' => hash_password($newPassword),
            'reset_token'   => null,
            'reset_token_expires' => null,
        ], 'id = ?', [$id]);

        return $rows > 0;
    }

    /**
     * Verify a user's password.
     */
    public function verifyPassword(array $user, string $password): bool
    {
        return verify_password($password, $user['password_hash']);
    }

    /**
     * Mark email as verified.
     */
    public function verifyEmail(int $id): bool
    {
        $rows = $this->db->update('users', [
            'email_verified_at'  => date('Y-m-d H:i:s'),
            'verification_token' => null,
        ], 'id = ?', [$id]);

        return $rows > 0;
    }

    /**
     * Set a password reset token.
     */
    public function setResetToken(int $id, string $token): bool
    {
        $rows = $this->db->update('users', [
            'reset_token'         => $token,
            'reset_token_expires' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ], 'id = ?', [$id]);

        return $rows > 0;
    }

    /**
     * Increment login attempts.
     */
    public function incrementLoginAttempts(int $id): void
    {
        $this->db->query(
            "UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?",
            [$id]
        );

        // Lock account after 5 failed attempts
        $user = $this->findById($id);
        if ($user && $user['login_attempts'] >= 5) {
            $this->db->update('users', [
                'locked_until' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
            ], 'id = ?', [$id]);
        }
    }

    /**
     * Reset login attempts (on successful login).
     */
    public function resetLoginAttempts(int $id): void
    {
        $this->db->update('users', [
            'login_attempts' => 0,
            'locked_until'   => null,
            'last_login_at'  => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
    }

    /**
     * Check if user account is locked.
     */
    public function isLocked(array $user): bool
    {
        if (empty($user['locked_until'])) {
            return false;
        }

        return strtotime($user['locked_until']) > time();
    }

    /**
     * Check if email already exists.
     */
    public function emailExists(string $email): bool
    {
        return $this->db->exists('users', 'email = ?', [strtolower($email)]);
    }

    /**
     * Update user profile.
     */
    public function update(int $id, array $data): bool
    {
        $allowed = ['name', 'mobile', 'is_active'];
        $updateData = array_intersect_key($data, array_flip($allowed));

        if (empty($updateData)) {
            return false;
        }

        $rows = $this->db->update('users', $updateData, 'id = ?', [$id]);
        return $rows > 0;
    }
}
