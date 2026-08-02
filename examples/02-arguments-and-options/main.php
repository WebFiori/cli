<?php

/**
 * Arguments and Options CLI Application
 * 
 * This application demonstrates comprehensive argument handling including:
 * - Required vs optional arguments
 * - Default values and constraints
 * - Data validation and type conversion
 * - Boolean flags and complex data types
 * - Error handling and user feedback
 */

use WebFiori\Cli\Runner;

// Load dependencies
require_once '../../vendor/autoload.php';
require_once 'CalculatorCommand.php';
require_once 'UserProfileCommand.php';

// Create and configure the CLI runner
$runner = new Runner();

// Register commands
$runner->register(new CalculatorCommand());
$runner->register(new UserProfileCommand());

// Set default command

// Start the application
exit($runner->start());
