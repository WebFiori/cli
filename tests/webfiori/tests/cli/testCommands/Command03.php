<?php
namespace webfiori\tests\cli\testCommands;

use webfiori\cli\CLICommand;

class Command03 extends CLICommand {
    public function __construct() {
        parent::__construct('run-another');
    }

    public function exec(): int {
        $this->println('Running Sub Command');
        $this->getOwner()->register(new Command01());
        $this->execSubCommand('show-v', ['arg-3' => 'Ur']);
        $this->println('Done');
        return 0;
    }

}
