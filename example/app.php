<?php
require_once '../vendor/autoload.php';
require_once './HelloWorldCommand.php';

use webfiori\cli\Runner;
use webfiori\cli\commands\HelpCommand;

$runner = new Runner();
$runner->register(new HelpCommand());
$runner->register(new HelloWorldCommand());
$runner->start();
