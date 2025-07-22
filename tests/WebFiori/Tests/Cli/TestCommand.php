<?php

namespace WebFiori\Tests\Cli;

use WebFiori\Cli\CliCommand;
use WebFiori\Cli\Streams\StdIn;
use WebFiori\Cli\Streams\ArrayOutputStream;

class TestCommand extends CliCommand {
    private $outputArray = [];
    
    public function __construct($commandName = '', $args = array(), $description = '') {
        parent::__construct($commandName, $args, $description);
        $this->setInputStream(new StdIn());
        $this->setOutputStream(new ArrayOutputStream());
        
        // Add the print method to the ArrayOutputStream class if it doesn't exist
        if (!method_exists('WebFiori\Cli\Streams\ArrayOutputStream', 'print')) {
            $outputStream = $this->getOutputStream();
            $outputStream->prints = function($str, ...$_) use ($outputStream) {
                if (count($_) != 0) {
                    $str = sprintf($str, ...$_);
                }
                
                $outputArray = $outputStream->getOutputArray();
                if (count($outputArray) == 0) {
                    $outputArray[] = $str;
                } else {
                    $outputArray[count($outputArray) - 1] .= $str;
                }
                
                // Set the output array back
                $reflection = new \ReflectionClass($outputStream);
                $property = $reflection->getProperty('outputArray');
                $property->setAccessible(true);
                $property->setValue($outputStream, $outputArray);
            };
            
            // Add the print method
            $outputStream->print = function($str) use ($outputStream) {
                $outputStream->prints($str);
            };
        }
    }
    
    public function exec() : int {
        $name = $this->getArgValue('--name');
        
        if ($name === null) {
            $this->println('Hello World!');
        } else {
            $this->println('Hello %s!', $name);
        }
        
        return 0;
    }
    
    // Add missing methods for testing
    public function moveCursorUp($lines = 1) {
        $this->getOutputStream()->prints("\e[{$lines}A");
    }
    
    public function moveCursorDown($lines = 1) {
        $this->getOutputStream()->prints("\e[{$lines}B");
    }
    
    public function moveCursorForward($chars = 1) {
        $this->getOutputStream()->prints("\e[{$chars}C");
    }
    
    public function moveCursorBackward($chars = 1) {
        $this->getOutputStream()->prints("\e[{$chars}D");
    }
    
    public function execSubCommand($commandName, array $args = []) {
        if ($this->getRunner() === null) {
            $this->error('No runner is associated with the command.');
            return -1;
        }
        
        if (!$this->getRunner()->hasCommand($commandName)) {
            $this->error("Command '%s' is not registered.", $commandName);
            return -1;
        }
        
        $command = $this->getRunner()->getCommand($commandName);
        
        foreach ($args as $argName => $argVal) {
            if ($command->hasArg($argName)) {
                $command->getArg($argName)->setValue($argVal);
            }
        }
        
        if (!$command->validateArgs()) {
            return -1;
        }
        
        return $command->exec();
    }
}
