<?php

include $_composer_autoload_path ?? __DIR__ . '/../vendor/autoload.php';

use webfiori\cli\commands\HelpCommand;
use webfiori\cli\Runner;

require 'InitAppCommand.php';
$runner = new Runner();
$runner->register(new HelpCommand());
$runner->register(new InitAppCommand());
$runner->start();