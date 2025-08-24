<?php
namespace WebFiori\Tests\Cli\TestCommands;

use WebFiori\Cli\Command;

/**
 * Test command for aliasing functionality.
 */
class AliasTestCommand extends Command {

    public function __construct() {
        parent::__construct('alias-test', [], 'A test command for aliasing', ['test', 'at']);
    }

    public function exec(): int {
        $this->println("Alias test command executed");
        return 0;
    }
}
