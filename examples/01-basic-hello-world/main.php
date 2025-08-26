<?php

/**
 * Basic Hello World CLI Application
 * 
 * This is the entry point for a simple CLI application that demonstrates
 * the fundamental concepts of the WebFiori CLI library.
 * 
 * Features demonstrated:
 * - Command registration
 * - Runner setup
 * - Help command integration
 * - Basic application structure
 */

use WebFiori\CLI\Commands\HelpCommand;
use WebFiori\CLI\Runner;

// Load the WebFiori CLI library
require_once '../../vendor/autoload.php';

// Load our custom command
require_once 'HelloCommand.php';

// Create the CLI runner
$runner = new Runner();

// Register the help command (provides automatic help generation)
$runner->register(new HelpCommand());

// Register our custom hello command
$runner->register(new HelloCommand());

// Set the default command to show help when no command is specified
$runner->setDefaultCommand('help');

// Start the CLI application and exit with the appropriate code
exit($runner->start());
