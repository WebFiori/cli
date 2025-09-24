<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\ArgumentOption;

/**
 * A simple hello world command that demonstrates basic CLI functionality.
 * 
 * This command shows how to:
 * - Create a basic command class
 * - Handle optional arguments with defaults
 * - Use different output methods
 * - Return appropriate exit codes
 */
class HelloCommand extends Command {
    public function __construct() {
        parent::__construct('hello', [
            '--name' => [
                ArgumentOption::DESCRIPTION => 'The name to greet (default: World)',
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => 'World'
            ]
        ], 'A simple greeting command that says hello to someone');
    }

    /**
     * Execute the hello command.
     * 
     * This method demonstrates:
     * - Getting argument values
     * - Basic output formatting
     * - Conditional logic
     * - Proper return codes
     */
    public function exec(): int {
        // Get the name argument, with fallback to default
        $name = $this->getArgValue('--name') ?? 'World';

        // Trim whitespace and validate
        $name = trim($name);

        if (empty($name)) {
            $this->error('Name cannot be empty!');

            return 1; // Error exit code
        }

        // Special greeting for WebFiori
        if (strtolower($name) === 'webfiori') {
            $this->success("🎉 Hello, $name! Welcome to the CLI world!");
            $this->info('You\'re using the WebFiori CLI library - great choice!');
        } else {
            // Standard greeting
            $this->println("Hello, $name! 👋");

            // Add some personality based on name length
            if (strlen($name) > 10) {
                $this->info('Wow, that\'s quite a long name!');
            } elseif (strlen($name) <= 2) {
                $this->info('Short and sweet!');
            }
        }

        // Success message
        $this->println('Have a wonderful day!');

        return 0; // Success exit code
    }
}
