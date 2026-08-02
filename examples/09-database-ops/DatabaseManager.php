<?php

/**
 * Database Manager - Core database functionality and connection management.
 * 
 * This class provides:
 * - Database connection management
 * - Query execution and result formatting
 * - Migration tracking and execution
 * - Schema inspection and documentation
 * - Backup and restore operations
 */
class DatabaseManager {
    private array $config = [];

    private ?PDO $connection = null;
    private array $executedQueries = [];
    private string $migrationsPath;
    private string $seedsPath;

    public function __construct(string $basePath = __DIR__) {
        $this->migrationsPath = $basePath.'/migrations';
        $this->seedsPath = $basePath.'/seeds';
        $this->loadConfig();
    }

    /**
     * Connect to database.
     */
    public function connect(array $config = null): bool {
        if ($config) {
            $this->config = array_merge($this->config, $config);
        }

        try {
            $dsn = $this->buildDsn();
            $this->connection = new PDO(
                $dsn,
                $this->config['username'] ?? '',
                $this->config['password'] ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            return true;
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: ".$e->getMessage());
        }
    }

    /**
     * Create database backup.
     */
    public function createBackup(string $outputPath = null): array {
        $this->ensureConnected();

        if (!$outputPath) {
            $timestamp = date('Y-m-d_H-i-s');
            $outputPath = "backup_{$timestamp}.sql";
        }

        $tables = $this->getTables();
        $backup = [];

        // Add header
        $backup[] = "-- Database Backup";
        $backup[] = "-- Generated: ".date('Y-m-d H:i:s');
        $backup[] = "-- Database: ".($this->config['database'] ?? 'unknown');
        $backup[] = "";

        foreach ($tables as $table) {
            $tableName = $table['name'];

            // Skip migrations table
            if ($tableName === 'migrations') {
                continue;
            }

            $backup[] = "-- Table: $tableName";
            $backup[] = "DROP TABLE IF EXISTS `$tableName`;";

            // Get CREATE TABLE statement
            $createResult = $this->query("SHOW CREATE TABLE `$tableName`");

            if ($createResult['success'] && !empty($createResult['data'])) {
                $createStatement = $createResult['data'][0]['Create Table'] ?? '';
                $backup[] = $createStatement.";";
            }

            // Get table data
            $dataResult = $this->query("SELECT * FROM `$tableName`");

            if ($dataResult['success'] && !empty($dataResult['data'])) {
                $backup[] = "";

                foreach ($dataResult['data'] as $row) {
                    $values = array_map(function ($value) {
                        return $value === null ? 'NULL' : "'".addslashes($value)."'";
                    }, array_values($row));

                    $columns = '`'.implode('`, `', array_keys($row)).'`';
                    $backup[] = "INSERT INTO `$tableName` ($columns) VALUES (".implode(', ', $values).");";
                }
            }

            $backup[] = "";
        }

        $backupContent = implode("\n", $backup);

        if (file_put_contents($outputPath, $backupContent) !== false) {
            return [
                'success' => true,
                'file' => $outputPath,
                'size' => strlen($backupContent),
                'tables' => count($tables)
            ];
        } else {
            return [
                'success' => false,
                'error' => "Failed to write backup file: $outputPath"
            ];
        }
    }

    /**
     * Get list of available migrations.
     */
    public function getAvailableMigrations(): array {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }

        $files = glob($this->migrationsPath.'/*.sql');
        $migrations = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $migrations[] = [
                'filename' => $filename,
                'path' => $file,
                'name' => pathinfo($filename, PATHINFO_FILENAME),
                'size' => filesize($file),
                'modified' => filemtime($file)
            ];
        }

        // Sort by filename (which should include version numbers)
        usort($migrations, fn($a, $b) => strcmp($a['filename'], $b['filename']));

        return $migrations;
    }

    /**
     * Get connection status information.
     */
    public function getConnectionStatus(): array {
        if (!$this->isConnected()) {
            return [
                'connected' => false,
                'error' => 'Not connected to database'
            ];
        }

        try {
            $stmt = $this->connection->query('SELECT VERSION() as version');
            $result = $stmt->fetch();

            return [
                'connected' => true,
                'host' => $this->config['host'] ?? 'unknown',
                'database' => $this->config['database'] ?? 'unknown',
                'version' => $result['version'] ?? 'unknown',
                'driver' => $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME)
            ];
        } catch (PDOException $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get executed migrations.
     */
    public function getExecutedMigrations(): array {
        $this->ensureConnected();
        $this->ensureMigrationsTable();

        $result = $this->query('SELECT * FROM migrations ORDER BY executed_at ASC');

        return $result['success'] ? $result['data'] : [];
    }

    /**
     * Get database schema information.
     */
    public function getSchema(): array {
        $this->ensureConnected();

        $tables = $this->getTables();
        $schema = [
            'database' => $this->config['database'] ?? 'unknown',
            'tables' => [],
            'total_tables' => count($tables),
            'total_size' => 0
        ];

        foreach ($tables as $table) {
            $tableInfo = $this->getTableInfo($table['name']);
            $schema['tables'][] = $tableInfo;
            $schema['total_size'] += $tableInfo['size_bytes'] ?? 0;
        }

        return $schema;
    }

    /**
     * Get table columns.
     */
    public function getTableColumns(string $tableName): array {
        $this->ensureConnected();

        $driver = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);

        switch ($driver) {
            case 'mysql':
                $sql = "DESCRIBE `$tableName`";
                break;
            case 'pgsql':
                $sql = "SELECT column_name, data_type, is_nullable 
                        FROM information_schema.columns 
                        WHERE table_name = '$tableName'";
                break;
            case 'sqlite':
                $sql = "PRAGMA table_info($tableName)";
                break;
            default:
                return [];
        }

        $result = $this->query($sql);

        return $result['success'] ? $result['data'] : [];
    }

    /**
     * Get detailed table information.
     */
    public function getTableInfo(string $tableName): array {
        $this->ensureConnected();

        $driver = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);

        // Get column information
        $columns = $this->getTableColumns($tableName);

        // Get row count
        $countResult = $this->query("SELECT COUNT(*) as count FROM `$tableName`");
        $rowCount = $countResult['success'] ? $countResult['data'][0]['count'] : 0;

        // Get table size (MySQL specific)
        $sizeBytes = 0;

        if ($driver === 'mysql') {
            $sizeResult = $this->query(
                "SELECT (data_length + index_length) as size_bytes 
                 FROM information_schema.tables 
                 WHERE table_schema = ? AND table_name = ?",
                [$this->config['database'], $tableName]
            );

            if ($sizeResult['success'] && !empty($sizeResult['data'])) {
                $sizeBytes = $sizeResult['data'][0]['size_bytes'] ?? 0;
            }
        }

        return [
            'name' => $tableName,
            'columns' => $columns,
            'column_count' => count($columns),
            'row_count' => $rowCount,
            'size_bytes' => $sizeBytes,
            'size_human' => $this->formatBytes($sizeBytes)
        ];
    }

    /**
     * Get list of tables.
     */
    public function getTables(): array {
        $this->ensureConnected();

        $driver = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);

        switch ($driver) {
            case 'mysql':
                $sql = 'SHOW TABLES';
                break;
            case 'pgsql':
                $sql = "SELECT tablename as table_name FROM pg_tables WHERE schemaname = 'public'";
                break;
            case 'sqlite':
                $sql = "SELECT name as table_name FROM sqlite_master WHERE type='table'";
                break;
            default:
                throw new Exception("Unsupported database driver: $driver");
        }

        $result = $this->query($sql);

        if (!$result['success']) {
            return [];
        }

        $tables = [];

        foreach ($result['data'] as $row) {
            $tableName = array_values($row)[0]; // Get first column value
            $tables[] = ['name' => $tableName];
        }

        return $tables;
    }

    /**
     * Check if connected to database.
     */
    public function isConnected(): bool {
        return $this->connection !== null;
    }

    /**
     * Execute SQL query.
     */
    public function query(string $sql, array $params = []): array {
        $this->ensureConnected();

        $startTime = microtime(true);

        try {
            if (empty($params)) {
                $stmt = $this->connection->query($sql);
            } else {
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($params);
            }

            $executionTime = microtime(true) - $startTime;

            // Record query for history
            $this->executedQueries[] = [
                'sql' => $sql,
                'params' => $params,
                'execution_time' => $executionTime,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $results = $stmt->fetchAll();

            return [
                'success' => true,
                'data' => $results,
                'row_count' => $stmt->rowCount(),
                'execution_time' => $executionTime,
                'affected_rows' => $stmt->rowCount()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'sql' => $sql,
                'execution_time' => microtime(true) - $startTime
            ];
        }
    }

    /**
     * Run migration.
     */
    public function runMigration(string $filename): array {
        $this->ensureConnected();
        $this->ensureMigrationsTable();

        $migrationPath = $this->migrationsPath.'/'.$filename;

        if (!file_exists($migrationPath)) {
            return [
                'success' => false,
                'error' => "Migration file not found: $filename"
            ];
        }

        // Check if already executed
        $result = $this->query('SELECT COUNT(*) as count FROM migrations WHERE filename = ?', [$filename]);

        if ($result['success'] && $result['data'][0]['count'] > 0) {
            return [
                'success' => false,
                'error' => "Migration already executed: $filename"
            ];
        }

        // Read and execute migration
        $sql = file_get_contents($migrationPath);
        $statements = $this->splitSqlStatements($sql);

        $this->connection->beginTransaction();

        try {
            foreach ($statements as $statement) {
                if (trim($statement)) {
                    $this->connection->exec($statement);
                }
            }

            // Record migration
            $this->query(
                'INSERT INTO migrations (filename, executed_at) VALUES (?, ?)',
                [$filename, date('Y-m-d H:i:s')]
            );

            $this->connection->commit();

            return [
                'success' => true,
                'message' => "Migration executed successfully: $filename"
            ];
        } catch (PDOException $e) {
            $this->connection->rollBack();

            return [
                'success' => false,
                'error' => "Migration failed: ".$e->getMessage()
            ];
        }
    }

    /**
     * Seed database with test data.
     */
    public function seedTable(string $tableName, string $seedFile = null): array {
        $this->ensureConnected();

        if (!$seedFile) {
            $seedFile = $this->seedsPath."/{$tableName}.json";
        }

        if (!file_exists($seedFile)) {
            return [
                'success' => false,
                'error' => "Seed file not found: $seedFile"
            ];
        }

        $seedData = json_decode(file_get_contents($seedFile), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error' => "Invalid JSON in seed file: ".json_last_error_msg()
            ];
        }

        if (empty($seedData)) {
            return [
                'success' => false,
                'error' => "No data found in seed file"
            ];
        }

        $inserted = 0;
        $errors = [];

        foreach ($seedData as $record) {
            $columns = array_keys($record);
            $placeholders = array_fill(0, count($columns), '?');

            $sql = "INSERT INTO `$tableName` (`".implode('`, `', $columns)."`) VALUES (".implode(', ', $placeholders).")";

            $result = $this->query($sql, array_values($record));

            if ($result['success']) {
                $inserted++;
            } else {
                $errors[] = $result['error'];
            }
        }

        return [
            'success' => empty($errors),
            'inserted' => $inserted,
            'total' => count($seedData),
            'errors' => $errors
        ];
    }

    /**
     * Build DSN string from config.
     */
    private function buildDsn(): string {
        $driver = $this->config['driver'] ?? 'mysql';
        $host = $this->config['host'] ?? 'localhost';
        $port = $this->config['port'] ?? 3306;
        $database = $this->config['database'] ?? '';

        return "$driver:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    }

    /**
     * Ensure database connection exists.
     */
    private function ensureConnected(): void {
        if (!$this->isConnected()) {
            throw new Exception('Not connected to database. Call connect() first.');
        }
    }

    /**
     * Ensure migrations table exists.
     */
    private function ensureMigrationsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->connection->exec($sql);
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return sprintf('%.1f %s', $bytes, $units[$unitIndex]);
    }

    /**
     * Load database configuration.
     */
    private function loadConfig(): void {
        $this->config = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'testing_db',
            'username' => 'root',
            'password' => '123456'
        ];
    }

    /**
     * Split SQL into individual statements.
     */
    private function splitSqlStatements(string $sql): array {
        // Simple split by semicolon (could be improved for complex cases)
        $statements = explode(';', $sql);

        return array_filter(array_map('trim', $statements));
    }
}
