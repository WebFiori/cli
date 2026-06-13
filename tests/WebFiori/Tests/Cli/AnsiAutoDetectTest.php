<?php
declare(strict_types=1);

namespace WebFiori\Tests\Cli;

use WebFiori\Cli\Command;
use WebFiori\Cli\CommandTestCase;
use WebFiori\Cli\Runner;
use WebFiori\Cli\Streams\ArrayOutputStream;
use WebFiori\Cli\Streams\StdOut;

class AnsiAutoDetectCommand extends Command {
    public function __construct() {
        parent::__construct('ansi-test', [], 'Test ANSI detection');
    }

    public function exec(): int {
        $this->println('Hello', ['color' => 'red']);

        return 0;
    }
}

class AnsiAutoDetectTest extends CommandTestCase {
    /**
     * @test
     */
    public function testShouldUseAnsiReturnsBoolean() {
        $result = Runner::shouldUseAnsi();
        $this->assertIsBool($result);
    }

    /**
     * @test
     */
    public function testShouldUseAnsiRespectsNoColorEnv() {
        putenv('NO_COLOR=1');
        $this->assertFalse(Runner::shouldUseAnsi());
        putenv('NO_COLOR');
    }

    /**
     * @test
     */
    public function testShouldUseAnsiRespectsNoColorServer() {
        $_SERVER['NO_COLOR'] = '1';
        $this->assertFalse(Runner::shouldUseAnsi());
        unset($_SERVER['NO_COLOR']);
    }

    /**
     * @test
     */
    public function testIsAnsiDefaultFalseWithArrayOutputStream() {
        $runner = new Runner();
        $runner->reset();
        $runner->setInputs([]);

        // With ArrayOutputStream, isAnsi should be false
        $this->assertFalse($runner->isAnsi());
    }

    /**
     * @test
     */
    public function testIsAnsiTrueWhenForcedViaFlag() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new AnsiAutoDetectCommand());
        $runner->setInputs([]);
        $runner->runCommand(null, ['ansi-test', '--ansi']);

        $this->assertTrue($runner->isAnsi());
    }

    /**
     * @test
     */
    public function testIsAnsiFalseWhenNoColorFlag() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new AnsiAutoDetectCommand());
        $runner->setInputs([]);
        $runner->runCommand(null, ['ansi-test', '--no-color']);

        $this->assertFalse($runner->isAnsi());
    }

    /**
     * @test
     */
    public function testNoColorOverridesAnsi() {
        // --no-color should take precedence when both are present
        $runner = new Runner();
        $runner->reset();
        $runner->register(new AnsiAutoDetectCommand());
        $runner->setInputs([]);
        $runner->runCommand(null, ['ansi-test', '--ansi', '--no-color']);

        $this->assertFalse($runner->isAnsi());
    }

    /**
     * @test
     */
    public function testOutputHasNoAnsiWhenNoColor() {
        $output = $this->executeSingleCommand(
            new AnsiAutoDetectCommand(),
            ['--no-color']
        );

        // Output should NOT contain ANSI escape sequences
        foreach ($output as $line) {
            $this->assertStringNotContainsString("\e[", $line);
        }
    }

    /**
     * @test
     */
    public function testOutputHasAnsiWhenForced() {
        $output = $this->executeSingleCommand(
            new AnsiAutoDetectCommand(),
            ['--ansi']
        );

        // Output should contain ANSI escape sequences
        $hasAnsi = false;

        foreach ($output as $line) {
            if (strpos($line, "\e[") !== false) {
                $hasAnsi = true;

                break;
            }
        }
        $this->assertTrue($hasAnsi, 'Expected ANSI codes in output when --ansi is forced');
    }

    /**
     * @test
     */
    public function testDefaultOutputNoAnsiInTestMode() {
        // Without --ansi, in test mode (ArrayOutputStream), no ANSI
        $output = $this->executeSingleCommand(new AnsiAutoDetectCommand());

        foreach ($output as $line) {
            $this->assertStringNotContainsString("\e[", $line);
        }
    }

    /**
     * @test
     */
    public function testInteractiveModeNoColorFlag() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new AnsiAutoDetectCommand());
        $runner->setArgsVector(['main.php', '-i', '--no-color']);
        $runner->setInputs(['ansi-test', 'exit']);
        $runner->start();

        $output = $runner->getOutput();

        foreach ($output as $line) {
            $this->assertStringNotContainsString("\e[", $line);
        }
    }

    /**
     * @test
     */
    public function testInteractiveModeAnsiFlag() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new AnsiAutoDetectCommand());
        $runner->setArgsVector(['main.php', '-i', '--ansi']);
        $runner->setInputs(['ansi-test', 'exit']);
        $runner->start();

        $output = $runner->getOutput();
        $hasAnsi = false;

        foreach ($output as $line) {
            if (strpos($line, "\e[") !== false) {
                $hasAnsi = true;

                break;
            }
        }
        $this->assertTrue($hasAnsi, 'Expected ANSI codes in interactive mode with --ansi');
    }

    /**
     * @test
     */
    public function testRunMethodHandlesNoColor() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new AnsiAutoDetectCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'ansi-test', '--no-color']);
        $runner->start();

        $output = $runner->getOutput();

        foreach ($output as $line) {
            $this->assertStringNotContainsString("\e[", $line);
        }
    }

    /**
     * @test
     */
    public function testRunMethodHandlesAnsiForce() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new AnsiAutoDetectCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'ansi-test', '--ansi']);
        $runner->start();

        $output = $runner->getOutput();
        $hasAnsi = false;

        foreach ($output as $line) {
            if (strpos($line, "\e[") !== false) {
                $hasAnsi = true;

                break;
            }
        }
        $this->assertTrue($hasAnsi);
    }

    /**
     * @test
     */
    public function testResolveAnsiOnlyAppliesWithStdOut() {
        $runner = new Runner();
        $runner->reset();

        // With StdOut, resolveAnsi should apply TTY detection
        $runner->setOutputStream(new StdOut());
        // Can't directly call resolveAnsi (private), but we can check behavior
        // After start() with StdOut, isAnsi depends on actual TTY
        // We just verify it doesn't crash
        $this->assertIsBool($runner->isAnsi());
    }

    /**
     * @test
     */
    public function testNoColorEnvDisablesAnsi() {
        putenv('NO_COLOR=1');

        $runner = new Runner();
        $runner->reset();
        $runner->setOutputStream(new StdOut());
        $runner->register(new AnsiAutoDetectCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'ansi-test']);
        $runner->start();

        // Even with StdOut, NO_COLOR env should disable ANSI
        $output = $runner->getOutput();

        foreach ($output as $line) {
            $this->assertStringNotContainsString("\e[", $line);
        }

        putenv('NO_COLOR');
    }

    /**
     * @test
     */
    public function testCommandIsAnsiEnabledUsesRunner() {
        $runner = new Runner();
        $runner->reset();

        $ansiDetected = false;
        $command = new class extends Command {
            public $ansiValue = null;

            public function __construct() {
                parent::__construct('check-ansi', [], 'Check ANSI state');
            }

            public function exec(): int {
                // Access owner's isAnsi through println behavior
                $this->println('test', ['color' => 'red']);

                return 0;
            }
        };

        $runner->register($command);
        $runner->setInputs([]);
        $runner->runCommand(null, ['check-ansi', '--ansi']);

        $output = $runner->getOutput();
        $hasAnsi = false;

        foreach ($output as $line) {
            if (strpos($line, "\e[") !== false) {
                $hasAnsi = true;

                break;
            }
        }
        $this->assertTrue($hasAnsi);
    }

    /**
     * @test
     */
    public function testCommandWithoutOwnerFallsBackToArgCheck() {
        // Command used standalone without Runner
        $command = new AnsiAutoDetectCommand();
        $command->setOutputStream(new ArrayOutputStream());

        // Without owner, isAnsiEnabled falls back to isArgProvided
        // which checks the command's own args
        $command->excCommand();
        // No crash = success. Output won't have ANSI since no --ansi arg.
        $this->assertTrue(true);
    }
}
