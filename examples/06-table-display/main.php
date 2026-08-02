<?php

require_once '../../vendor/autoload.php';
require_once 'TableDemoCommand.php';

use WebFiori\Cli\Runner;

// Create CLI runner
$runner = new Runner();

// Register the table demo command
$runner->register(new TableDemoCommand());
// Start the application
exit($runner->start());
