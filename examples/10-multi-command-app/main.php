<?php

/**
 * Multi-Command CLI Application
 * 
 * This is a comprehensive CLI application demonstrating:
 * - Multiple command organization
 * - Configuration management
 * - Data persistence and CRUD operations
 * - User management system
 * - Export/import functionality
 * - System monitoring and maintenance
 * - Comprehensive error handling and logging
 */

use WebFiori\Cli\Commands\HelpCommand;
use WebFiori\Cli\Runner;

// Load dependencies
require_once '../../vendor/autoload.php';
require_once 'AppManager.php';
require_once 'commands/UserCommand.php';

// Create and configure the CLI runner
$runner = new Runner();

// Register core commands
$runner->register(new HelpCommand());

// Register application commands
$runner->register(new UserCommand());

// Set default command
$runner->setDefaultCommand('help');

// Initialize application
$app = new AppManager();
$app->log('info', 'Application started');

// Start the application
$exitCode = $runner->start();

// Log application shutdown
$app->log('info', "Application finished with exit code: $exitCode");

exit($exitCode);
