<?php
namespace WebFiori\Tests\Cli\TestCommands;

use WebFiori\Cli\Command;

/**
 * Test command for alias conflict testing.
 */
class ConflictTestCommand extends Command {

    public function __construct() {
        parent::__construct('conflict-test', [], 'A command for testing alias conflicts', ['test']);
    }

    public function exec(): int {
        $this->println("Conflict test command executed");
        return 0;
    }
}
