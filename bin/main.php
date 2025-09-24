<?php

include $_composer_autoload_path ?? __DIR__.'/../vendor/autoload.php';

use WebFiori\Cli\Commands\HelpCommand;
use WebFiori\Cli\Commands\InitAppCommand;
use WebFiori\Cli\Runner;

$runner = new Runner();
exit($runner->register(new HelpCommand())
        ->register(new InitAppCommand())
        ->setDefaultCommand('help')
        ->start());
