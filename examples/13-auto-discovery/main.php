<?php

/**
 * Auto-Discovery with Factory Example
 *
 * Demonstrates how to use a factory callable with command auto-discovery
 * to inject dependencies into commands that require constructor arguments.
 */

use WebFiori\Cli\Runner;
use WebFiori\Cli\Discovery\CommandCache;
use WebFiori\Cli\Discovery\CommandDiscovery;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/commands/GreetCommand.php';
require_once __DIR__ . '/commands/StatusCommand.php';

// Simulate a simple service container
$services = [
    'greeting' => 'Hello from the container!',
    'version' => '2.1.0',
];

// Create the runner
$runner = new Runner();

// Set up discovery with a factory that injects dependencies
$discovery = new CommandDiscovery(new CommandCache(enabled: false));
$discovery->addSearchPath(__DIR__ . '/commands');
$discovery->setFactory(function (string $className) use ($services) {
    // Use a match or conditional to provide dependencies per command
    return match ($className) {
        GreetCommand::class => new GreetCommand($services['greeting']),
        StatusCommand::class => new StatusCommand($services['version']),
        default => new $className(),
    };
});

// Assign discovery to runner
$runner->setCommandDiscovery($discovery);
$runner->discoverCommands();

// Run the CLI
exit($runner->start());
