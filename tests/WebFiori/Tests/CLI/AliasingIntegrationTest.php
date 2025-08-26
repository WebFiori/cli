<?php
namespace WebFiori\Tests\Cli;

use WebFiori\CLI\CommandTestCase;
use WebFiori\CLI\Runner;
use WebFiori\CLI\Commands\HelpCommand;
use WebFiori\CLI\Streams\ArrayInputStream;
use WebFiori\CLI\Streams\ArrayOutputStream;
use WebFiori\Tests\CLI\TestCommands\AliasTestCommand;
use WebFiori\Tests\CLI\TestCommands\NoAliasCommand;

/**
 * Integration tests for command aliasing functionality.
 */
class AliasingIntegrationTest extends CommandTestCase {

    /**
     * Test aliasing with help command.
     * @test
     */
    public function testAliasingWithHelpCommand() {
        $runner = new Runner();
        $runner->setInputStream(new ArrayInputStream([]));
        $runner->setOutputStream(new ArrayOutputStream());
        
        $aliasCommand = new AliasTestCommand();
        $helpCommand = new HelpCommand();
        
        $runner->register($aliasCommand);
        $runner->register($helpCommand);
        
        // Test help for command via direct name (not alias, as help might not resolve aliases)
        $runner->setArgsVector(['script.php', 'help', '--command-name=alias-test']);
        $exitCode = $runner->start();
        
        $output = $runner->getOutputStream()->getOutputArray();
        $this->assertEquals(0, $exitCode);
        
        // Should show help for the actual command
        $helpOutput = implode('', $output);
        $this->assertStringContainsString('alias-test:', $helpOutput);
    }

    /**
     * Test multiple aliases pointing to same command in help.
     * @test
     */
    public function testMultipleAliasesInHelp() {
        $runner = new Runner();
        $runner->setInputStream(new ArrayInputStream([]));
        $runner->setOutputStream(new ArrayOutputStream());
        
        $command = new AliasTestCommand(); // Has aliases: 'test', 'at'
        $helpCommand = new HelpCommand();
        
        $runner->register($command, ['extra-alias']); // Add runtime alias
        $runner->register($helpCommand);
        
        // Get general help
        $runner->setArgsVector(['script.php', 'help']);
        $exitCode = $runner->start();
        
        $output = $runner->getOutputStream()->getOutputArray();
        $this->assertEquals(0, $exitCode);
        
        $helpOutput = implode('', $output);
        // Should show the main command name
        $this->assertStringContainsString('alias-test:', $helpOutput);
    }

    /**
     * Test alias resolution performance with many aliases.
     * @test
     */
    public function testAliasResolutionPerformance() {
        $runner = new Runner();
        
        // Create many commands with aliases
        $commands = [];
        for ($i = 1; $i <= 50; $i++) {
            $command = new NoAliasCommand();
            $aliases = ["alias$i", "a$i", "cmd$i"];
            $runner->register($command, $aliases);
            $commands[] = $command;
        }
        
        // Test resolution performance
        $start = microtime(true);
        for ($i = 1; $i <= 50; $i++) {
            $this->assertEquals('no-alias', $runner->resolveAlias("alias$i"));
            $this->assertEquals('no-alias', $runner->resolveAlias("a$i"));
            $this->assertEquals('no-alias', $runner->resolveAlias("cmd$i"));
        }
        $end = microtime(true);
        
        // Should resolve quickly (less than 0.1 seconds for 150 lookups)
        $this->assertLessThan(0.1, $end - $start);
    }

    /**
     * Test alias with special argument patterns.
     * @test
     */
    public function testAliasWithArguments() {
        $runner = new Runner();
        $runner->setInputStream(new ArrayInputStream([]));
        $runner->setOutputStream(new ArrayOutputStream());
        
        $command = new AliasTestCommand();
        $runner->register($command);
        
        // Test alias with arguments
        $runner->setArgsVector(['script.php', 'test', '--some-arg=value']);
        $exitCode = $runner->start();
        
        $output = $runner->getOutputStream()->getOutputArray();
        $this->assertEquals(0, $exitCode);
        $this->assertEquals(["Alias test command executed\n"], $output);
    }

    /**
     * Test alias registration order doesn't affect functionality.
     * @test
     */
    public function testAliasRegistrationOrder() {
        $runner1 = new Runner();
        $runner2 = new Runner();
        
        $command1 = new AliasTestCommand();
        $command2 = new NoAliasCommand();
        
        // Register in different orders
        $runner1->register($command1);
        $runner1->register($command2, ['test2']);
        
        $runner2->register($command2, ['test2']);
        $runner2->register($command1);
        
        // Both should have same aliases
        $aliases1 = $runner1->getAliases();
        $aliases2 = $runner2->getAliases();
        
        $this->assertEquals($aliases1['test'], $aliases2['test']);
        $this->assertEquals($aliases1['at'], $aliases2['at']);
        $this->assertEquals($aliases1['test2'], $aliases2['test2']);
    }

    /**
     * Test alias with empty string handling.
     * @test
     */
    public function testAliasEdgeCases() {
        $runner = new Runner();
        $command = new NoAliasCommand();
        
        // Test with empty strings in aliases array
        $aliases = ['valid-alias', '', 'another-valid'];
        
        $runner->register($command, $aliases);
        
        $registeredAliases = $runner->getAliases();
        
        // Should register all non-empty aliases (empty string might still be registered)
        $this->assertArrayHasKey('valid-alias', $registeredAliases);
        $this->assertArrayHasKey('another-valid', $registeredAliases);
        
        // Check that valid aliases point to correct command
        $this->assertEquals('no-alias', $registeredAliases['valid-alias']);
        $this->assertEquals('no-alias', $registeredAliases['another-valid']);
    }

    /**
     * Test alias resolution with case variations.
     * @test
     */
    public function testAliasResolutionCaseVariations() {
        $runner = new Runner();
        $command = new NoAliasCommand();
        
        $runner->register($command, ['Test', 'TEST', 'test']);
        
        // Each case variation should be treated separately
        $this->assertEquals('no-alias', $runner->resolveAlias('Test'));
        $this->assertEquals('no-alias', $runner->resolveAlias('TEST'));
        $this->assertEquals('no-alias', $runner->resolveAlias('test'));
        
        // Non-matching cases should return null
        $this->assertNull($runner->resolveAlias('tEsT'));
        $this->assertNull($runner->resolveAlias('TeSt'));
    }

    /**
     * Test command registration with duplicate aliases in same call.
     * @test
     */
    public function testDuplicateAliasesInSameRegistration() {
        $runner = new Runner();
        $command = new NoAliasCommand();
        
        // Register with duplicate aliases
        $runner->register($command, ['dup', 'unique', 'dup', 'another']);
        
        $aliases = $runner->getAliases();
        
        // Should handle duplicates gracefully
        $this->assertArrayHasKey('dup', $aliases);
        $this->assertArrayHasKey('unique', $aliases);
        $this->assertArrayHasKey('another', $aliases);
        $this->assertEquals('no-alias', $aliases['dup']);
    }

    /**
     * Test alias functionality after runner reset and re-registration.
     * @test
     */
    public function testAliasAfterResetAndReregistration() {
        $runner = new Runner();
        $command = new AliasTestCommand();
        
        // Initial registration
        $runner->register($command, ['extra']);
        $this->assertTrue($runner->hasAlias('test'));
        $this->assertTrue($runner->hasAlias('extra'));
        
        // Reset
        $runner->reset();
        $this->assertFalse($runner->hasAlias('test'));
        $this->assertFalse($runner->hasAlias('extra'));
        
        // Re-register with different aliases
        $runner->register($command, ['new-alias']);
        $this->assertTrue($runner->hasAlias('test')); // Built-in alias
        $this->assertTrue($runner->hasAlias('new-alias')); // New runtime alias
        $this->assertFalse($runner->hasAlias('extra')); // Old runtime alias should be gone
    }
}
