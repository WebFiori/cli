<?php
namespace WebFiori\Tests\Cli\Discovery\TestCommands;

use WebFiori\Cli\Command;

/**
 * A simple test command for discovery testing.
 * 
 * @Command(name="test-cmd", description="A test command", group="test")
 */
class TestCommand extends Command {
    public function __construct() {
        parent::__construct('test-cmd', [], 'A test command for discovery testing');
    }
    
    public function exec(): int {
        $this->println('Test command executed');
        return 0;
    }
}
