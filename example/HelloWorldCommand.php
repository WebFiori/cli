<?php

use webfiori\cli\CLICommand;

class HelloWorldCommand extends CLICommand {
    public function __construct() {
        parent::__construct('hello');
    }

    public function exec(): int {
        $this->println("Hello World!");

        return 0;
    }
}
