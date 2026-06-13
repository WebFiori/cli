<?php
declare(strict_types=1);

namespace WebFiori\Tests\Cli;

use WebFiori\Cli\Command;
use WebFiori\Cli\CommandTestCase;
use WebFiori\Cli\Runner;
use WebFiori\Cli\Verbosity;

class VerbosityTestCommand extends Command {
    public function __construct() {
        parent::__construct('verb-test', [], 'Test verbosity levels');
    }

    public function exec(): int {
        $this->error('error msg');
        $this->warning('warning msg');
        $this->info('info msg');
        $this->success('success msg');
        $this->verbose('verbose msg');
        $this->debug('debug msg');
        $this->println('always shown');

        return 0;
    }
}

class VerbosityTest extends CommandTestCase {
    /**
     * @test
     */
    public function testVerbosityConstants() {
        $this->assertEquals(0, Verbosity::QUIET);
        $this->assertEquals(1, Verbosity::NORMAL);
        $this->assertEquals(2, Verbosity::VERBOSE);
        $this->assertEquals(3, Verbosity::DEBUG);
    }

    /**
     * @test
     */
    public function testDefaultVerbosityIsNormal() {
        $runner = new Runner();
        $runner->reset();
        $this->assertEquals(Verbosity::NORMAL, $runner->getVerbosity());
    }

    /**
     * @test
     */
    public function testSetVerbosity() {
        $runner = new Runner();
        $runner->reset();

        $result = $runner->setVerbosity(Verbosity::QUIET);
        $this->assertSame($runner, $result);
        $this->assertEquals(Verbosity::QUIET, $runner->getVerbosity());

        $runner->setVerbosity(Verbosity::VERBOSE);
        $this->assertEquals(Verbosity::VERBOSE, $runner->getVerbosity());

        $runner->setVerbosity(Verbosity::DEBUG);
        $this->assertEquals(Verbosity::DEBUG, $runner->getVerbosity());
    }

    /**
     * @test
     */
    public function testQuietModeViaFlag() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new VerbosityTestCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'verb-test', '-q']);
        $runner->start();

        $output = $runner->getOutput();
        $outputStr = implode('', $output);

        // error and warning always shown
        $this->assertStringContainsString('error msg', $outputStr);
        $this->assertStringContainsString('warning msg', $outputStr);
        // println always shown
        $this->assertStringContainsString('always shown', $outputStr);
        // info and success suppressed
        $this->assertStringNotContainsString('info msg', $outputStr);
        $this->assertStringNotContainsString('success msg', $outputStr);
        // verbose and debug suppressed
        $this->assertStringNotContainsString('verbose msg', $outputStr);
        $this->assertStringNotContainsString('debug msg', $outputStr);
    }

    /**
     * @test
     */
    public function testNormalMode() {
        $output = $this->executeSingleCommand(new VerbosityTestCommand());
        $outputStr = implode('', $output);

        // error, warning, info, success shown
        $this->assertStringContainsString('error msg', $outputStr);
        $this->assertStringContainsString('warning msg', $outputStr);
        $this->assertStringContainsString('info msg', $outputStr);
        $this->assertStringContainsString('success msg', $outputStr);
        $this->assertStringContainsString('always shown', $outputStr);
        // verbose and debug suppressed
        $this->assertStringNotContainsString('verbose msg', $outputStr);
        $this->assertStringNotContainsString('debug msg', $outputStr);
    }

    /**
     * @test
     */
    public function testVerboseModeViaFlag() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new VerbosityTestCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'verb-test', '-v']);
        $runner->start();

        $output = $runner->getOutput();
        $outputStr = implode('', $output);

        // everything except debug shown
        $this->assertStringContainsString('error msg', $outputStr);
        $this->assertStringContainsString('warning msg', $outputStr);
        $this->assertStringContainsString('info msg', $outputStr);
        $this->assertStringContainsString('success msg', $outputStr);
        $this->assertStringContainsString('verbose msg', $outputStr);
        $this->assertStringContainsString('always shown', $outputStr);
        // debug suppressed
        $this->assertStringNotContainsString('debug msg', $outputStr);
    }

    /**
     * @test
     */
    public function testDebugModeViaFlag() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new VerbosityTestCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'verb-test', '-vv']);
        $runner->start();

        $output = $runner->getOutput();
        $outputStr = implode('', $output);

        // everything shown
        $this->assertStringContainsString('error msg', $outputStr);
        $this->assertStringContainsString('warning msg', $outputStr);
        $this->assertStringContainsString('info msg', $outputStr);
        $this->assertStringContainsString('success msg', $outputStr);
        $this->assertStringContainsString('verbose msg', $outputStr);
        $this->assertStringContainsString('debug msg', $outputStr);
        $this->assertStringContainsString('always shown', $outputStr);
    }

    /**
     * @test
     */
    public function testSetVerbosityProgrammatically() {
        $runner = new Runner();
        $runner->reset();
        $runner->setVerbosity(Verbosity::QUIET);
        $runner->register(new VerbosityTestCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'verb-test']);
        $runner->start();

        $output = $runner->getOutput();
        $outputStr = implode('', $output);

        $this->assertStringContainsString('error msg', $outputStr);
        $this->assertStringNotContainsString('info msg', $outputStr);
    }

    /**
     * @test
     */
    public function testFlagOverridesProgrammaticVerbosity() {
        $runner = new Runner();
        $runner->reset();
        $runner->setVerbosity(Verbosity::QUIET);
        $runner->register(new VerbosityTestCommand());
        $runner->setInputs([]);
        // -vv flag should override the programmatic QUIET setting
        $runner->setArgsVector(['main.php', 'verb-test', '-vv']);
        $runner->start();

        $output = $runner->getOutput();
        $outputStr = implode('', $output);

        $this->assertStringContainsString('debug msg', $outputStr);
    }

    /**
     * @test
     */
    public function testVerbosityFlagsStrippedFromArgs() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new VerbosityTestCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'verb-test', '-v']);
        $runner->start();

        // Command should execute successfully (flag not passed as unknown arg)
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
    }

    /**
     * @test
     */
    public function testQuietFlagStrippedFromArgs() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new VerbosityTestCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'verb-test', '-q']);
        $runner->start();

        $this->assertEquals(0, $runner->getLastCommandExitStatus());
    }

    /**
     * @test
     */
    public function testCommandWithoutOwnerDefaultsToNormal() {
        // Command without Runner uses NORMAL verbosity
        $command = new VerbosityTestCommand();
        $output = $this->executeSingleCommand($command);
        $outputStr = implode('', $output);

        $this->assertStringContainsString('info msg', $outputStr);
        $this->assertStringNotContainsString('verbose msg', $outputStr);
    }

    /**
     * @test
     */
    public function testInteractiveModeWithQuiet() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new VerbosityTestCommand());
        $runner->setArgsVector(['main.php', '-i', '-q']);
        $runner->setInputs(['verb-test', 'exit']);
        $runner->start();

        $output = $runner->getOutput();
        $outputStr = implode('', $output);

        $this->assertStringContainsString('error msg', $outputStr);
        $this->assertStringNotContainsString('info msg', $outputStr);
    }
}
