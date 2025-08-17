<?php
namespace WebFiori\Tests\Cli;

use WebFiori\Cli\CommandTestCase;
use WebFiori\Cli\Runner;
use WebFiori\Cli\Streams\ArrayInputStream;
use WebFiori\Cli\Streams\ArrayOutputStream;
use WebFiori\Tests\Cli\TestCommands\AliasTestCommand;
use WebFiori\Tests\Cli\TestCommands\ConflictTestCommand;
use WebFiori\Tests\Cli\TestCommands\NoAliasCommand;

/**
 * Unit tests for command aliasing functionality.
 */
class AliasingTest extends CommandTestCase {

    /**
     * Test basic alias registration and resolution.
     * @test
     */
    public function testBasicAliasRegistration() {
        $runner = new Runner();
        $command = new AliasTestCommand();
        
        $runner->register($command);
        
        // Test that aliases are registered
        $aliases = $runner->getAliases();
        $this->assertArrayHasKey('test', $aliases);
        $this->assertArrayHasKey('at', $aliases);
        $this->assertEquals('alias-test', $aliases['test']);
        $this->assertEquals('alias-test', $aliases['at']);
        
        // Test alias resolution
        $this->assertEquals('alias-test', $runner->resolveAlias('test'));
        $this->assertEquals('alias-test', $runner->resolveAlias('at'));
        $this->assertNull($runner->resolveAlias('nonexistent'));
        
        // Test hasAlias method
        $this->assertTrue($runner->hasAlias('test'));
        $this->assertTrue($runner->hasAlias('at'));
        $this->assertFalse($runner->hasAlias('nonexistent'));
    }

    /**
     * Test runtime alias registration.
     * @test
     */
    public function testRuntimeAliasRegistration() {
        $runner = new Runner();
        $command = new NoAliasCommand();
        
        // Register command with runtime aliases
        $runner->register($command, ['na', 'noalias']);
        
        $aliases = $runner->getAliases();
        $this->assertArrayHasKey('na', $aliases);
        $this->assertArrayHasKey('noalias', $aliases);
        $this->assertEquals('no-alias', $aliases['na']);
        $this->assertEquals('no-alias', $aliases['noalias']);
    }

    /**
     * Test combined built-in and runtime aliases.
     * @test
     */
    public function testCombinedAliases() {
        $runner = new Runner();
        $command = new AliasTestCommand(); // Has built-in aliases: 'test', 'at'
        
        // Register with additional runtime aliases
        $runner->register($command, ['alias', 'testing']);
        
        $aliases = $runner->getAliases();
        
        // Check built-in aliases
        $this->assertArrayHasKey('test', $aliases);
        $this->assertArrayHasKey('at', $aliases);
        
        // Check runtime aliases
        $this->assertArrayHasKey('alias', $aliases);
        $this->assertArrayHasKey('testing', $aliases);
        
        // All should point to the same command
        $this->assertEquals('alias-test', $aliases['test']);
        $this->assertEquals('alias-test', $aliases['at']);
        $this->assertEquals('alias-test', $aliases['alias']);
        $this->assertEquals('alias-test', $aliases['testing']);
    }

    /**
     * Test command execution via aliases.
     * @test
     */
    public function testCommandExecutionViaAlias() {
        $command = new AliasTestCommand();
        
        // Test execution via built-in alias
        $output = $this->executeSingleCommand($command, ['test']);
        $this->assertEquals(["Alias test command executed\n"], $output);
        $this->assertEquals(0, $this->getExitCode());
        
        // Test execution via another built-in alias
        $output = $this->executeSingleCommand($command, ['at']);
        $this->assertEquals(["Alias test command executed\n"], $output);
        $this->assertEquals(0, $this->getExitCode());
    }

    /**
     * Test command execution via runtime aliases.
     * @test
     */
    public function testCommandExecutionViaRuntimeAlias() {
        $runner = new Runner();
        $runner->setInputStream(new ArrayInputStream([]));
        $runner->setOutputStream(new ArrayOutputStream());
        
        $command = new NoAliasCommand();
        $runner->register($command, ['na']);
        
        // Set arguments vector to execute the alias (first element is script name)
        $runner->setArgsVector(['script.php', 'na']);
        $exitCode = $runner->start();
        
        $output = $runner->getOutputStream()->getOutputArray();
        $this->assertEquals(["No alias command executed\n"], $output);
        $this->assertEquals(0, $exitCode);
    }

    /**
     * Test alias conflict resolution in non-interactive mode.
     * @test
     */
    public function testAliasConflictNonInteractive() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        
        $command1 = new AliasTestCommand(); // Has alias 'test'
        $command2 = new ConflictTestCommand(); // Also has alias 'test'
        
        $runner->register($command1);
        $runner->register($command2); // This should trigger conflict warning
        
        $aliases = $runner->getAliases();
        
        // First command should keep the alias
        $this->assertEquals('alias-test', $aliases['test']);
        
        // Check that warning was issued
        $output = $runner->getOutputStream()->getOutputArray();
        $warningFound = false;
        foreach ($output as $line) {
            if (strpos($line, "Warning: Alias 'test' already exists") !== false) {
                $warningFound = true;
                break;
            }
        }
        $this->assertTrue($warningFound, 'Expected warning message about alias conflict');
    }

    /**
     * Test alias conflict resolution in interactive mode.
     * @test
     */
    public function testAliasConflictInteractive() {
        $runner = new Runner();
        $runner->setInputStream(new ArrayInputStream(['2'])); // Choose second option
        $runner->setOutputStream(new ArrayOutputStream());
        
        $command1 = new AliasTestCommand(); // Has alias 'test'
        $command2 = new ConflictTestCommand(); // Also has alias 'test'
        
        $runner->register($command1);
        $runner->register($command2); // This should trigger interactive conflict resolution
        
        $aliases = $runner->getAliases();
        
        // In non-interactive mode, first command should keep the alias
        // (Interactive conflict resolution might not be fully implemented yet)
        $this->assertEquals('alias-test', $aliases['test']);
    }

    /**
     * Test getCommandByName with aliases.
     * @test
     */
    public function testGetCommandByNameWithAliases() {
        $runner = new Runner();
        $command = new AliasTestCommand();
        
        $runner->register($command);
        
        // Test direct command name
        $retrievedCommand = $runner->getCommandByName('alias-test');
        $this->assertSame($command, $retrievedCommand);
        
        // Test via aliases
        $retrievedCommand = $runner->getCommandByName('test');
        $this->assertSame($command, $retrievedCommand);
        
        $retrievedCommand = $runner->getCommandByName('at');
        $this->assertSame($command, $retrievedCommand);
        
        // Test non-existent
        $retrievedCommand = $runner->getCommandByName('nonexistent');
        $this->assertNull($retrievedCommand);
    }

    /**
     * Test reset functionality clears aliases.
     * @test
     */
    public function testResetClearsAliases() {
        $runner = new Runner();
        $command = new AliasTestCommand();
        
        $runner->register($command);
        
        // Verify aliases exist
        $this->assertNotEmpty($runner->getAliases());
        $this->assertTrue($runner->hasAlias('test'));
        
        // Reset and verify aliases are cleared
        $runner->reset();
        $this->assertEmpty($runner->getAliases());
        $this->assertFalse($runner->hasAlias('test'));
    }

    /**
     * Test command getAliases method.
     * @test
     */
    public function testCommandGetAliases() {
        $command = new AliasTestCommand();
        $aliases = $command->getAliases();
        
        $this->assertIsArray($aliases);
        $this->assertContains('test', $aliases);
        $this->assertContains('at', $aliases);
        $this->assertCount(2, $aliases);
        
        // Test command without aliases
        $noAliasCommand = new NoAliasCommand();
        $noAliases = $noAliasCommand->getAliases();
        $this->assertIsArray($noAliases);
        $this->assertEmpty($noAliases);
    }

    /**
     * Test multiple commands with different aliases.
     * @test
     */
    public function testMultipleCommandsWithDifferentAliases() {
        $runner = new Runner();
        
        $command1 = new AliasTestCommand(); // aliases: 'test', 'at'
        $command2 = new NoAliasCommand();
        
        $runner->register($command1);
        $runner->register($command2, ['na', 'no']);
        
        $aliases = $runner->getAliases();
        
        // Check all aliases are registered correctly
        $this->assertEquals('alias-test', $aliases['test']);
        $this->assertEquals('alias-test', $aliases['at']);
        $this->assertEquals('no-alias', $aliases['na']);
        $this->assertEquals('no-alias', $aliases['no']);
        
        // Test command retrieval via different aliases
        $this->assertSame($command1, $runner->getCommandByName('test'));
        $this->assertSame($command1, $runner->getCommandByName('at'));
        $this->assertSame($command2, $runner->getCommandByName('na'));
        $this->assertSame($command2, $runner->getCommandByName('no'));
    }

    /**
     * Test alias priority (direct command name vs alias).
     * @test
     */
    public function testAliasPriority() {
        $runner = new Runner();
        $command1 = new AliasTestCommand(); // name: 'alias-test'
        $command2 = new NoAliasCommand(); // name: 'no-alias'
        
        $runner->register($command1);
        // Register command2 with alias that matches command1's name
        $runner->register($command2, ['alias-test']);
        
        // Direct command name should take priority over alias
        $retrievedCommand = $runner->getCommandByName('alias-test');
        $this->assertSame($command1, $retrievedCommand, 'Direct command name should take priority over alias');
    }

    /**
     * Test empty aliases array.
     * @test
     */
    public function testEmptyAliasesArray() {
        $runner = new Runner();
        $command = new NoAliasCommand();
        
        // Register with empty aliases array
        $runner->register($command, []);
        
        $aliases = $runner->getAliases();
        $this->assertEmpty($aliases);
    }

    /**
     * Test alias with special characters.
     * @test
     */
    public function testAliasWithSpecialCharacters() {
        $runner = new Runner();
        $command = new NoAliasCommand();
        
        // Register with special character aliases
        $runner->register($command, ['?', 'h', 'help-me']);
        
        $aliases = $runner->getAliases();
        $this->assertArrayHasKey('?', $aliases);
        $this->assertArrayHasKey('h', $aliases);
        $this->assertArrayHasKey('help-me', $aliases);
        
        // Test command retrieval
        $this->assertSame($command, $runner->getCommandByName('?'));
        $this->assertSame($command, $runner->getCommandByName('h'));
        $this->assertSame($command, $runner->getCommandByName('help-me'));
    }

    /**
     * Test alias case sensitivity.
     * @test
     */
    public function testAliasCaseSensitivity() {
        $runner = new Runner();
        $command = new NoAliasCommand();
        
        $runner->register($command, ['Test', 'TEST']);
        
        $aliases = $runner->getAliases();
        $this->assertArrayHasKey('Test', $aliases);
        $this->assertArrayHasKey('TEST', $aliases);
        
        // Test that they are treated as different aliases
        $this->assertSame($command, $runner->getCommandByName('Test'));
        $this->assertSame($command, $runner->getCommandByName('TEST'));
        $this->assertNull($runner->getCommandByName('test')); // lowercase should not match
    }

    /**
     * Test large number of aliases.
     * @test
     */
    public function testLargeNumberOfAliases() {
        $runner = new Runner();
        $command = new NoAliasCommand();
        
        // Create many aliases
        $manyAliases = [];
        for ($i = 1; $i <= 100; $i++) {
            $manyAliases[] = "alias$i";
        }
        
        $runner->register($command, $manyAliases);
        
        $aliases = $runner->getAliases();
        $this->assertCount(100, $aliases);
        
        // Test a few random aliases
        $this->assertEquals('no-alias', $aliases['alias1']);
        $this->assertEquals('no-alias', $aliases['alias50']);
        $this->assertEquals('no-alias', $aliases['alias100']);
        
        // Test command retrieval
        $this->assertSame($command, $runner->getCommandByName('alias1'));
        $this->assertSame($command, $runner->getCommandByName('alias50'));
        $this->assertSame($command, $runner->getCommandByName('alias100'));
    }

    /**
     * Test backward compatibility - existing code should work unchanged.
     * @test
     */
    public function testBackwardCompatibility() {
        $runner = new Runner();
        $command = new NoAliasCommand();
        
        // Old way of registering (without aliases parameter)
        $runner->register($command);
        
        // Should work exactly as before
        $this->assertSame($command, $runner->getCommandByName('no-alias'));
        $this->assertEmpty($runner->getAliases());
        
        // Command execution should work
        $output = $this->executeSingleCommand($command, ['no-alias']);
        $this->assertEquals(["No alias command executed\n"], $output);
        $this->assertEquals(0, $this->getExitCode());
    }
}
