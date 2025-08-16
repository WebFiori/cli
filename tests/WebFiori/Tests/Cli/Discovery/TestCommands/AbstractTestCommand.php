<?php
namespace WebFiori\Tests\Cli\Discovery\TestCommands;

use WebFiori\Cli\CLICommand;

/**
 * An abstract test command that should not be discovered.
 */
abstract class AbstractTestCommand extends CLICommand {
    public function __construct() {
        parent::__construct('abstract-cmd', [], 'Abstract test command');
    }
}
