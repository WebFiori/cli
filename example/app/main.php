<?php

use WebFiori\Cli\Commands\HelpCommand;
use WebFiori\Cli\Runner;

require_once '../../vendor/autoload.php';
require_once './HelloWorldCommand.php';
require_once './OpenFileCommand.php';
require_once './ProgressDemoCommand.php';



$runner = new Runner();

$runner->register(new HelpCommand());
$runner->register(new HelloWorldCommand());
$runner->register(new OpenFileCommand());
$runner->register(new ProgressDemoCommand());
$runner->setDefaultCommand('help');

exit($runner->start());
