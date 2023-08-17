<?php

require_once '../vendor/autoload.php';

use webfiori\cli\commands\HelpCommand;
use webfiori\cli\Runner;

$runner = new Runner();
$runner->register(new HelpCommand());
$runner->register(new InitAppCommand());
$runner->start();