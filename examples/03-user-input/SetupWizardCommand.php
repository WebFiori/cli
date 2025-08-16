<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\Option;
use WebFiori\Cli\InputValidator;

/**
 * Setup wizard command demonstrating multi-step interactive workflows.
 * 
 * This command shows:
 * - Multi-step wizard interface
 * - Step navigation and progress tracking
 * - Configuration validation
 * - File generation simulation
 * - Error recovery and retry mechanisms
 */
class SetupWizardCommand extends Command {
    
    private array $config = [];
    private array $steps = [
        'basic' => 'Basic Configuration',
        'database' => 'Database Settings',
        'security' => 'Security Configuration',
        'features' => 'Feature Selection'
    ];
    
    public function __construct() {
        parent::__construct('setup', [
            '--step' => [
                Option::DESCRIPTION => 'Start from specific step (basic, database, security, features)',
                Option::OPTIONAL => true,
                Option::VALUES => ['basic', 'database', 'security', 'features']
            ],
            '--config-file' => [
                Option::DESCRIPTION => 'Output configuration file path',
                Option::OPTIONAL => true,
                Option::DEFAULT => 'app-config.json'
            ]
        ], 'Interactive setup wizard for application configuration');
    }
    
    public function exec(): int {
        $this->println("🔧 Application Setup Wizard");
        $this->println("===========================");
        $this->println();
        
        $startStep = $this->getArgValue('--step') ?? 'basic';
        $configFile = $this->getArgValue('--config-file') ?? 'app-config.json';
        
        // Show wizard overview
        $this->showWizardOverview($startStep);
        
        // Execute steps
        $stepKeys = array_keys($this->steps);
        $startIndex = array_search($startStep, $stepKeys);
        
        for ($i = $startIndex; $i < count($stepKeys); $i++) {
            $stepKey = $stepKeys[$i];
            $stepNumber = $i + 1;
            $totalSteps = count($stepKeys);
            
            if (!$this->executeStep($stepKey, $stepNumber, $totalSteps)) {
                $this->error('Setup cancelled or failed.');
                return 1;
            }
            
            // Ask if user wants to continue (except for last step)
            if ($i < count($stepKeys) - 1) {
                if (!$this->confirm('Continue to next step?', true)) {
                    $this->warning('Setup paused. Run again with --step=' . $stepKeys[$i + 1] . ' to continue.');
                    return 0;
                }
                $this->println();
            }
        }
        
        // Complete setup
        $this->completeSetup($configFile);
        
        return 0;
    }
    
    /**
     * Show wizard overview.
     */
    private function showWizardOverview(string $startStep): void {
        $this->info("📋 Setup Steps:");
        
        $stepNumber = 1;
        foreach ($this->steps as $key => $title) {
            $icon = ($key === $startStep) ? '👉' : '  ';
            $this->println("$icon $stepNumber. $title");
            $stepNumber++;
        }
        
        $this->println();
        
        if ($startStep !== 'basic') {
            $this->warning("⚠️  Starting from step: " . $this->steps[$startStep]);
            $this->println();
        }
    }
    
    /**
     * Execute a specific setup step.
     */
    private function executeStep(string $stepKey, int $stepNumber, int $totalSteps): bool {
        $stepTitle = $this->steps[$stepKey];
        
        $this->success("Step $stepNumber/$totalSteps: $stepTitle");
        $this->println(str_repeat('-', strlen("Step $stepNumber/$totalSteps: $stepTitle")));
        
        switch ($stepKey) {
            case 'basic':
                return $this->setupBasicConfig();
            case 'database':
                return $this->setupDatabaseConfig();
            case 'security':
                return $this->setupSecurityConfig();
            case 'features':
                return $this->setupFeatures();
            default:
                $this->error("Unknown step: $stepKey");
                return false;
        }
    }
    
    /**
     * Setup basic configuration.
     */
    private function setupBasicConfig(): bool {
        $this->config['app_name'] = $this->getInput(
            '📝 Application name:',
            'MyApp',
            new InputValidator(function($input) {
                return preg_match('/^[A-Za-z0-9\s_-]+$/', $input) && strlen($input) >= 2;
            }, 'App name must be at least 2 characters and contain only letters, numbers, spaces, hyphens, and underscores')
        );
        
        $environments = ['development', 'staging', 'production'];
        $envIndex = $this->select('🌐 Environment:', $environments, 0);
        $this->config['environment'] = $environments[$envIndex];
        
        $this->config['debug'] = $this->confirm('🐛 Enable debug mode?', $this->config['environment'] === 'development');
        
        $this->config['app_url'] = $this->getInput(
            '🌍 Application URL:',
            'http://localhost:8000',
            new InputValidator(function($input) {
                return filter_var($input, FILTER_VALIDATE_URL) !== false;
            }, 'Please enter a valid URL')
        );
        
        $this->println();
        $this->info("✅ Basic configuration completed!");
        
        return true;
    }
    
    /**
     * Setup database configuration.
     */
    private function setupDatabaseConfig(): bool {
        $dbTypes = ['mysql', 'postgresql', 'sqlite', 'mongodb'];
        $dbIndex = $this->select('🗄️  Database type:', $dbTypes, 0);
        $this->config['db_type'] = $dbTypes[$dbIndex];
        
        if ($this->config['db_type'] !== 'sqlite') {
            $this->config['db_host'] = $this->getInput('🌐 Database host:', 'localhost');
            
            $this->config['db_port'] = $this->readInteger(
                '🔌 Database port:',
                $this->getDefaultPort($this->config['db_type'])
            );
            
            $this->config['db_name'] = $this->getInput(
                '📊 Database name:',
                strtolower(str_replace(' ', '_', $this->config['app_name'] ?? 'myapp'))
            );
            
            $this->config['db_username'] = $this->getInput('👤 Database username:', 'root');
            
            // Simulate password input (in real implementation, this would be hidden)
            $this->config['db_password'] = $this->getInput('🔑 Database password:', '');
            
            // Test connection (simulated)
            if ($this->confirm('🔍 Test database connection?', true)) {
                $this->testDatabaseConnection();
            }
        } else {
            $this->config['db_file'] = $this->getInput('📁 SQLite file path:', 'database.sqlite');
        }
        
        $this->println();
        $this->info("✅ Database configuration completed!");
        
        return true;
    }
    
    /**
     * Setup security configuration.
     */
    private function setupSecurityConfig(): bool {
        // Generate app key
        if ($this->confirm('🔐 Generate application key?', true)) {
            $this->config['app_key'] = $this->generateAppKey();
            $this->success("🔑 Application key generated!");
        }
        
        // JWT settings
        if ($this->confirm('🎫 Enable JWT authentication?', false)) {
            $this->config['jwt_enabled'] = true;
            $this->config['jwt_secret'] = $this->generateJwtSecret();
            
            $this->config['jwt_expiry'] = $this->readInteger('⏰ JWT token expiry (hours):', 24);
        }
        
        // CORS settings
        if ($this->confirm('🌐 Configure CORS?', false)) {
            $this->config['cors_enabled'] = true;
            $this->config['cors_origins'] = $this->getInput(
                '🔗 Allowed origins (comma-separated):',
                '*'
            );
        }
        
        // Rate limiting
        if ($this->confirm('⚡ Enable rate limiting?', true)) {
            $this->config['rate_limit_enabled'] = true;
            $this->config['rate_limit_requests'] = $this->readInteger('📊 Requests per minute:', 60);
        }
        
        $this->println();
        $this->info("✅ Security configuration completed!");
        
        return true;
    }
    
    /**
     * Setup feature selection.
     */
    private function setupFeatures(): bool {
        $this->info("🎯 Select features to enable:");
        
        $features = [
            'caching' => 'Caching System',
            'logging' => 'Advanced Logging',
            'monitoring' => 'Performance Monitoring',
            'backup' => 'Automated Backups',
            'notifications' => 'Email Notifications',
            'api_docs' => 'API Documentation',
            'testing' => 'Testing Framework'
        ];
        
        $this->config['features'] = [];
        
        foreach ($features as $key => $title) {
            if ($this->confirm("Enable $title?", in_array($key, ['caching', 'logging']))) {
                $this->config['features'][] = $key;
            }
        }
        
        // Feature-specific configuration
        if (in_array('caching', $this->config['features'])) {
            $cacheTypes = ['redis', 'memcached', 'file'];
            $cacheIndex = $this->select('💾 Cache driver:', $cacheTypes, 0);
            $this->config['cache_driver'] = $cacheTypes[$cacheIndex];
        }
        
        if (in_array('notifications', $this->config['features'])) {
            $this->config['smtp_host'] = $this->getInput('📧 SMTP host:', 'smtp.gmail.com');
            $this->config['smtp_port'] = $this->readInteger('📧 SMTP port:', 587);
        }
        
        $this->println();
        $this->info("✅ Feature selection completed!");
        
        return true;
    }
    
    /**
     * Complete the setup process.
     */
    private function completeSetup(string $configFile): void {
        $this->println();
        $this->success("🎉 Setup Wizard Completed!");
        $this->println("=========================");
        
        // Show configuration summary
        $this->showConfigSummary();
        
        // Save configuration
        if ($this->confirm("💾 Save configuration to $configFile?", true)) {
            $this->saveConfiguration($configFile);
        }
        
        // Show next steps
        $this->showNextSteps();
    }
    
    /**
     * Show configuration summary.
     */
    private function showConfigSummary(): void {
        $this->info("📋 Configuration Summary:");
        $this->println("• App Name: " . ($this->config['app_name'] ?? 'N/A'));
        $this->println("• Environment: " . ($this->config['environment'] ?? 'N/A'));
        $this->println("• Database: " . ($this->config['db_type'] ?? 'N/A'));
        $this->println("• Features: " . count($this->config['features'] ?? []));
        $this->println("• Security: " . (isset($this->config['app_key']) ? 'Configured' : 'Basic'));
        $this->println();
    }
    
    /**
     * Save configuration to file (simulated).
     */
    private function saveConfiguration(string $configFile): void {
        $this->info("💾 Saving configuration...");
        
        // Simulate file writing
        usleep(1000000); // 1 second
        
        $this->success("✅ Configuration saved to $configFile");
        $this->info("📁 File size: " . rand(2, 8) . " KB");
    }
    
    /**
     * Show next steps.
     */
    private function showNextSteps(): void {
        $this->info("🚀 Next Steps:");
        $this->println("1. Review the generated configuration file");
        $this->println("2. Set up your database schema");
        $this->println("3. Configure your web server");
        $this->println("4. Run initial tests");
        $this->println("5. Deploy your application");
        $this->println();
        $this->success("Happy coding! 🎉");
    }
    
    /**
     * Get default port for database type.
     */
    private function getDefaultPort(string $dbType): int {
        return match($dbType) {
            'mysql' => 3306,
            'postgresql' => 5432,
            'mongodb' => 27017,
            default => 3306
        };
    }
    
    /**
     * Test database connection (simulated).
     */
    private function testDatabaseConnection(): void {
        $this->info("🔍 Testing database connection...");
        
        // Simulate connection test
        usleep(2000000); // 2 seconds
        
        if (rand(0, 10) > 2) { // 80% success rate
            $this->success("✅ Database connection successful!");
        } else {
            $this->warning("⚠️  Connection test failed, but continuing setup...");
        }
    }
    
    /**
     * Generate application key.
     */
    private function generateAppKey(): string {
        return 'base64:' . base64_encode(random_bytes(32));
    }
    
    /**
     * Generate JWT secret.
     */
    private function generateJwtSecret(): string {
        return bin2hex(random_bytes(32));
    }
}
