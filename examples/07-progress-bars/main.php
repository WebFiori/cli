<?php

/**
 * Progress Bars CLI Application
 * 
 * This application demonstrates the comprehensive progress bar system including:
 * - Various progress bar styles and formats
 * - Real-world file processing scenarios
 * - Download simulation with detailed progress
 * - Batch processing with multiple progress indicators
 * - Performance monitoring and optimization
 */

use WebFiori\Cli\Runner;

// Load dependencies
require_once '../../vendor/autoload.php';
require_once 'ProgressDemoCommand.php';

// Create and configure the CLI runner
$runner = new Runner();

// Register commands
$runner->register(new ProgressDemoCommand());

// Set default command

// Start the application
exit($runner->start());
