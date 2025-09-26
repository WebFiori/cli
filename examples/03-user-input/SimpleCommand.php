<?php
require_once '../../vendor/autoload.php';

use WebFiori\Cli\Command;

class SimpleCommand extends Command {
    public function __construct() {
        parent::__construct('simple-survey', [], 'A simple survey without interactive input');
    }

    public function exec(): int {
        $this->println('📋 Simple Survey Demo');
        $this->println('====================');
        
        // Simulate collecting data
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 30,
            'country' => 'Canada',
            'languages' => ['PHP', 'Python'],
            'experience' => 'Advanced'
        ];
        
        $this->println();
        $this->success('Survey completed! Here\'s your data:');
        $this->println();
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->println('%s: %s', ucfirst($key), implode(', ', $value));
            } else {
                $this->println('%s: %s', ucfirst($key), $value);
            }
        }
        
        return 0;
    }
}
