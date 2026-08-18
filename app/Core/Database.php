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
