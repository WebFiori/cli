<?php
namespace WebFiori\Tests\Cli\TestCommands;

use WebFiori\Cli\CliCommand;
use Exception;

class WithExceptionCommand extends CliCommand {
    public function __construct() {
        parent::__construct('with-exception');
    }
    public function exec(): int {
        $this->notExist();
    }

}
