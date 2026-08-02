<?php

/**
 * Command Scaffolding Example - Generate CLI commands automatically
 * 
 * This example demonstrates the command scaffolding tools that help developers
 * quickly generate new command classes with proper structure and templates.
 */

require_once '../../vendor/autoload.php';
require_once '../../WebFiori/Cli/Commands/MakeCommand.php';

use WebFiori\Cli\Runner;
use WebFiori\Cli\Commands\MakeCommand;

// Create and configure the CLI runner
$runner = new Runner();

// Register the scaffolding command
$runner->register(new MakeCommand());

// Start the application
exit($runner->start());
