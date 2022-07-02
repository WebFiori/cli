<?php
namespace webfiori\tests\cli\testCommands;

use webfiori\cli\CLICommand;

class Command01 extends CLICommand {
    public function __construct() {
        parent::__construct('show-v', [
            'arg-1' => [
                
            ],
            'arg-2' => [
                
            ],
            'arg-3' => [
                'default' => 'Hello'
            ]
        ], 'No desc');
    }

    public function exec(): int {
        $this->println('System version: 1.0.0');
        $this->println($this->getArgValue('arg-1'));
        $this->println($this->getArgValue('arg-2'));
        $this->println($this->getArgValue('arg-3'));
        return 0;
    }

}
