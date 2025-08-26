<?php

require_once '../../vendor/autoload.php';
require_once 'TableUsageCommand.php';
require_once 'BasicTableCommand.php';

use WebFiori\CLI\Commands\HelpCommand;
use WebFiori\CLI\Runner;

// Create CLI runner
$runner = new Runner();

// Register the help command and set it as default
$runner->register(new HelpCommand());
$runner->setDefaultCommand('help');

// Register both table commands
$runner->register(new TableUsageCommand());
$runner->register(new BasicTableCommand());

// Start the application
exit($runner->start());
