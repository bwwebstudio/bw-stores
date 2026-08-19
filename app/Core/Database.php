<?php

namespace App\Core;

/**
 * Database
 * 
 * PDO database wrapper. Uses prepared statements exclusively.
 * Provides query builder helpers for common operations.
 */
class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
        self::$instance = $this;
    }

    /**
     * Get the singleton database instance.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            $app = Application::getInstance();
            if ($app !== null) {
                return $app->getDatabase();
            }
            $config = require BASE_PATH . '/config/database.php';
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * Establish the PDO connection.
     */
    private function connect(): void
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $this->config['host'],
                $this->config['port'],
                $this->config['name'],
                $this->config['charset']
            );

            $this->pdo = new \PDO(
                $dsn,
                $this->config['user'],
                $this->config['password'],
                $this->config['options']
            );
        } catch (\PDOException $e) {
            // If unknown database error (1049), try to connect without dbname and create it
            if ($e->getCode() == 1049 || str_contains($e->getMessage(), 'Unknown database')) {
                $dsnWithoutDb = sprintf(
                    'mysql:host=%s;port=%s;charset=%s',
                    $this->config['host'],
                    $this->config['port'],
                    $this->config['charset']
                );
                $this->pdo = new \PDO(
                    $dsnWithoutDb,
                    $this->config['user'],
                    $this->config['password'],
                    $this->config['options']
                );
                $dbName = $this->config['name'];
                $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $this->pdo->exec("USE `{$dbName}`");
            } else {
                throw $e;
            }
        }

        // Auto-initialize schema if users table does not exist
        $this->ensureSchema();
    }

    /**
     * Auto-initialize tables from schema.sql if empty, or repair legacy column names
     */
     private function ensureSchema(): void
     {
         try {
             $stmt = $this->pdo->query("SHOW TABLES LIKE 'users'");
             $usersExist = (bool)$stmt->fetch();

             $stmtPlans = $this->pdo->query("SHOW TABLES LIKE 'plans'");
             $plansExist = (bool)$stmtPlans->fetch();

             if (!$usersExist || !$plansExist) {
                 $schemaFile = defined('BASE_PATH') ? BASE_PATH . '/database/schema.sql' : dirname(__DIR__, 2) . '/database/schema.sql';
                 if (file_exists($schemaFile)) {
                     $sql = file_get_contents($schemaFile);
                     $this->executeSqlScript($sql);
                 }
             } else {
                 // Ensure default plans exist
                 $planCount = (int)$this->fetchColumn("SELECT COUNT(*) FROM plans");
                 if ($planCount === 0) {
                     $seederFile = defined('BASE_PATH') ? BASE_PATH . '/database/seeders/001_PlanSeeder.php' : dirname(__DIR__, 2) . '/database/seeders/001_PlanSeeder.php';
                     if (file_exists($seederFile)) {
                         $seeder = require $seederFile;
                         if (is_callable($seeder)) {
                             $seeder($this->pdo);
                         }
                     }
                 }

                 // Fix any legacy column mismatches if tables were created with an older schema
                 $this->fixLegacyColumns();
             }
         } catch (\Throwable $t) {
             if (function_exists('app_log')) {
                 app_log("ensureSchema note: " . $t->getMessage(), 'INFO');
             }
         }
     }

     /**
      * Automatically fix legacy column names and ENUMs if tables already existed
      */
     private function fixLegacyColumns(): void
     {
         try {
             // 1. Fix users.verification_token
             $cols = $this->pdo->query("SHOW COLUMNS FROM `users` LIKE 'verification_token'")->fetchAll();
             if (empty($cols)) {
                 $hasOld = $this->pdo->query("SHOW COLUMNS FROM `users` LIKE 'email_verify_token'")->fetchAll();
                 if (!empty($hasOld)) {
                     $this->pdo->exec("ALTER TABLE `users` CHANGE `email_verify_token` `verification_token` VARCHAR(64) NULL");
                 } else {
                     $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `verification_token` VARCHAR(64) NULL AFTER `email_verified_at`");
                 }
             }

             // 2. Fix users.reset_token
             $colsReset = $this->pdo->query("SHOW COLUMNS FROM `users` LIKE 'reset_token'")->fetchAll();
             if (empty($colsReset)) {
                 $hasOld = $this->pdo->query("SHOW COLUMNS FROM `users` LIKE 'password_reset_token'")->fetchAll();
                 if (!empty($hasOld)) {
                     $this->pdo->exec("ALTER TABLE `users` CHANGE `password_reset_token` `reset_token` VARCHAR(64) NULL");
                 } else {
                     $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `reset_token` VARCHAR(64) NULL AFTER `verification_token`");
                 }
             }

             // 3. Fix users.reset_token_expires
             $colsResetExp = $this->pdo->query("SHOW COLUMNS FROM `users` LIKE 'reset_token_expires'")->fetchAll();
             if (empty($colsResetExp)) {
                 $hasOld = $this->pdo->query("SHOW COLUMNS FROM `users` LIKE 'password_reset_expires_at'")->fetchAll();
                 if (!empty($hasOld)) {
                     $this->pdo->exec("ALTER TABLE `users` CHANGE `password_reset_expires_at` `reset_token_expires` DATETIME NULL");
                 } else {
                     $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `reset_token_expires` DATETIME NULL AFTER `reset_token`");
                 }
             }

             // 4. Fix users.login_attempts
             $colsAttempts = $this->pdo->query("SHOW COLUMNS FROM `users` LIKE 'login_attempts'")->fetchAll();
             if (empty($colsAttempts)) {
                 $hasOld = $this->pdo->query("SHOW COLUMNS FROM `users` LIKE 'failed_logins'")->fetchAll();
                 if (!empty($hasOld)) {
                     $this->pdo->exec("ALTER TABLE `users` CHANGE `failed_logins` `login_attempts` INT NOT NULL DEFAULT 0");
                 } else {
                     $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `login_attempts` INT NOT NULL DEFAULT 0");
                 }
             }

             // 5. Ensure subscriptions.status ENUM includes 'trialing'
             $this->pdo->exec("ALTER TABLE `subscriptions` MODIFY COLUMN `status` ENUM('pending', 'active', 'trialing', 'past_due', 'grace_period', 'cancelled', 'canceled', 'expired', 'suspended') NOT NULL DEFAULT 'pending'");

             // 6. Ensure audit_logs table exists
             $this->pdo->exec("
                 CREATE TABLE IF NOT EXISTS `audit_logs` (
                     `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
                     `user_id` INT NULL,
                     `action` VARCHAR(100) NOT NULL,
                     `entity_type` VARCHAR(50) NULL,
                     `entity_id` INT NULL,
                     `description` TEXT NULL,
                     `ip_address` VARCHAR(45) NULL,
                     `user_agent` VARCHAR(500) NULL,
                     `old_values` LONGTEXT NULL,
                     `new_values` LONGTEXT NULL,
                     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                     INDEX `idx_audit_user` (`user_id`),
                     INDEX `idx_audit_action` (`action`)
                 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
             ");

             // 7. Ensure payments table exists
             $this->pdo->exec("
                 CREATE TABLE IF NOT EXISTS `payments` (
                     `id` INT AUTO_INCREMENT PRIMARY KEY,
                     `merchant_id` INT NOT NULL,
                     `order_id` INT NOT NULL,
                     `amount` DECIMAL(10,2) NOT NULL,
                     `currency` VARCHAR(3) NOT NULL DEFAULT 'INR',
                     `status` ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
                     `gateway` VARCHAR(50) NOT NULL DEFAULT 'COD',
                     `gateway_payment_id` VARCHAR(255) NULL,
                     `gateway_response` LONGTEXT NULL,
                     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
             ");

             // 8. Ensure coupon_usages table exists
             $this->pdo->exec("
                 CREATE TABLE IF NOT EXISTS `coupon_usages` (
                     `id` INT AUTO_INCREMENT PRIMARY KEY,
                     `coupon_id` INT NOT NULL,
                     `order_id` INT NOT NULL,
                     `merchant_id` INT NOT NULL,
                     `customer_email` VARCHAR(255) NOT NULL,
                     `discount_amount` DECIMAL(10,2) NOT NULL,
                     `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
             ");
         } catch (\Throwable $t) {
             // Non-critical background repair
         }
     }

     /**
      * Execute a multi-statement SQL script safely
      */
     public function executeSqlScript(string $sql): void
     {
         try {
             $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
         } catch (\Throwable $e) {}

         // Try full execution first
         try {
             $this->pdo->exec($sql);
         } catch (\Throwable $e) {
             // Split statements by semicolon
             $lines = explode("\n", $sql);
             $currentStmt = '';
             foreach ($lines as $line) {
                 $trimmed = trim($line);
                 if (empty($trimmed) || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
                     continue;
                 }
                 $currentStmt .= ' ' . $line;
                 if (str_ends_with($trimmed, ';')) {
                     $stmtToRun = trim($currentStmt);
                     $currentStmt = '';
                     if (!empty($stmtToRun)) {
                         try {
                             $this->pdo->exec($stmtToRun);
                         } catch (\Throwable $se) {
                             // Ignore duplicate table/column or non-fatal statement errors
                         }
                     }
                 }
             }
         }

         try {
             $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
         } catch (\Throwable $e) {}
     }

    /**
     * Get the raw PDO instance.
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a prepared query and return the statement.
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row.
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch a single column value.
     */
    public function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }

    /**
     * Insert a row and return the last insert ID.
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update rows in a table.
     * Returns the number of affected rows.
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setClauses = [];
        $values = [];

        foreach ($data as $column => $value) {
            $setClauses[] = "{$column} = ?";
            $values[] = $value;
        }

        $setString = implode(', ', $setClauses);
        $sql = "UPDATE {$table} SET {$setString} WHERE {$where}";

        $stmt = $this->query($sql, array_merge($values, $whereParams));
        return $stmt->rowCount();
    }

    /**
     * Delete rows from a table.
     * Returns the number of affected rows.
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Count rows matching a condition.
     */
    public function count(string $table, string $where = '1=1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
        return (int) $this->fetchColumn($sql, $params);
    }

    /**
     * Check if a row exists.
     */
    public function exists(string $table, string $where, array $params = []): bool
    {
        return $this->count($table, $where, $params) > 0;
    }

    /**
     * Begin a database transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a database transaction.
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback a database transaction.
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Execute a callback within a transaction.
     * Automatically commits on success, rolls back on exception.
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Get the last insert ID.
     */
    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}
