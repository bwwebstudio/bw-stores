<?php

/**
 * Migration Runner
 * 
 * Tracks and executes database migrations in order.
 * Usage: php database/migrate.php [command]
 * Commands: migrate, rollback, fresh, seed, status
 */

// Bootstrap the application for CLI usage
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

$config = require BASE_PATH . '/config/database.php';

// Connect to database
try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['name'],
        $config['charset']
    );

    $pdo = new PDO($dsn, $config['user'], $config['password'], $config['options']);
    echo "✓ Connected to database: {$config['name']}\n";
} catch (PDOException $e) {
    // If unknown database error (1049), try to connect without dbname and create it
    if ($e->getCode() == 1049 || str_contains($e->getMessage(), 'Unknown database')) {
        try {
            $dsnWithoutDb = sprintf(
                'mysql:host=%s;port=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['charset']
            );
            $pdo = new PDO($dsnWithoutDb, $config['user'], $config['password'], $config['options']);
            $dbName = $config['name'];
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$dbName}`");
            echo "✓ Created and connected to database: {$dbName}\n";
        } catch (PDOException $e2) {
            echo "✗ Database connection failed: " . $e2->getMessage() . "\n";
            exit(1);
        }
    } else {
        echo "✗ Database connection failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Create migrations tracking table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL UNIQUE,
        batch INT NOT NULL,
        migrated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// Get command
$command = $argv[1] ?? 'migrate';

switch ($command) {
    case 'migrate':
        runMigrations($pdo);
        break;
    case 'rollback':
        rollbackMigrations($pdo);
        break;
    case 'fresh':
        freshMigrations($pdo);
        break;
    case 'seed':
        runSeeders($pdo);
        break;
    case 'status':
        showStatus($pdo);
        break;
    default:
        echo "Unknown command: {$command}\n";
        echo "Available commands: migrate, rollback, fresh, seed, status\n";
        exit(1);
}

/**
 * Run pending migrations.
 */
function runMigrations(PDO $pdo): void
{
    $migrationsDir = BASE_PATH . '/database/migrations';
    $files = glob($migrationsDir . '/*.php');
    sort($files);

    // Get already-run migrations
    $run = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
    $batch = (int) ($pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn() ?: 0) + 1;

    $migrated = 0;

    foreach ($files as $file) {
        $name = basename($file, '.php');

        if (in_array($name, $run)) {
            continue;
        }

        echo "Migrating: {$name}... ";

        $migration = require $file;

        if (isset($migration['up'])) {
            try {
                $pdo->exec($migration['up']);

                $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                $stmt->execute([$name, $batch]);

                echo "✓\n";
                $migrated++;
            } catch (PDOException $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
                exit(1);
            }
        }
    }

    if ($migrated === 0) {
        echo "Nothing to migrate.\n";
    } else {
        echo "\n✓ Migrated {$migrated} migration(s).\n";
    }
}

/**
 * Rollback the last batch of migrations.
 */
function rollbackMigrations(PDO $pdo): void
{
    $batch = (int) $pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn();

    if ($batch === 0) {
        echo "Nothing to rollback.\n";
        return;
    }

    $stmt = $pdo->prepare("SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC");
    $stmt->execute([$batch]);
    $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($migrations as $name) {
        $file = BASE_PATH . '/database/migrations/' . $name . '.php';

        if (!file_exists($file)) {
            echo "Migration file not found: {$name}\n";
            continue;
        }

        echo "Rolling back: {$name}... ";

        $migration = require $file;

        if (isset($migration['down'])) {
            try {
                $pdo->exec($migration['down']);

                $stmt2 = $pdo->prepare("DELETE FROM migrations WHERE migration = ?");
                $stmt2->execute([$name]);

                echo "✓\n";
            } catch (PDOException $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
}

/**
 * Drop all tables and re-run all migrations.
 */
function freshMigrations(PDO $pdo): void
{
    echo "⚠ Dropping all tables...\n";

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `{$table}`");
        echo "  Dropped: {$table}\n";
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Re-create migrations table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            batch INT NOT NULL,
            migrated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "\n";
    runMigrations($pdo);
}

/**
 * Run database seeders.
 */
function runSeeders(PDO $pdo): void
{
    $seedersDir = BASE_PATH . '/database/seeders';
    $files = glob($seedersDir . '/*.php');
    sort($files);

    foreach ($files as $file) {
        $name = basename($file, '.php');
        echo "Seeding: {$name}... ";

        $seeder = require $file;

        if (is_callable($seeder)) {
            try {
                $seeder($pdo);
                echo "✓\n";
            } catch (PDOException $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\n✓ Seeding complete.\n";
}

/**
 * Show migration status.
 */
function showStatus(PDO $pdo): void
{
    $migrationsDir = BASE_PATH . '/database/migrations';
    $files = glob($migrationsDir . '/*.php');
    sort($files);

    $run = $pdo->query("SELECT migration, batch, migrated_at FROM migrations ORDER BY id")
        ->fetchAll(PDO::FETCH_ASSOC);

    $runMap = [];
    foreach ($run as $r) {
        $runMap[$r['migration']] = $r;
    }

    echo "\n Migration Status\n";
    echo str_repeat('─', 70) . "\n";
    printf(" %-45s %-8s %-15s\n", "Migration", "Batch", "Status");
    echo str_repeat('─', 70) . "\n";

    foreach ($files as $file) {
        $name = basename($file, '.php');
        if (isset($runMap[$name])) {
            $batch = $runMap[$name]['batch'];
            $status = "✓ Migrated";
        } else {
            $batch = '-';
            $status = "○ Pending";
        }
        printf(" %-45s %-8s %-15s\n", $name, $batch, $status);
    }

    echo str_repeat('─', 70) . "\n\n";
}
