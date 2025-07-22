<?php

namespace WebFiori\Tests\Cli;

use WebFiori\Cli\CliCommand;
use WebFiori\Cli\Streams\StdIn;
use WebFiori\Cli\Streams\ArrayOutputStream;

class TestCommand extends CliCommand {
    public function __construct($commandName = '', $args = array(), $description = '') {
        parent::__construct($commandName, $args, $description);
        $this->setInputStream(new StdIn());
        $this->setOutputStream(new ArrayOutputStream());
    }
    public function exec() : int {
        $name = $this->getArgValue('--name');
        
        if ($name === null) {
            $this->println('Hello World!');
        } else {
            $this->println('Hello %s!', $name);
        }
        
        return 0;
    }

}
