<?php

namespace webfiori\tests\cli;

use webfiori\cli\CLICommand;
use webfiori\cli\streams\StdIn;
use webfiori\cli\streams\ArrayOutputStream;

class TestCommand extends CLICommand {
    public function __construct($commandName, $args = array(), $description = '') {
        parent::__construct($commandName, $args, $description);
        $this->setInputStream(new StdIn());
        $this->setOutputStream(new ArrayOutputStream());
    }
    public function exec() : int {
        $name = $this->getArgValue('name');
        $this->println('Hello '.$name.'!', [
            'color' => 'red',
        ]);
        $this->println('Ok');
        return 0;
    }

}
