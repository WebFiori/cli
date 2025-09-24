<?php
namespace WebFiori\Tests\Cli\TestCommands;

use WebFiori\Cli\Argument;
use WebFiori\Cli\ArgumentOption;
use WebFiori\Cli\Command;



class Command01 extends Command {
    public function __construct() {
        parent::__construct('show-v', [
            'arg-1' => [
                
            ],
            new Argument('arg-2'),
            'arg-3' => [
                ArgumentOption::DEFAULT => 'Hello'
            ]
        ], 'No desc');
    }

    public function exec(): int {
        $this->println('System version: 1.0.0');
        $this->println("%s", $this->getArgValue('arg-1'));
        $this->println("%s", $this->getArgValue('arg-2'));
        $this->println("%s", $this->getArgValue('arg-3'));
        return 0;
    }

}
