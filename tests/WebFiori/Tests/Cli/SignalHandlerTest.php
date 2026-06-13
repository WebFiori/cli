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

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\SignalHandler;

class SignalHandlerTest extends TestCase {
    /**
     * @test
     */
    public function testIsSupported() {
        // On Linux CLI, pcntl should be available
        $result = SignalHandler::isSupported();
        $this->assertIsBool($result);

        if (function_exists('pcntl_async_signals')) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @test
     */
    public function testInitialState() {
        $handler = new SignalHandler();
        $this->assertFalse($handler->isEnabled());
        $this->assertEmpty($handler->getHandlers());
    }

    /**
     * @test
     */
    public function testRegisterHandler() {
        $handler = new SignalHandler();
        $callback = function (int $signal) {};

        $result = $handler->register(SIGINT, $callback);

        $this->assertSame($handler, $result);
        $this->assertTrue($handler->hasHandler(SIGINT));
        $this->assertCount(1, $handler->getHandlers());
        $this->assertSame($callback, $handler->getHandlers()[SIGINT]);
    }

    /**
     * @test
     */
    public function testRegisterMultipleHandlers() {
        $handler = new SignalHandler();
        $cb1 = function (int $signal) {};
        $cb2 = function (int $signal) {};

        $handler->register(SIGINT, $cb1)
                ->register(SIGTERM, $cb2);

        $this->assertTrue($handler->hasHandler(SIGINT));
        $this->assertTrue($handler->hasHandler(SIGTERM));
        $this->assertCount(2, $handler->getHandlers());
    }

    /**
     * @test
     */
    public function testRegisterOverwritesExisting() {
        $handler = new SignalHandler();
        $cb1 = function (int $signal) { return 1; };
        $cb2 = function (int $signal) { return 2; };

        $handler->register(SIGINT, $cb1);
        $handler->register(SIGINT, $cb2);

        $this->assertCount(1, $handler->getHandlers());
        $this->assertSame($cb2, $handler->getHandlers()[SIGINT]);
    }

    /**
     * @test
     */
    public function testRemoveHandler() {
        $handler = new SignalHandler();
        $callback = function (int $signal) {};

        $handler->register(SIGINT, $callback);
        $result = $handler->remove(SIGINT);

        $this->assertSame($handler, $result);
        $this->assertFalse($handler->hasHandler(SIGINT));
        $this->assertEmpty($handler->getHandlers());
    }

    /**
     * @test
     */
    public function testRemoveNonExistentHandler() {
        $handler = new SignalHandler();
        $result = $handler->remove(SIGINT);

        $this->assertSame($handler, $result);
        $this->assertFalse($handler->hasHandler(SIGINT));
    }

    /**
     * @test
     */
    public function testEnable() {
        $handler = new SignalHandler();
        $result = $handler->enable();

        $this->assertSame($handler, $result);
        $this->assertTrue($handler->isEnabled());
    }

    /**
     * @test
     */
    public function testDisable() {
        $handler = new SignalHandler();
        $handler->enable();
        $result = $handler->disable();

        $this->assertSame($handler, $result);
        $this->assertFalse($handler->isEnabled());
    }

    /**
     * @test
     */
    public function testEnableWithRegisteredHandlers() {
        $handler = new SignalHandler();
        $called = false;
        $callback = function (int $signal) use (&$called) {
            $called = true;
        };

        $handler->register(SIGUSR1, $callback);
        $handler->enable();

        $this->assertTrue($handler->isEnabled());
        $this->assertTrue($handler->hasHandler(SIGUSR1));

        // Send SIGUSR1 to current process to verify handler is installed
        if (SignalHandler::isSupported()) {
            posix_kill(posix_getpid(), SIGUSR1);
            $this->assertTrue($called);
        }

        $handler->disable();
    }

    /**
     * @test
     */
    public function testRegisterWhileEnabled() {
        $handler = new SignalHandler();
        $called = false;
        $callback = function (int $signal) use (&$called) {
            $called = true;
        };

        $handler->enable();
        $handler->register(SIGUSR1, $callback);

        if (SignalHandler::isSupported()) {
            posix_kill(posix_getpid(), SIGUSR1);
            $this->assertTrue($called);
        }

        $handler->disable();
    }

    /**
     * @test
     */
    public function testRemoveWhileEnabled() {
        $handler = new SignalHandler();
        $called = false;
        $callback = function (int $signal) use (&$called) {
            $called = true;
        };

        $handler->register(SIGUSR1, $callback);
        $handler->enable();
        $handler->remove(SIGUSR1);

        // After removal, signal should use default behavior
        // We won't send the signal as default for SIGUSR1 is termination
        $this->assertFalse($handler->hasHandler(SIGUSR1));

        $handler->disable();
    }

    /**
     * @test
     */
    public function testHasHandlerReturnsFalse() {
        $handler = new SignalHandler();
        $this->assertFalse($handler->hasHandler(SIGINT));
        $this->assertFalse($handler->hasHandler(SIGTERM));
        $this->assertFalse($handler->hasHandler(999));
    }

    /**
     * @test
     */
    public function testDisableRestoresDefaults() {
        $handler = new SignalHandler();
        $called = false;
        $callback = function (int $signal) use (&$called) {
            $called = true;
        };

        $handler->register(SIGUSR1, $callback);
        $handler->enable();
        $handler->disable();

        // Handlers array should still contain the handler
        $this->assertTrue($handler->hasHandler(SIGUSR1));
        // But it's no longer enabled
        $this->assertFalse($handler->isEnabled());
    }

    /**
     * @test
     */
    public function testEnableDisableMultipleTimes() {
        $handler = new SignalHandler();
        $callback = function (int $signal) {};

        $handler->register(SIGUSR1, $callback);

        $handler->enable();
        $this->assertTrue($handler->isEnabled());

        $handler->disable();
        $this->assertFalse($handler->isEnabled());

        $handler->enable();
        $this->assertTrue($handler->isEnabled());

        $handler->disable();
        $this->assertFalse($handler->isEnabled());
    }

    /**
     * @test
     */
    public function testChaining() {
        $handler = new SignalHandler();
        $cb = function (int $signal) {};

        $result = $handler->register(SIGINT, $cb)
                          ->register(SIGTERM, $cb)
                          ->enable();

        $this->assertSame($handler, $result);
        $this->assertTrue($handler->isEnabled());
        $this->assertCount(2, $handler->getHandlers());

        $handler->disable();
    }
}
