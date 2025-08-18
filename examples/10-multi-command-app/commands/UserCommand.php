<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\Option;

/**
 * User management command with full CRUD operations.
 * 
 * This command demonstrates:
 * - Complete user lifecycle management
 * - Data validation and sanitization
 * - Multiple output formats
 * - Batch operations
 * - Search and filtering
 */
class UserCommand extends Command {
    private AppManager $app;

    public function __construct() {
        parent::__construct('user', [
            '--action' => [
                Option::DESCRIPTION => 'Action to perform',
                Option::OPTIONAL => false,
                Option::VALUES => ['list', 'create', 'update', 'delete', 'search', 'export']
            ],
            '--id' => [
                Option::DESCRIPTION => 'User ID for update/delete operations',
                Option::OPTIONAL => true
            ],
            '--name' => [
                Option::DESCRIPTION => 'User full name',
                Option::OPTIONAL => true
            ],
            '--email' => [
                Option::DESCRIPTION => 'User email address',
                Option::OPTIONAL => true
            ],
            '--status' => [
                Option::DESCRIPTION => 'User status',
                Option::OPTIONAL => true,
                Option::VALUES => ['active', 'inactive']
            ],
            '--format' => [
                Option::DESCRIPTION => 'Output format',
                Option::OPTIONAL => true,
                Option::DEFAULT => 'table',
                Option::VALUES => ['table', 'json', 'csv', 'xml']
            ],
            '--search' => [
                Option::DESCRIPTION => 'Search term for filtering users',
                Option::OPTIONAL => true
            ],
            '--limit' => [
                Option::DESCRIPTION => 'Maximum number of results',
                Option::OPTIONAL => true,
                Option::DEFAULT => '50'
            ],
            '--batch' => [
                Option::DESCRIPTION => 'Enable batch mode for bulk operations',
                Option::OPTIONAL => true
            ],
            '--file' => [
                Option::DESCRIPTION => 'File path for batch operations or export',
                Option::OPTIONAL => true
            ]
        ], 'User management operations (list, create, update, delete, search, export)');

        $this->app = new AppManager();
    }

    public function exec(): int {
        $action = $this->getArgValue('--action');

        try {
            return match ($action) {
                'list' => $this->listUsers(),
                'create' => $this->createUser(),
                'update' => $this->updateUser(),
                'delete' => $this->deleteUser(),
                'search' => $this->searchUsers(),
                'export' => $this->exportUsers(),
                default => $this->showUsage()
            };
        } catch (Exception $e) {
            $this->error("Operation failed: ".$e->getMessage());
            $this->app->log('error', "User command failed: ".$e->getMessage());

            return 1;
        }
    }

    /**
     * Create a new user.
     */
    private function createUser(): int {
        if ($this->isArgProvided('--batch')) {
            return $this->createUsersBatch();
        }

        $name = $this->getArgValue('--name');
        $email = $this->getArgValue('--email');
        $status = $this->getArgValue('--status') ?? 'active';

        // Interactive input if not provided
        if (!$name) {
            $name = $this->getInput('Enter user name: ');
        }

        if (!$email) {
            $email = $this->getInput('Enter user email: ');
        }

        // Validate input
        $errors = $this->app->validateData([
            'name' => $name,
            'email' => $email,
            'status' => $status
        ], [
            'name' => ['required' => true, 'min_length' => 2, 'max_length' => 100],
            'email' => ['required' => true, 'email' => true],
            'status' => ['required' => true]
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed:');

            foreach ($errors as $field => $error) {
                $this->println("  • $error");
            }

            return 1;
        }

        // Check for duplicate email
        $users = $this->app->loadData('users');

        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $this->error("User with email '$email' already exists.");

                return 1;
            }
        }

        // Create user
        $newUser = [
            'id' => $this->generateUserId($users),
            'name' => $name,
            'email' => $email,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $users[] = $newUser;

        if ($this->app->saveData('users', $users)) {
            $this->success("✅ User created successfully!");
            $this->displayUserInfo($newUser);

            return 0;
        } else {
            $this->error("Failed to save user data.");

            return 1;
        }
    }

    /**
     * Create users in batch mode.
     */
    private function createUsersBatch(): int {
        $file = $this->getArgValue('--file');

        if (!$file) {
            $this->error('File path is required for batch operations.');

            return 1;
        }

        if (!file_exists($file)) {
            $this->error("File not found: $file");

            return 1;
        }

        $this->info("📥 Processing batch file: $file");

        // Read and parse file (assuming CSV format)
        $content = file_get_contents($file);
        $lines = array_filter(array_map('trim', explode("\n", $content)));

        if (empty($lines)) {
            $this->error('File is empty or invalid.');

            return 1;
        }

        // Parse CSV
        $header = str_getcsv(array_shift($lines));
        $batchUsers = [];

        foreach ($lines as $line) {
            $data = str_getcsv($line);

            if (count($data) === count($header)) {
                $batchUsers[] = array_combine($header, $data);
            }
        }

        if (empty($batchUsers)) {
            $this->error('No valid user data found in file.');

            return 1;
        }

        $this->info("Found ".count($batchUsers)." users to create");

        $users = $this->app->loadData('users');
        $created = 0;
        $errors = 0;

        $this->withProgressBar($batchUsers, function ($userData) use (&$users, &$created, &$errors) {
            // Validate user data
            $validationErrors = $this->app->validateData($userData, [
                'name' => ['required' => true, 'min_length' => 2],
                'email' => ['required' => true, 'email' => true]
            ]);

            if (!empty($validationErrors)) {
                $errors++;

                return;
            }

            // Check for duplicate email
            foreach ($users as $user) {
                if ($user['email'] === $userData['email']) {
                    $errors++;

                    return;
                }
            }

            // Create user
            $newUser = [
                'id' => $this->generateUserId($users),
                'name' => $userData['name'],
                'email' => $userData['email'],
                'status' => $userData['status'] ?? 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $users[] = $newUser;
            $created++;
        }, 'Creating users...');

        if ($this->app->saveData('users', $users)) {
            $this->success("✅ Batch operation completed!");
            $this->info("📊 Summary:");
            $this->println("   • Created: $created users");

            if ($errors > 0) {
                $this->println("   • Errors: $errors users");
            }

            return 0;
        } else {
            $this->error("Failed to save user data.");

            return 1;
        }
    }

    /**
     * Delete a user.
     */
    private function deleteUser(): int {
        $id = (int)$this->getArgValue('--id');

        if (!$id) {
            $this->error('User ID is required for delete operation.');

            return 1;
        }

        $users = $this->app->loadData('users');
        $userIndex = $this->findUserIndex($users, $id);

        if ($userIndex === -1) {
            $this->error("User with ID $id not found.");

            return 1;
        }

        $user = $users[$userIndex];
        $this->warning("⚠️  You are about to delete user: {$user['name']} ({$user['email']})");

        if (!$this->confirm('Are you sure you want to delete this user?', false)) {
            $this->info('Delete operation cancelled.');

            return 0;
        }

        array_splice($users, $userIndex, 1);

        if ($this->app->saveData('users', $users)) {
            $this->success("✅ User deleted successfully!");

            return 0;
        } else {
            $this->error("Failed to save user data.");

            return 1;
        }
    }

    /**
     * Display individual user information.
     */
    private function displayUserInfo(array $user): void {
        $this->println();
        $this->info("👤 User Information:");
        $this->println("   • ID: {$user['id']}");
        $this->println("   • Name: {$user['name']}");
        $this->println("   • Email: {$user['email']}");
        $this->println("   • Status: ".ucfirst($user['status']));
        $this->println("   • Created: {$user['created_at']}");
        $this->println("   • Updated: {$user['updated_at']}");
    }

    /**
     * Display users in table format.
     */
    private function displayUsersTable(array $users): void {
        // Table header
        $this->prints('┌────┬─────────────────────┬─────────────────────────┬─────────────┬─────────────┐', ['color' => 'blue']);
        $this->println();

        $this->prints('│', ['color' => 'blue']);
        $this->prints(' ID ', ['bold' => true]);
        $this->prints('│', ['color' => 'blue']);
        $this->prints(' Name                ', ['bold' => true]);
        $this->prints('│', ['color' => 'blue']);
        $this->prints(' Email                   ', ['bold' => true]);
        $this->prints('│', ['color' => 'blue']);
        $this->prints(' Status      ', ['bold' => true]);
        $this->prints('│', ['color' => 'blue']);
        $this->prints(' Created     ', ['bold' => true]);
        $this->prints('│', ['color' => 'blue']);
        $this->println();

        $this->prints('├────┼─────────────────────┼─────────────────────────┼─────────────┼─────────────┤', ['color' => 'blue']);
        $this->println();

        // Table rows
        foreach ($users as $user) {
            $this->prints('│', ['color' => 'blue']);
            $this->prints(' '.str_pad($user['id'], 2).' ');
            $this->prints('│', ['color' => 'blue']);
            $this->prints(' '.str_pad(substr($user['name'], 0, 19), 19).' ');
            $this->prints('│', ['color' => 'blue']);
            $this->prints(' '.str_pad(substr($user['email'], 0, 23), 23).' ');
            $this->prints('│', ['color' => 'blue']);

            $statusColor = $user['status'] === 'active' ? 'green' : 'red';
            $this->prints(' '.str_pad(ucfirst($user['status']), 11).' ', ['color' => $statusColor]);

            $this->prints('│', ['color' => 'blue']);
            $this->prints(' '.str_pad(substr($user['created_at'], 0, 10), 11).' ');
            $this->prints('│', ['color' => 'blue']);
            $this->println();
        }

        $this->prints('└────┴─────────────────────┴─────────────────────────┴─────────────┴─────────────┘', ['color' => 'blue']);
        $this->println();
    }

    /**
     * Export users to file.
     */
    private function exportUsers(): int {
        $format = $this->getArgValue('--format') ?? 'json';
        $file = $this->getArgValue('--file');

        $users = $this->app->loadData('users');

        if (empty($users)) {
            $this->warning('No users to export.');

            return 0;
        }

        if (!$file) {
            $timestamp = date('Y-m-d_H-i-s');
            $file = "users_export_{$timestamp}.{$format}";
        }

        $this->info("📤 Exporting ".count($users)." users to $file");

        // Show progress for large exports
        if (count($users) > 10) {
            $this->withProgressBar($users, function ($user) {
                usleep(10000); // Simulate processing time
            }, 'Preparing export...');
        }

        $content = $this->app->formatData($users, $format);

        if (file_put_contents($file, $content) !== false) {
            $this->success("✅ Export completed successfully!");
            $this->info("📋 Export Summary:");
            $this->println("   • Format: ".strtoupper($format));
            $this->println("   • Records: ".count($users));
            $this->println("   • File Size: ".$this->formatBytes(strlen($content)));
            $this->println("   • Location: $file");

            return 0;
        } else {
            $this->error("Failed to write export file: $file");

            return 1;
        }
    }

    /**
     * Find user index by ID.
     */
    private function findUserIndex(array $users, int $id): int {
        foreach ($users as $index => $user) {
            if ($user['id'] == $id) {
                return $index;
            }
        }

        return -1;
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return sprintf('%.1f %s', $bytes, $units[$unitIndex]);
    }

    /**
     * Generate unique user ID.
     */
    private function generateUserId(array $users): int {
        if (empty($users)) {
            return 1;
        }

        $maxId = max(array_column($users, 'id'));

        return $maxId + 1;
    }

    /**
     * List all users.
     */
    private function listUsers(): int {
        $users = $this->app->loadData('users');
        $format = $this->getArgValue('--format') ?? 'table';
        $limit = (int)($this->getArgValue('--limit') ?? 50);

        if (empty($users)) {
            $this->warning('No users found.');

            return 0;
        }

        // Apply limit
        $users = array_slice($users, 0, $limit);

        $this->info("👥 User Management - List Users");
        $this->println();

        if ($format === 'table') {
            $this->displayUsersTable($users);
        } else {
            $output = $this->app->formatData($users, $format);
            $this->println($output);
        }

        $this->showUserStats($users);

        return 0;
    }

    /**
     * Search users.
     */
    private function searchUsers(): int {
        $searchTerm = $this->getArgValue('--search');
        $format = $this->getArgValue('--format') ?? 'table';

        if (!$searchTerm) {
            $searchTerm = $this->getInput('Enter search term: ');
        }

        $users = $this->app->loadData('users');
        $filteredUsers = array_filter($users, function ($user) use ($searchTerm) {
            return stripos($user['name'], $searchTerm) !== false ||
                   stripos($user['email'], $searchTerm) !== false ||
                   stripos($user['status'], $searchTerm) !== false;
        });

        $this->info("🔍 Search Results for: '$searchTerm'");
        $this->println();

        if (empty($filteredUsers)) {
            $this->warning('No users found matching the search criteria.');

            return 0;
        }

        if ($format === 'table') {
            $this->displayUsersTable($filteredUsers);
        } else {
            $output = $this->app->formatData(array_values($filteredUsers), $format);
            $this->println($output);
        }

        $this->info("Found ".count($filteredUsers)." user(s) matching '$searchTerm'");

        return 0;
    }

    /**
     * Show command usage.
     */
    private function showUsage(): int {
        $this->info('User Management Command Usage:');
        $this->println();
        $this->println('Examples:');
        $this->println('  php main.php user --action=list');
        $this->println('  php main.php user --action=create --name="John Doe" --email="john@example.com"');
        $this->println('  php main.php user --action=update --id=1 --name="Jane Doe"');
        $this->println('  php main.php user --action=delete --id=1');
        $this->println('  php main.php user --action=search --search="john"');
        $this->println('  php main.php user --action=export --format=json');

        return 0;
    }

    /**
     * Display user statistics.
     */
    private function showUserStats(array $users): void {
        $total = count($users);
        $active = count(array_filter($users, fn($u) => $u['status'] === 'active'));
        $inactive = $total - $active;

        $this->println();
        $this->info("📊 Total: $total users | Active: $active | Inactive: $inactive");
    }

    /**
     * Update an existing user.
     */
    private function updateUser(): int {
        $id = (int)$this->getArgValue('--id');

        if (!$id) {
            $this->error('User ID is required for update operation.');

            return 1;
        }

        $users = $this->app->loadData('users');
        $userIndex = $this->findUserIndex($users, $id);

        if ($userIndex === -1) {
            $this->error("User with ID $id not found.");

            return 1;
        }

        $user = $users[$userIndex];
        $this->info("Updating user: {$user['name']} ({$user['email']})");

        // Update fields if provided
        $name = $this->getArgValue('--name');
        $email = $this->getArgValue('--email');
        $status = $this->getArgValue('--status');

        if ($name) {
            $user['name'] = $name;
        }

        if ($email) {
            $user['email'] = $email;
        }

        if ($status) {
            $user['status'] = $status;
        }

        $user['updated_at'] = date('Y-m-d H:i:s');

        // Validate updated data
        $errors = $this->app->validateData($user, [
            'name' => ['required' => true, 'min_length' => 2, 'max_length' => 100],
            'email' => ['required' => true, 'email' => true],
            'status' => ['required' => true]
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed:');

            foreach ($errors as $field => $error) {
                $this->println("  • $error");
            }

            return 1;
        }

        // Check for duplicate email (excluding current user)
        foreach ($users as $index => $existingUser) {
            if ($index !== $userIndex && $existingUser['email'] === $user['email']) {
                $this->error("Another user with email '{$user['email']}' already exists.");

                return 1;
            }
        }

        $users[$userIndex] = $user;

        if ($this->app->saveData('users', $users)) {
            $this->success("✅ User updated successfully!");
            $this->displayUserInfo($user);

            return 0;
        } else {
            $this->error("Failed to save user data.");

            return 1;
        }
    }
}
