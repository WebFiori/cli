<?php

use webfiori\cli\CLICommand;
use webfiori\cli\CommandArgument;
/**
 * A class which is used to initialize a new CLI application.
 *
 * @author Ibrahim
 */
class InitAppCommand extends CLICommand {
    public function __construct() {
        parent::__construct('init', [
            new CommandArgument('--dir', 'The name of application root directory.')
        ], 'Initialize new CLI application.');

    }
    public function exec(): int {
        $dirName = $this->getArgValue('--dir');
        $this->println(__DIR__);
    }
}
