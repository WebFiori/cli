<?php
namespace WebFiori\Tests\Cli\TestCommands;

use WebFiori\Cli\Command;
use Exception;


class WithExceptionCommand extends Command {
    public function __construct() {
        parent::__construct('with-exception');
    }
    public function exec(): int {
        $this->notExist();
    }

}
