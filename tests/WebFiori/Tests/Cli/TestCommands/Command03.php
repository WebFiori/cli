<?php
namespace WebFiori\Tests\Cli\TestCommands;

use WebFiori\Cli\CliCommand;

class Command03 extends CliCommand {
    public function __construct() {
        parent::__construct('run-another');
    }

    public function exec(): int {
        $this->println('Running Sub Command');
        $this->getRunner()->register(new Command01());
        $this->execSubCommand('show-v', ['arg-3' => 'Ur']);
        $this->println('Done');
        return 0;
    }

}
