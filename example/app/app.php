<?php

require_once '../../vendor/autoload.php';
require_once './HelloWorldCommand.php';
require_once './OpenFileCommand.php';

use webfiori\cli\commands\HelpCommand;
use webfiori\cli\Runner;

$runner = new Runner();

$runner->register(new HelpCommand());
$runner->register(new HelloWorldCommand());
$runner->register(new OpenFileCommand());
$runner->setDefaultCommand('help');

exit($runner->start());
