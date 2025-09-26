<?php

/**
 * Output Formatting CLI Application
 * 
 * This application demonstrates advanced output formatting including:
 * - ANSI colors and text styling
 * - Table creation and formatting
 * - Progress bars and animations
 * - Layout techniques and visual elements
 * - Terminal cursor manipulation
 */

use WebFiori\Cli\Runner;

// Load dependencies
require_once '../../vendor/autoload.php';
require_once 'FormattingDemoCommand.php';

// Create and configure the CLI runner
$runner = new Runner();

// Register commands
$runner->register(new FormattingDemoCommand());

// Set default command

// Start the application
exit($runner->start());
