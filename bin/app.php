<?php

include $_composer_autoload_path ?? __DIR__.'/../vendor/autoload.php';

use webfiori\cli\commands\HelpCommand;
use webfiori\cli\Runner;

require 'InitAppCommand.php';
$runner = new Runner();
exit($runner->register(new HelpCommand())
        ->register(new InitAppCommand())
        ->setDefaultCommand('help')
        ->start());
