<?php

require_once '../../vendor/autoload.php';
require_once 'TableUsageCommand.php';
require_once 'BasicTableCommand.php';

use WebFiori\Cli\Runner;
use WebFiori\Cli\Commands\HelpCommand;

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
