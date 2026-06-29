<?php
namespace WebFiori\Tests\Cli\Discovery\FactoryTestCommands;

use WebFiori\Cli\Command;

/**
 * A command that requires a dependency in its constructor.
 * Used to test factory-based instantiation.
 */
class CommandWithDependency extends Command {
    private string $service;

    public function __construct(string $service) {
        parent::__construct('dep-cmd', [], 'A command with a dependency');
        $this->service = $service;
    }

    public function exec(): int {
        $this->println('Service: ' . $this->service);
        return 0;
    }

    public function getService(): string {
        return $this->service;
    }
}
