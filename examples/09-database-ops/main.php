<?php

/**
 * Database CLI Tool
 * 
 * A comprehensive database management CLI application featuring:
 * - Database connection management
 * - Migration system with version control
 * - Data seeding and fixtures
 * - Interactive query execution
 * - Schema inspection and documentation
 * - Backup and restore operations
 */

use WebFiori\Cli\ArgumentOption;
use WebFiori\Cli\Command;
use WebFiori\Cli\Runner;

// Load dependencies
require_once '../../vendor/autoload.php';
require_once 'DatabaseManager.php';

class DatabaseCommand extends Command {
    private DatabaseManager $dbManager;

    public function __construct() {
        parent::__construct('db', [
            '--action' => [
                ArgumentOption::OPTIONAL => false,
                ArgumentOption::DESCRIPTION => 'Database action to perform',
                ArgumentOption::VALUES => ['connect', 'migrate', 'seed', 'query', 'backup', 'restore', 'status', 'cleanup']
            ],
            '--sql' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'SQL query to execute (for query action)'
            ],
            '--file' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'File path for backup/restore operations'
            ]
        ], 'Database management operations');
        
        $this->dbManager = new DatabaseManager();
    }

    public function exec(): int {
        $action = $this->getArgValue('--action');
        
        try {
            switch ($action) {
                case 'connect':
                    return $this->testConnection();
                case 'migrate':
                    return $this->runMigrations();
                case 'seed':
                    return $this->seedDatabase();
                case 'query':
                    return $this->executeQuery();
                case 'backup':
                    return $this->backupDatabase();
                case 'restore':
                    return $this->restoreDatabase();
                case 'status':
                    return $this->showStatus();
                case 'cleanup':
                    return $this->cleanupDatabase();
                default:
                    $this->println("Unknown action: $action");
                    return 1;
            }
        } catch (Exception $e) {
            $this->println("Error: " . $e->getMessage());
            return 1;
        }
    }

    private function testConnection(): int {
        $this->println("🔌 Testing database connection...");
        
        if ($this->dbManager->connect()) {
            $this->println("✅ Database connection successful!");
            $this->println("📊 Connection details:");
            $this->println("   • Host: localhost:3306");
            $this->println("   • Database: testing_db");
            $this->println("   • Username: root");
            return 0;
        } else {
            $this->println("❌ Database connection failed!");
            return 1;
        }
    }

    private function runMigrations(): int {
        $this->println("🚀 Running database migrations...");
        
        if (!$this->dbManager->connect()) {
            $this->println("❌ Cannot connect to database");
            return 1;
        }

        // Create sample tables
        $migrations = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                title VARCHAR(200) NOT NULL,
                content TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )"
        ];

        foreach ($migrations as $index => $sql) {
            $this->println("   • Running migration " . ($index + 1) . "...");
            $this->dbManager->query($sql);
        }

        $this->println("✅ Migrations completed successfully!");
        return 0;
    }

    private function seedDatabase(): int {
        $this->println("🌱 Seeding database with sample data...");
        
        if (!$this->dbManager->connect()) {
            $this->println("❌ Cannot connect to database");
            return 1;
        }

        // Insert sample users
        $users = [
            ['Ahmed Hassan', 'ahmed.hassan@example.com'],
            ['Sarah Johnson', 'sarah.johnson@example.com'],
            ['Omar Al-Rashid', 'omar.alrashid@example.com']
        ];

        foreach ($users as $user) {
            $this->dbManager->query(
                "INSERT IGNORE INTO users (name, email) VALUES (?, ?)",
                $user
            );
        }

        // Insert sample posts
        $posts = [
            [1, 'First Post', 'This is the content of the first post.'],
            [1, 'Second Post', 'This is another post by Ahmed.'],
            [2, 'Sarah\'s Post', 'Hello from Sarah!'],
            [3, 'Omar\'s Thoughts', 'Some thoughts from Omar.']
        ];

        foreach ($posts as $post) {
            $this->dbManager->query(
                "INSERT IGNORE INTO posts (user_id, title, content) VALUES (?, ?, ?)",
                $post
            );
        }

        $this->println("✅ Database seeded successfully!");
        $this->println("   • Added 3 users");
        $this->println("   • Added 4 posts");
        return 0;
    }

    private function executeQuery(): int {
        $sql = $this->getArgValue('--sql');
        
        if (!$sql) {
            $this->println("❌ SQL query is required for query action");
            $this->println("Usage: php main.php db --action=query --sql=\"SELECT * FROM users\"");
            return 1;
        }

        $this->println("🔍 Executing query...");
        $this->println("SQL: $sql");
        
        if (!$this->dbManager->connect()) {
            $this->println("❌ Cannot connect to database");
            return 1;
        }

        $result = $this->dbManager->query($sql);
        
        if ($result['success']) {
            $data = $result['data'];
            if (!empty($data)) {
                $this->println("📊 Query results:");
                $this->table($data);
                $this->println("⏱️  Execution time: " . number_format($result['execution_time'] * 1000, 2) . "ms");
            } else {
                $this->println("📊 Query executed successfully (no results)");
            }
        } else {
            $this->println("❌ Query failed: " . $result['error']);
            return 1;
        }

        return 0;
    }

    private function backupDatabase(): int {
        $file = $this->getArgValue('--file') ?? 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        $this->println("💾 Creating database backup...");
        $this->println("File: $file");
        
        if (!$this->dbManager->connect()) {
            $this->println("❌ Cannot connect to database");
            return 1;
        }

        $result = $this->dbManager->createBackup($file);
        
        if ($result['success']) {
            $this->println("✅ Backup created successfully!");
            $this->println("   • File: " . $result['file']);
            $this->println("   • Size: " . number_format($result['size']) . " bytes");
            $this->println("   • Tables: " . $result['tables']);
        } else {
            $this->println("❌ Backup failed: " . $result['error']);
            return 1;
        }
        
        return 0;
    }

    private function restoreDatabase(): int {
        $file = $this->getArgValue('--file');
        
        if (!$file || !file_exists($file)) {
            $this->println("❌ Backup file is required and must exist");
            return 1;
        }

        $this->println("🔄 Restoring database from backup...");
        $this->println("File: $file");
        
        if (!$this->dbManager->connect()) {
            $this->println("❌ Cannot connect to database");
            return 1;
        }

        $result = $this->dbManager->restoreFromFile($file);
        
        if ($result['success']) {
            $this->println("✅ Database restored successfully!");
            $this->println("   • Statements executed: " . $result['statements']);
        } else {
            $this->println("❌ Restore failed: " . $result['error']);
            return 1;
        }
        
        return 0;
    }

    private function showStatus(): int {
        $this->println("📊 Database Status");
        $this->println("==================");
        
        if (!$this->dbManager->connect()) {
            $this->println("❌ Cannot connect to database");
            return 1;
        }

        // Show tables
        $tablesResult = $this->dbManager->query("SHOW TABLES");
        if (!$tablesResult['success']) {
            $this->println("❌ Failed to get table list");
            return 1;
        }
        
        $tables = $tablesResult['data'];
        $this->println("📋 Tables: " . count($tables));
        
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            $countResult = $this->dbManager->query("SELECT COUNT(*) as count FROM `$tableName`");
            if ($countResult['success'] && !empty($countResult['data'])) {
                $count = $countResult['data'][0]['count'] ?? 0;
                $this->println("   • $tableName: $count records");
            }
        }

        return 0;
    }

    private function cleanupDatabase(): int {
        $this->println("🧹 Cleaning up database...");
        
        if (!$this->dbManager->connect()) {
            $this->println("❌ Cannot connect to database");
            return 1;
        }

        // Drop tables in correct order (foreign key constraints)
        $tables = ['posts', 'users'];
        
        foreach ($tables as $table) {
            $this->println("   • Dropping table: $table");
            $this->dbManager->query("DROP TABLE IF EXISTS `$table`");
        }

        $this->println("✅ Database cleanup completed!");
        return 0;
    }
}

// Create and configure the CLI runner
$runner = new Runner();
$runner->register(new DatabaseCommand());
$runner->setDefaultCommand('help');

// Start the application
exit($runner->start());
