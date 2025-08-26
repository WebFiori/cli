<?php

include $_composer_autoload_path ?? __DIR__.'/../vendor/autoload.php';

use WebFiori\CLI\Commands\HelpCommand;
use WebFiori\CLI\Commands\InitAppCommand;
use WebFiori\CLI\Runner;

$runner = new Runner();
exit($runner->register(new HelpCommand())
        ->register(new InitAppCommand())
        ->setDefaultCommand('help')
        ->start());
