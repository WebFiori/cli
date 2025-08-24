<?php

/**
 * Application Manager - Core application logic and data management.
 * 
 * This class provides:
 * - Configuration management
 * - Data persistence
 * - Logging functionality
 * - Application state management
 * - Utility methods for commands
 */
class AppManager {
    private string $basePath;

    private array $config = [];
    private string $configPath;
    private string $dataPath;
    private array $logs = [];

    public function __construct(string $basePath = __DIR__) {
        $this->basePath = $basePath;
        $this->configPath = $basePath.'/config';
        $this->dataPath = $basePath.'/data';

        $this->ensureDirectories();
        $this->loadConfiguration();
    }

    /**
     * Create a backup of data.
     */
    public function createBackup(string $destination = null): string {
        $destination = $destination ?? $this->basePath.'/backups';

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = $destination."/backup_{$timestamp}.json";

        $backupData = [
            'timestamp' => date('c'),
            'version' => $this->getConfig('app.version'),
            'data' => [
                'users' => $this->loadData('users'),
                'config' => $this->config
            ]
        ];

        $content = json_encode($backupData, JSON_PRETTY_PRINT);
        file_put_contents($backupFile, $content);

        $this->log('info', "Backup created: {$backupFile}");

        return $backupFile;
    }

    /**
     * Format data for output.
     */
    public function formatData(array $data, string $format): string {
        switch (strtolower($format)) {
            case 'json':
                return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            case 'csv':
                if (empty($data)) {
                    return '';
                }

                $output = '';
                $headers = array_keys($data[0]);
                $output .= implode(',', $headers)."\n";

                foreach ($data as $row) {
                    $values = array_map(function ($value) {
                        return '"'.str_replace('"', '""', $value).'"';
                    }, array_values($row));
                    $output .= implode(',', $values)."\n";
                }

                return $output;

            case 'xml':
                $xml = new SimpleXMLElement('<data/>');

                foreach ($data as $item) {
                    $record = $xml->addChild('record');

                    foreach ($item as $key => $value) {
                        $record->addChild($key, htmlspecialchars($value));
                    }
                }

                return $xml->asXML();

            default:
                return print_r($data, true);
        }
    }

    /**
     * Get configuration value(s).
     */
    public function getConfig(string $key = null) {
        if ($key === null) {
            return $this->config;
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Get recent logs.
     */
    public function getLogs(int $limit = 100): array {
        return array_slice($this->logs, -$limit);
    }

    /**
     * Get application statistics.
     */
    public function getStats(): array {
        $users = $this->loadData('users');

        return [
            'users' => [
                'total' => count($users),
                'active' => count(array_filter($users, fn($u) => $u['status'] === 'active')),
                'inactive' => count(array_filter($users, fn($u) => $u['status'] === 'inactive'))
            ],
            'storage' => [
                'data_size' => $this->getDirectorySize($this->dataPath),
                'config_size' => $this->getDirectorySize($this->configPath),
                'free_space' => disk_free_space($this->basePath)
            ],
            'logs' => [
                'total_entries' => count($this->logs),
                'errors' => count(array_filter($this->logs, fn($l) => $l['level'] === 'ERROR')),
                'warnings' => count(array_filter($this->logs, fn($l) => $l['level'] === 'WARNING'))
            ]
        ];
    }

    /**
     * Load data from storage.
     */
    public function loadData(string $type): array {
        $filePath = $this->dataPath."/{$type}.json";

        if (!file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log('error', "Failed to load {$type} data: ".json_last_error_msg());

            return [];
        }

        return $data ?? [];
    }

    /**
     * Log a message.
     */
    public function log(string $level, string $message): void {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = [
            'timestamp' => $timestamp,
            'level' => strtoupper($level),
            'message' => $message
        ];

        $this->logs[] = $logEntry;

        // Also write to file if configured
        if ($this->getConfig('logging.file_enabled')) {
            $this->writeLogToFile($logEntry);
        }
    }

    /**
     * Restore from backup.
     */
    public function restoreBackup(string $backupFile): bool {
        if (!file_exists($backupFile)) {
            $this->log('error', "Backup file not found: {$backupFile}");

            return false;
        }

        $content = file_get_contents($backupFile);
        $backupData = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log('error', "Invalid backup file format");

            return false;
        }

        // Restore data
        foreach ($backupData['data'] as $type => $data) {
            if ($type === 'config') {
                $this->config = $data;
                $this->saveConfiguration();
            } else {
                $this->saveData($type, $data);
            }
        }

        $this->log('info', "Restored from backup: {$backupFile}");

        return true;
    }

    /**
     * Save data to storage.
     */
    public function saveData(string $type, array $data): bool {
        $filePath = $this->dataPath."/{$type}.json";

        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log('error', "Failed to encode {$type} data: ".json_last_error_msg());

            return false;
        }

        $result = file_put_contents($filePath, $content);

        if ($result === false) {
            $this->log('error', "Failed to save {$type} data to {$filePath}");

            return false;
        }

        $this->log('info', "Saved {$type} data (".count($data)." records)");

        return true;
    }

    /**
     * Set configuration value.
     */
    public function setConfig(string $key, $value): void {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
        $this->saveConfiguration();
    }

    /**
     * Validate data against rules.
     */
    public function validateData(array $data, array $rules): array {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;

            // Required check
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors[$field] = "Field {$field} is required";
                continue;
            }

            if (empty($value)) {
                continue; // Skip validation for empty optional fields
            }

            // Type check
            if (isset($rule['type'])) {
                if (!$this->validateType($value, $rule['type'])) {
                    $errors[$field] = "Field {$field} must be of type {$rule['type']}";
                    continue;
                }
            }

            // Length check
            if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
                $errors[$field] = "Field {$field} must be at least {$rule['min_length']} characters";
            }

            if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                $errors[$field] = "Field {$field} must not exceed {$rule['max_length']} characters";
            }

            // Email validation
            if (isset($rule['email']) && $rule['email'] && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Field {$field} must be a valid email address";
            }

            // Custom validation
            if (isset($rule['validator']) && is_callable($rule['validator'])) {
                $result = $rule['validator']($value);

                if ($result !== true) {
                    $errors[$field] = $result;
                }
            }
        }

        return $errors;
    }

    /**
     * Ensure required directories exist.
     */
    private function ensureDirectories(): void {
        $directories = [$this->configPath, $this->dataPath, $this->dataPath.'/logs'];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Get directory size in bytes.
     */
    private function getDirectorySize(string $directory): int {
        $size = 0;

        if (is_dir($directory)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    /**
     * Load configuration from files.
     */
    private function loadConfiguration(): void {
        $configFiles = ['app.json', 'database.json'];

        foreach ($configFiles as $file) {
            $filePath = $this->configPath.'/'.$file;

            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $config = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->config = array_merge($this->config, $config);
                }
            }
        }

        // Set defaults if not configured
        $this->setDefaults();
    }

    /**
     * Save configuration to file.
     */
    private function saveConfiguration(): void {
        $appConfig = [
            'app' => $this->config['app'] ?? [],
            'logging' => $this->config['logging'] ?? []
        ];

        $dbConfig = [
            'database' => $this->config['database'] ?? []
        ];

        file_put_contents(
            $this->configPath.'/app.json',
            json_encode($appConfig, JSON_PRETTY_PRINT)
        );

        file_put_contents(
            $this->configPath.'/database.json',
            json_encode($dbConfig, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Set default configuration values.
     */
    private function setDefaults(): void {
        $defaults = [
            'app' => [
                'name' => 'MyApp',
                'version' => '1.0.0',
                'environment' => 'development',
                'debug' => true
            ],
            'database' => [
                'type' => 'json',
                'path' => $this->dataPath
            ],
            'logging' => [
                'level' => 'info',
                'file_enabled' => true
            ]
        ];

        foreach ($defaults as $section => $values) {
            if (!isset($this->config[$section])) {
                $this->config[$section] = [];
            }

            foreach ($values as $key => $value) {
                if (!isset($this->config[$section][$key])) {
                    $this->config[$section][$key] = $value;
                }
            }
        }
    }

    /**
     * Validate data type.
     */
    private function validateType($value, string $type): bool {
        return match ($type) {
            'string' => is_string($value),
            'int', 'integer' => is_int($value) || (is_string($value) && ctype_digit($value)),
            'float', 'double' => is_float($value) || is_numeric($value),
            'bool', 'boolean' => is_bool($value) || in_array(strtolower($value), ['true', 'false', '1', '0']),
            'array' => is_array($value),
            default => true
        };
    }

    /**
     * Write log entry to file.
     */
    private function writeLogToFile(array $logEntry): void {
        $logFile = $this->dataPath.'/logs/app.log';
        $line = "[{$logEntry['timestamp']}] {$logEntry['level']}: {$logEntry['message']}\n";
        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
