<?php

/**
 * User Input CLI Application
 * 
 * This application demonstrates comprehensive user input handling including:
 * - Interactive surveys with validation
 * - Multi-step setup wizards
 * - Quiz systems with scoring
 * - Various input types and validation methods
 * - Error handling and user feedback
 */

use WebFiori\Cli\Runner;

// Load dependencies
require_once '../../vendor/autoload.php';
require_once 'SurveyCommand.php';
require_once 'SimpleCommand.php';

// Create and configure the CLI runner
$runner = new Runner();

// Register commands
$runner->register(new SurveyCommand());
$runner->register(new SimpleCommand());

// Set default command

// Start the application
exit($runner->start());
