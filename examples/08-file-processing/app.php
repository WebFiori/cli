<?php
require_once '../../vendor/autoload.php';

use WebFiori\Cli\ArgumentOption;
use WebFiori\Cli\Command;
use WebFiori\Cli\Runner;

class FileProcessCommand extends Command {
    public function __construct() {
        parent::__construct('process-file', [
            '--file' => [
                ArgumentOption::OPTIONAL => false,
                ArgumentOption::DESCRIPTION => 'Path to the file to process'
            ],
            '--action' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => 'count',
                ArgumentOption::VALUES => ['count', 'uppercase', 'reverse'],
                ArgumentOption::DESCRIPTION => 'Action to perform (count, uppercase, reverse)'
            ]
        ], 'Process text files in various ways');
    }

    public function exec(): int {
        $filePath = $this->getArgValue('--file');
        $action = $this->getArgValue('--action');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        if (!is_readable($filePath)) {
            $this->error("File is not readable: $filePath");
            return 1;
        }

        $content = file_get_contents($filePath);
        
        switch ($action) {
            case 'count':
                $lines = substr_count($content, "\n") + 1;
                $words = str_word_count($content);
                $chars = strlen($content);
                
                $this->println("File Statistics for: %s", $filePath);
                $this->println("Lines: %d", $lines);
                $this->println("Words: %d", $words);
                $this->println("Characters: %d", $chars);
                break;
                
            case 'uppercase':
                $result = strtoupper($content);
                $this->println("Uppercase content:");
                $this->println($result);
                break;
                
            case 'reverse':
                $lines = explode("\n", $content);
                $reversed = array_reverse($lines);
                $this->println("Reversed content:");
                $this->println(implode("\n", $reversed));
                break;
        }

        return 0;
    }
}

$runner = new Runner();
$runner->register(new FileProcessCommand());
exit($runner->start());
