<?php
namespace WebFiori\Tests\Cli\TestCommands;

use WebFiori\Cli\Command;

/**
 * Test command without built-in aliases.
 */
class NoAliasCommand extends Command {

    public function __construct() {
        parent::__construct('no-alias', [], 'A command without built-in aliases');
    }

    public function exec(): int {
        $this->println("No alias command executed");
        return 0;
    }
}
