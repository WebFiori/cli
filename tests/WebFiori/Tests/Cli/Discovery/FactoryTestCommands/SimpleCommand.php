<?php
namespace WebFiori\Tests\Cli\Discovery\FactoryTestCommands;

use WebFiori\Cli\Command;

/**
 * A simple command with no constructor dependencies.
 * Used to test that the factory also handles simple commands.
 */
class SimpleCommand extends Command {
    public function __construct() {
        parent::__construct('simple-cmd', [], 'A simple command');
    }

    public function exec(): int {
        $this->println('Simple command executed');
        return 0;
    }
}
