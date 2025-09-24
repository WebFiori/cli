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
 * - Performance monitoring and optimization
 */

use WebFiori\Cli\Commands\HelpCommand;
use WebFiori\Cli\Runner;

// Load dependencies
require_once '../../vendor/autoload.php';
require_once 'DatabaseManager.php';

// Create and configure the CLI runner
$runner = new Runner();

// Register core commands
$runner->register(new HelpCommand());

// Initialize database manager
$dbManager = new DatabaseManager();

// Set default command
$runner->setDefaultCommand('help');

// Start the application
exit($runner->start());
