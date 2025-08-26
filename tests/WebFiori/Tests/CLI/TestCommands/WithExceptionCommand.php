<?php
namespace WebFiori\Tests\CLI\TestCommands;

use WebFiori\CLI\Command;
use Exception;


class WithExceptionCommand extends Command {
    public function __construct() {
        parent::__construct('with-exception');
    }
    public function exec(): int {
        $this->notExist();
    }

}
