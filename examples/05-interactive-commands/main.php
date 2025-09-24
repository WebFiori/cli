<?php

/**
 * Interactive Commands CLI Application
 * 
 * This application demonstrates complex interactive CLI workflows including:
 * - Multi-level menu systems with navigation
 * - Step-by-step wizards with validation
 * - Interactive games and entertainment
 * - State management and user experience
 * - Dynamic command flows and error recovery
 */

use WebFiori\Cli\Commands\HelpCommand;
use WebFiori\Cli\Runner;

// Load dependencies
require_once '../../vendor/autoload.php';
require_once 'InteractiveMenuCommand.php';

// Create and configure the CLI runner
$runner = new Runner();

// Register commands
$runner->register(new HelpCommand());
$runner->register(new InteractiveMenuCommand());

// Set default command
$runner->setDefaultCommand('help');

// Start the application
exit($runner->start());
