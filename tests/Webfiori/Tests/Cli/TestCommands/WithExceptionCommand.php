<?php
namespace WebFiori\Tests\Cli\TestCommands;

use WebFiori\Cli\CLICommand;
use Exception;


class WithExceptionCommand extends CLICommand {
    public function __construct() {
        parent::__construct('with-exception');
    }
    public function exec(): int {
        $this->notExist();
    }

}
