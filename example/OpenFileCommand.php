<?php
use webfiori\cli\CLICommand;

class OpenFileCommand extends CLICommand {
    public function __construct() {
        parent::__construct('open-file', [
            'path' => [
                'optional' => true,
                'description' => 'The absolute path to file.'
            ]
        ], 'Reads a text file and display its content.');
    }

    public function exec(): int {
        $path = $this->getArgValue('path');
        
        if ($path === null) {
            $path = $this->getInput('Give me file path:');
        }
        
        if (!file_exists($path)) {
            $this->error('File not found: '.$path);
            return -1;
        }
        $resource = fopen($path, 'r');
        $ch = '';
        while($ch !== false) {
            $ch = fgetc($resource);
            $this->prints($ch);
        }
        
        fclose($resource);
        return 1;
    }

}
