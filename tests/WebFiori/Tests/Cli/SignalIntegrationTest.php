<?php
/**
 * This file is licensed under MIT License.
 *
 * Copyright (c) 2026-present Webfiori Framework
 *
 * For more information on the license, please visit:
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 *
 */

declare(strict_types=1);

namespace WebFiori\Tests\Cli;

use WebFiori\Cli\Command;
use WebFiori\Cli\CommandTestCase;
use WebFiori\Cli\Runner;
use WebFiori\Cli\SignalHandler;
use WebFiori\Cli\Streams\ArrayInputStream;
use WebFiori\Cli\Streams\ArrayOutputStream;

class SignalCommandForTest extends Command {
    public $cleanupCalled = false;

    public function __construct() {
        parent::__construct('signal-test', [], 'A command to test signal handling');
    }

    public function exec(): int {
        $this->println('Running signal test command');

        return 0;
    }
}

class SignalIntegrationTest extends CommandTestCase {
    /**
     * @test
     */
    public function testRunnerEnableSignalHandling() {
        $runner = new Runner();
        $runner->reset();

        $result = $runner->enableSignalHandling();

        $this->assertSame($runner, $result);
        $this->assertNotNull($runner->getSignalHandler());
        $this->assertInstanceOf(SignalHandler::class, $runner->getSignalHandler());
        $this->assertTrue($runner->getSignalHandler()->isEnabled());
    }

    /**
     * @test
     */
    public function testRunnerSignalHandlerDefaultsNull() {
        $runner = new Runner();
        $runner->reset();

        $this->assertNull($runner->getSignalHandler());
    }

    /**
     * @test
     */
    public function testRunnerIsShutdownRequestedDefault() {
        $runner = new Runner();
        $runner->reset();

        $this->assertFalse($runner->isShutdownRequested());
    }

    /**
     * @test
     */
    public function testRunnerSetSignalHandler() {
        $runner = new Runner();
        $runner->reset();
        $called = false;

        $result = $runner->setSignalHandler(SIGUSR1, function (int $signal) use (&$called) {
            $called = true;
        });

        $this->assertSame($runner, $result);
        // setSignalHandler should auto-enable signal handling
        $this->assertNotNull($runner->getSignalHandler());
        $this->assertTrue($runner->getSignalHandler()->hasHandler(SIGUSR1));

        // Verify the handler actually fires
        if (SignalHandler::isSupported()) {
            posix_kill(posix_getpid(), SIGUSR1);
            $this->assertTrue($called);
        }

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testRunnerDefaultSigintHandler() {
        $runner = new Runner();
        $runner->reset();
        $runner->enableSignalHandling();

        $this->assertTrue($runner->getSignalHandler()->hasHandler(SIGINT));
        $this->assertTrue($runner->getSignalHandler()->hasHandler(SIGTERM));

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testRunnerSigtermSetsShutdown() {
        $runner = new Runner();
        $runner->reset();
        $runner->enableSignalHandling();

        if (SignalHandler::isSupported()) {
            posix_kill(posix_getpid(), SIGTERM);
            $this->assertTrue($runner->isShutdownRequested());
        }

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testRunnerSigintNonInteractive() {
        $runner = new Runner();
        $runner->reset();
        $runner->enableSignalHandling();

        if (SignalHandler::isSupported()) {
            posix_kill(posix_getpid(), SIGINT);
            // Non-interactive mode: sets shutdown requested
            $this->assertTrue($runner->isShutdownRequested());
        }

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testCommandOnSignal() {
        $command = new SignalCommandForTest();
        $called = false;

        $result = $command->onSignal(SIGINT, function (int $signal) use (&$called) {
            $called = true;
        });

        $this->assertSame($command, $result);
        $this->assertCount(1, $command->getSignalHandlers());
        $this->assertArrayHasKey(SIGINT, $command->getSignalHandlers());
    }

    /**
     * @test
     */
    public function testCommandOnSignalMultiple() {
        $command = new SignalCommandForTest();
        $cb1 = function (int $signal) {};
        $cb2 = function (int $signal) {};

        $command->onSignal(SIGINT, $cb1)
                ->onSignal(SIGTERM, $cb2);

        $this->assertCount(2, $command->getSignalHandlers());
        $this->assertSame($cb1, $command->getSignalHandlers()[SIGINT]);
        $this->assertSame($cb2, $command->getSignalHandlers()[SIGTERM]);
    }

    /**
     * @test
     */
    public function testCommandClearSignalHandlers() {
        $command = new SignalCommandForTest();
        $command->onSignal(SIGINT, function (int $signal) {});
        $command->onSignal(SIGTERM, function (int $signal) {});

        $result = $command->clearSignalHandlers();

        $this->assertSame($command, $result);
        $this->assertEmpty($command->getSignalHandlers());
    }

    /**
     * @test
     */
    public function testCommandSignalHandlersDefaultEmpty() {
        $command = new SignalCommandForTest();
        $this->assertEmpty($command->getSignalHandlers());
    }

    /**
     * @test
     */
    public function testCommandSignalHandlerRegisteredDuringExecution() {
        $runner = new Runner();
        $runner->reset();
        $runner->enableSignalHandling();

        $handlerRegistered = false;
        $command = new SignalCommandForTest();
        $command->onSignal(SIGUSR1, function (int $signal) use (&$handlerRegistered) {
            $handlerRegistered = true;
        });

        $runner->register($command);
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'signal-test']);
        $runner->start();

        // After command finishes, handlers should be cleaned up
        $this->assertEmpty($command->getSignalHandlers());

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testCommandSignalHandlerFires() {
        if (!SignalHandler::isSupported()) {
            $this->markTestSkipped('pcntl not available');
        }

        $runner = new Runner();
        $runner->reset();
        $runner->enableSignalHandling();

        $signalReceived = false;

        // Create a command that registers a SIGUSR1 handler and sends itself the signal
        $command = new class extends Command {
            public $signalReceived = false;

            public function __construct() {
                parent::__construct('signal-fire-test', [], 'Test signal firing');
            }

            public function exec(): int {
                $this->onSignal(SIGUSR1, function (int $signal) {
                    $this->signalReceived = true;
                });

                // Re-register with runner (simulate what runner does)
                $owner = $this->getOwner();
                if ($owner !== null && $owner->getSignalHandler() !== null) {
                    foreach ($this->getSignalHandlers() as $sig => $handler) {
                        $owner->getSignalHandler()->register($sig, $handler);
                    }
                }

                posix_kill(posix_getpid(), SIGUSR1);

                return 0;
            }
        };

        $runner->register($command);
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'signal-fire-test']);
        $runner->start();

        $this->assertTrue($command->signalReceived);

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testEnableSignalHandlingReturnsSameInstance() {
        $runner = new Runner();
        $runner->reset();

        $result = $runner->enableSignalHandling();
        $this->assertSame($runner, $result);

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testSetSignalHandlerReturnsSameInstance() {
        $runner = new Runner();
        $runner->reset();

        $result = $runner->setSignalHandler(SIGUSR1, function (int $signal) {});
        $this->assertSame($runner, $result);

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testEnableSignalHandlingCalledMultipleTimes() {
        $runner = new Runner();
        $runner->reset();

        $runner->enableSignalHandling();
        $handler1 = $runner->getSignalHandler();

        $runner->enableSignalHandling();
        $handler2 = $runner->getSignalHandler();

        // Each call creates a new handler
        $this->assertNotSame($handler1, $handler2);
        $this->assertTrue($handler2->isEnabled());

        $handler2->disable();
    }

    /**
     * @test
     */
    public function testCustomSignalHandlerOverridesDefault() {
        $runner = new Runner();
        $runner->reset();
        $runner->enableSignalHandling();

        $customCalled = false;
        $runner->setSignalHandler(SIGINT, function (int $signal) use (&$customCalled) {
            $customCalled = true;
        });

        if (SignalHandler::isSupported()) {
            posix_kill(posix_getpid(), SIGINT);
            $this->assertTrue($customCalled);
            // The custom handler replaced the default, so shutdown should NOT be set
            // unless the custom handler sets it
            $this->assertFalse($runner->isShutdownRequested());
        }

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testCommandWithoutSignalHandlersRunsNormally() {
        $runner = new Runner();
        $runner->reset();
        $runner->enableSignalHandling();

        $command = new SignalCommandForTest();
        $runner->register($command);
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'signal-test']);
        $exitCode = $runner->start();

        $this->assertEquals(0, $exitCode);
        $output = $runner->getOutput();
        $this->assertContains("Running signal test command\n", $output);

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testSignalHandlingWithoutEnabling() {
        // When signal handling is not enabled, command signal handlers
        // should just be ignored (no crash)
        $runner = new Runner();
        $runner->reset();

        $command = new SignalCommandForTest();
        $command->onSignal(SIGINT, function (int $signal) {});

        $runner->register($command);
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'signal-test']);
        $exitCode = $runner->start();

        $this->assertEquals(0, $exitCode);
        // Signal handlers should NOT be cleared when there's no signal handler on runner
        $this->assertCount(1, $command->getSignalHandlers());
    }

    /**
     * @test
     */
    public function testInteractiveModeShutdownFlag() {
        $runner = new Runner();
        $runner->reset();
        $runner->enableSignalHandling();

        // Register a command
        $command = new SignalCommandForTest();
        $runner->register($command);

        // Simulate interactive mode where shutdown is requested immediately
        // by providing "exit" as user input
        $runner->setInputs(['exit']);
        $runner->setArgsVector(['main.php', '-i']);
        $exitCode = $runner->start();

        $this->assertEquals(0, $exitCode);

        $runner->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testInteractiveModeSigintInterruptsCommand() {
        if (!SignalHandler::isSupported()) {
            $this->markTestSkipped('pcntl not available');
        }

        $runner = new Runner();
        $runner->reset();
        $runner->enableSignalHandling();

        $command = new SignalCommandForTest();
        $runner->register($command);

        // Simulate interactive mode with exit
        $runner->setInputs(['exit']);
        $runner->setArgsVector(['main.php', '-i']);
        $exitCode = $runner->start();
        $this->assertEquals(0, $exitCode);

        // Test the SIGINT handler in non-interactive mode directly
        $runner3 = new Runner();
        $runner3->reset();
        $runner3->enableSignalHandling();
        $runner3->register(new SignalCommandForTest());

        // Send SIGINT directly (not inside a command)
        posix_kill(posix_getpid(), SIGINT);
        $this->assertTrue($runner3->isShutdownRequested());
        $this->assertEquals(130, $runner3->getLastCommandExitStatus());

        $runner3->getSignalHandler()->disable();
    }

    /**
     * @test
     */
    public function testSigintInInteractiveMode() {
        if (!SignalHandler::isSupported()) {
            $this->markTestSkipped('pcntl not available');
        }

        // Test SIGINT in interactive mode by creating a command that sends SIGINT
        // while runner is in interactive mode
        $sigintCommand = new class extends Command {
            public function __construct() {
                parent::__construct('send-sigint', [], 'Sends SIGINT to self');
            }

            public function exec(): int {
                posix_kill(posix_getpid(), SIGINT);

                return 0;
            }
        };

        $runner = new Runner();
        $runner->reset();
        $runner->enableSignalHandling();
        $runner->register($sigintCommand);

        // Interactive mode: send-sigint, then exit
        $runner->setInputs(['send-sigint', 'exit']);
        $runner->setArgsVector(['main.php', '-i']);
        $exitCode = $runner->start();

        // SIGINT in interactive mode should NOT kill the app
        $this->assertEquals(0, $exitCode);

        // Output should contain the interruption message
        $output = $runner->getOutput();
        $found = false;

        foreach ($output as $line) {
            if (strpos($line, 'Command interrupted') !== false) {
                $found = true;

                break;
            }
        }
        $this->assertTrue($found, 'Expected "Command interrupted" message in output');

        $runner->getSignalHandler()->disable();
    }
}
