<?php

/**
 * Masked Input Example - Secure data entry demonstration
 * 
 * This example shows how to use the getMaskedInput() method for secure data entry.
 * Run different demos to see various use cases.
 */

require_once '../../vendor/autoload.php';
require_once 'SecureInputCommand.php';

use WebFiori\Cli\Runner;

// Create and configure the CLI runner
$runner = new Runner();

// Register the secure input command
$runner->register(new SecureInputCommand());

// Start the application
exit($runner->start());
