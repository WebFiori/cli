<?php
namespace webfiori\tests\cli\testCommands;

use webfiori\cli\CLICommand;
use Exception;

class WithExceptionCommand extends CLICommand {
    public function __construct() {
        parent::__construct('with-exception');
    }
    public function exec(): int {
        $this->notExist();
    }

}
