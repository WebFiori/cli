<?php
namespace WebFiori\Tests\CLI\Discovery\TestCommands;

use WebFiori\CLI\Command;

/**
 * An abstract test command that should not be discovered.
 */
abstract class AbstractTestCommand extends Command {
    public function __construct() {
        parent::__construct('abstract-cmd', [], 'Abstract test command');
    }
}
