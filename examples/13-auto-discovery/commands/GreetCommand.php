<?php

use WebFiori\Cli\Command;

/**
 * A command that requires a greeting message injected via constructor.
 */
class GreetCommand extends Command {
    private string $greeting;

    public function __construct(string $greeting) {
        parent::__construct('greet', [], 'Display a greeting from the service container');
        $this->greeting = $greeting;
    }

    public function exec(): int {
        $this->println($this->greeting);

        return 0;
    }
}
