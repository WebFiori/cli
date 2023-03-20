<?php

use webfiori\cli\CLICommand;

class HelloWorldCommand extends CLICommand {
    public function __construct() {
        parent::__construct('hello', [
            '--person-name' => [
                'description' => 'Name of someone to greet.',
                'optional' => true
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
