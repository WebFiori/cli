<?php

namespace WebFiori\Tests\Cli;

use WebFiori\Cli\CLICommand;
use WebFiori\Cli\Streams\ArrayOutputStream;
use WebFiori\Cli\Streams\StdIn;

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
