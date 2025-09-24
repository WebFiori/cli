<?php
namespace WebFiori\Tests\Cli\Discovery\TestCommands;

use WebFiori\Cli\Command;

/**
 * A hidden test command.
 * 
 * @Hidden
 */
class HiddenCommand extends Command {
    public function __construct() {
        parent::__construct('hidden-cmd', [], 'A hidden test command');
    }
    
    public function exec(): int {
        $this->println('Hidden command executed');
        return 0;
    }
}
