<?php

use WebFiori\Cli\CLICommand;
use WebFiori\Cli\Option;


class HelloWorldCommand extends CLICommand {
    public function __construct() {
        parent::__construct('hello', [
            '--person-name' => [
                Option::DESCRIPTION => 'Name of someone to greet.',
                Option::OPTIONAL => true
            ]
        ], 'A command to show greetings.');
    }

    public function exec(): int {
        $name = $this->getArgValue('--person-name');

        if ($name === null) {
            $this->println("Hello World!");
        } else {
            $this->println("Hello %s!", $name);
        }

        return 0;
    }
}
