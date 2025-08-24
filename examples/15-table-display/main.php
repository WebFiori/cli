<?php

require_once '../../vendor/autoload.php';
require_once 'TableDemoCommand.php';

use WebFiori\Cli\Commands\HelpCommand;
use WebFiori\Cli\Runner;

// Create CLI runner
$runner = new Runner();

// Register the table demo command
$runner->register(new HelpCommand());
$runner->register(new TableDemoCommand());
$runner->setDefaultCommand('help');
// Start the application
exit($runner->start());
