<?php
declare(strict_types=1);

namespace WebFiori\Tests\Cli;

use WebFiori\Cli\CommandTestCase;
use WebFiori\Cli\Commands\MakeCommand;

/**
 * Test cases for command scaffolding functionality.
 */
class MakeCommandTest extends CommandTestCase {
    
    private string $testOutputDir = 'test_commands';
    
    protected function setUp(): void {
        parent::setUp();
        // Clean up test directory
        if (is_dir($this->testOutputDir)) {
            $this->removeDirectory($this->testOutputDir);
        }
    }
    
    protected function tearDown(): void {
        parent::tearDown();
        // Clean up test directory
        if (is_dir($this->testOutputDir)) {
            $this->removeDirectory($this->testOutputDir);
        }
    }
    
    /**
     * Test basic command generation.
     * 
     * @test
     */
    public function testBasicCommandGeneration() {
        $command = new MakeCommand();
        
        $output = $this->executeSingleCommand($command, [
            '--name' => 'test-command',
            '--class' => 'TestCommand',
            '--path' => $this->testOutputDir
        ], ['y']); // Confirm if file exists
        
        $this->assertContains("Command generated successfully!\n", $output);
        $this->assertEquals(0, $this->getExitCode());
        
        // Check if file was created
        $expectedFile = $this->testOutputDir . '/TestCommand.php';
        $this->assertTrue(file_exists($expectedFile), "Command file should be created");
        
        // Check file content
        $content = file_get_contents($expectedFile);
        $this->assertStringContainsString('class TestCommand extends Command', $content);
        $this->assertStringContainsString("'test-command'", $content);
    }
    
    /**
     * Test command generation with namespace.
     * 
     * @test
     */
    public function testCommandGenerationWithNamespace() {
        $command = new MakeCommand();
        
        $output = $this->executeSingleCommand($command, [
            '--name' => 'user:create',
            '--namespace' => 'App\\Commands',
            '--path' => $this->testOutputDir
        ]);
        
        $this->assertEquals(0, $this->getExitCode());
        
        $expectedFile = $this->testOutputDir . '/UserCreateCommand.php';
        $this->assertTrue(file_exists($expectedFile));
        
        $content = file_get_contents($expectedFile);
        $this->assertStringContainsString('namespace App\\Commands;', $content);
        $this->assertStringContainsString('class UserCreateCommand extends Command', $content);
    }
    
    /**
     * Test interactive template generation.
     * 
     * @test
     */
    public function testInteractiveTemplate() {
        $command = new MakeCommand();
        
        $output = $this->executeSingleCommand($command, [
            '--name' => 'setup-wizard',
            '--template' => 'interactive',
            '--path' => $this->testOutputDir
        ]);
        
        $this->assertEquals(0, $this->getExitCode());
        
        $expectedFile = $this->testOutputDir . '/SetupWizardCommand.php';
        $content = file_get_contents($expectedFile);
        
        $this->assertStringContainsString('InputValidator', $content);
        $this->assertStringContainsString('getInput(', $content);
        $this->assertStringContainsString('confirm(', $content);
    }
    
    /**
     * Test CRUD template generation.
     * 
     * @test
     */
    public function testCrudTemplate() {
        $command = new MakeCommand();
        
        $output = $this->executeSingleCommand($command, [
            '--name' => 'user-manager',
            '--template' => 'crud',
            '--path' => $this->testOutputDir
        ]);
        
        $this->assertEquals(0, $this->getExitCode());
        
        $expectedFile = $this->testOutputDir . '/UserManagerCommand.php';
        $content = file_get_contents($expectedFile);
        
        $this->assertStringContainsString('createRecord()', $content);
        $this->assertStringContainsString('updateRecord()', $content);
        $this->assertStringContainsString('deleteRecord()', $content);
        $this->assertStringContainsString('listRecords()', $content);
    }
    
    /**
     * Test command generation with arguments.
     * 
     * @test
     */
    public function testCommandWithArguments() {
        $command = new MakeCommand();
        
        $output = $this->executeSingleCommand($command, [
            '--name' => 'process-data',
            '--args' => 'input file,output format,verbose mode',
            '--path' => $this->testOutputDir
        ]);
        
        $this->assertEquals(0, $this->getExitCode());
        
        $expectedFile = $this->testOutputDir . '/ProcessDataCommand.php';
        $content = file_get_contents($expectedFile);
        
        $this->assertStringContainsString('--input-file', $content);
        $this->assertStringContainsString('--output-format', $content);
        $this->assertStringContainsString('--verbose-mode', $content);
    }
    
    /**
     * Test invalid command name validation.
     * 
     * @test
     */
    public function testInvalidCommandName() {
        $command = new MakeCommand();
        
        $output = $this->executeSingleCommand($command, [
            '--name' => 'Invalid Command Name!',
            '--path' => $this->testOutputDir
        ]);
        
        $this->assertEquals(1, $this->getExitCode());
        $this->assertContains("Command name must start with a letter\n", $output);
    }
    
    /**
     * Test file overwrite confirmation.
     * 
     * @test
     */
    public function testFileOverwriteConfirmation() {
        $command = new MakeCommand();
        
        // Create file first
        $this->executeSingleCommand($command, [
            '--name' => 'existing-command',
            '--path' => $this->testOutputDir
        ]);
        
        // Try to create again with 'no' confirmation
        $output = $this->executeSingleCommand($command, [
            '--name' => 'existing-command',
            '--path' => $this->testOutputDir
        ], ['n']);
        
        $this->assertEquals(1, $this->getExitCode());
        $this->assertContains("File already exists and overwrite was declined\n", $output);
    }
    
    /**
     * Helper method to remove directory recursively.
     */
    private function removeDirectory(string $dir): void {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
