<?php
declare(strict_types=1);
namespace WebFiori\Cli\Commands;

use WebFiori\Cli\ArgumentOption;
use WebFiori\Cli\Command;
use WebFiori\Cli\InputValidator;
use WebFiori\Cli\Templates\TemplateManager;
use WebFiori\File\File;

/**
 * Command scaffolding tool for generating new CLI commands.
 * 
 * This command helps developers quickly create new command classes with proper
 * structure, documentation, and optional features like arguments, validation,
 * and interactive prompts.
 */
class MakeCommand extends Command {
    
    private TemplateManager $templateManager;
    
    public function __construct() {
        $this->templateManager = new TemplateManager();
        
        parent::__construct('make:command', [
            '--name' => [
                ArgumentOption::DESCRIPTION => 'The name of the command (e.g., "user:create")',
                ArgumentOption::OPTIONAL => false
            ],
            '--class' => [
                ArgumentOption::DESCRIPTION => 'The class name (e.g., "CreateUserCommand")',
                ArgumentOption::OPTIONAL => true
            ],
            '--path' => [
                ArgumentOption::DESCRIPTION => 'Output directory path',
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => 'commands'
            ],
            '--namespace' => [
                ArgumentOption::DESCRIPTION => 'PHP namespace for the command class',
                ArgumentOption::OPTIONAL => true
            ],
            '--interactive' => [
                ArgumentOption::DESCRIPTION => 'Generate command with interactive prompts',
                ArgumentOption::OPTIONAL => true
            ],
            '--args' => [
                ArgumentOption::DESCRIPTION => 'Add command arguments (comma-separated)',
                ArgumentOption::OPTIONAL => true
            ],
            '--template' => [
                ArgumentOption::DESCRIPTION => 'Template type to use',
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::VALUES => $this->templateManager->getAvailableTemplates(),
                ArgumentOption::DEFAULT => 'basic'
            ]
        ], 'Generate a new CLI command class with scaffolding');
    }

    public function exec(): int {
        $this->println('🚀 WebFiori CLI Command Generator');
        $this->println('=================================');
        $this->println();

        // Get command details
        $commandName = $this->getArgValue('--name');
        $className = $this->getArgValue('--class') ?? $this->generateClassName($commandName);
        $outputPath = $this->getArgValue('--path') ?? 'commands';
        $namespace = $this->getArgValue('--namespace');
        $template = $this->getArgValue('--template') ?? 'basic';
        $interactive = $this->isArgProvided('--interactive');
        $args = $this->getArgValue('--args');

        // Interactive mode for missing details
        if (!$namespace) {
            $namespace = $this->getInput('Enter namespace (optional): ') ?: null;
        }

        // Validate inputs
        if (!$this->validateInputs($commandName, $className)) {
            return 1;
        }

        // Generate command
        try {
            $filePath = $this->generateCommand([
                'name' => $commandName,
                'class' => $className,
                'path' => $outputPath,
                'namespace' => $namespace,
                'template' => $template,
                'interactive' => $interactive,
                'args' => $args ? explode(',', $args) : []
            ]);

            $this->success("✅ Command generated successfully!");
            $this->info("📁 File: $filePath");
            $this->info("🏷️  Class: $className");
            $this->info("⚡ Command: $commandName");
            
            $this->println();
            $this->println("Next steps:");
            $this->println("1. Register the command in your application");
            $this->println("2. Implement the exec() method logic");
            $this->println("3. Add any additional arguments or validation");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to generate command: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Generate class name from command name.
     */
    private function generateClassName(string $commandName): string {
        // Convert command-name or namespace:command to ClassName
        $parts = preg_split('/[:\-_]/', $commandName);
        $className = '';
        
        foreach ($parts as $part) {
            $className .= ucfirst(strtolower($part));
        }
        
        return $className . 'Command';
    }

    /**
     * Validate command inputs.
     */
    private function validateInputs(string $commandName, string $className): bool {
        // Validate command name
        if (!preg_match('/^[a-z][a-z0-9\-:_]*$/', $commandName)) {
            $this->error('Command name must start with a letter and contain only lowercase letters, numbers, hyphens, colons, and underscores.');
            return false;
        }

        // Validate class name
        if (!preg_match('/^[A-Z][a-zA-Z0-9]*$/', $className)) {
            $this->error('Class name must be a valid PHP class name (PascalCase).');
            return false;
        }

        return true;
    }

    /**
     * Generate the command file.
     */
    private function generateCommand(array $config): string {
        $content = $this->templateManager->processTemplate($config['template'], [
            'namespace' => $config['namespace'] ? "namespace {$config['namespace']};\n\n" : '',
            'use_statements' => $this->generateUseStatements($config),
            'class_name' => $config['class'],
            'command_name' => $config['name'],
            'command_description' => "Description for {$config['name']} command",
            'arguments' => $this->generateArguments($config['args'])
        ]);
        
        // Ensure output directory exists
        $outputDir = $config['path'];
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Generate file path
        $fileName = $config['class'] . '.php';
        $filePath = rtrim($outputDir, '/') . '/' . $fileName;

        // Check if file exists
        if (file_exists($filePath)) {
            $overwrite = $this->confirm("File $filePath already exists. Overwrite?");
            if (!$overwrite) {
                throw new \Exception("File already exists and overwrite was declined.");
            }
        }

        // Write file
        file_put_contents($filePath, $content);
        
        return $filePath;
    }

    /**
     * Generate use statements.
     */
    private function generateUseStatements(array $config): string {
        $uses = [
            'use WebFiori\Cli\Command;',
            'use WebFiori\Cli\ArgumentOption;'
        ];

        if ($config['interactive'] || $config['template'] === 'interactive') {
            $uses[] = 'use WebFiori\Cli\InputValidator;';
        }

        return implode("\n", $uses);
    }

    /**
     * Generate command arguments array.
     */
    private function generateArguments(array $args): string {
        if (empty($args)) {
            return '[]';
        }

        $argStrings = [];
        foreach ($args as $arg) {
            $arg = trim($arg);
            $argName = '--' . strtolower(str_replace(' ', '-', $arg));
            $argStrings[] = "            '$argName' => [\n" .
                           "                ArgumentOption::DESCRIPTION => 'Description for $arg',\n" .
                           "                ArgumentOption::OPTIONAL => true\n" .
                           "            ]";
        }

        return "[\n" . implode(",\n", $argStrings) . "\n        ]";
    }
}
