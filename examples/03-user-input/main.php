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

use WebFiori\Cli\Commands\HelpCommand;
use WebFiori\Cli\Runner;

// Load dependencies
require_once '../../vendor/autoload.php';
require_once 'SurveyCommand.php';
require_once 'SetupWizardCommand.php';
require_once 'QuizCommand.php';

// Create and configure the CLI runner
$runner = new Runner();

// Register commands
$runner->register(new HelpCommand());
$runner->register(new SurveyCommand());
$runner->register(new SetupWizardCommand());
$runner->register(new QuizCommand());

// Set default command
$runner->setDefaultCommand('help');

// Start the application
exit($runner->start());
