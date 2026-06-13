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
namespace WebFiori\Cli;

/**
 * A class that provides signal handling capabilities for CLI applications.
 *
 * This class wraps PHP's pcntl signal functions and provides a clean API
 * for registering and managing signal handlers. On systems where pcntl
 * is not available (e.g., Windows), the class degrades gracefully as a no-op.
 *
 * @author Ibrahim
 */
class SignalHandler {
    /**
     * @var bool
     */
    private $enabled;
    /**
     * @var array<int, callable>
     */
    private $handlers;

    /**
     * Creates new instance of the class.
     */
    public function __construct() {
        $this->handlers = [];
        $this->enabled = false;
    }

    /**
     * Disables signal handling by restoring default signal behavior for
     * all registered signals.
     *
     * @return SignalHandler The method returns same instance for chaining.
     */
    public function disable(): self {
        $this->enabled = false;

        if (self::isSupported()) {
            foreach ($this->handlers as $signal => $handler) {
                pcntl_signal($signal, SIG_DFL);
            }
        }

        return $this;
    }

    /**
     * Enables signal handling by activating async signals and installing
     * all registered handlers.
     *
     * If signal handling is not supported, this method does nothing but
     * still marks the handler as enabled.
     *
     * @return SignalHandler The method returns same instance for chaining.
     */
    public function enable(): self {
        $this->enabled = true;

        if (self::isSupported()) {
            pcntl_async_signals(true);

            foreach ($this->handlers as $signal => $handler) {
                pcntl_signal($signal, $handler);
            }
        }

        return $this;
    }

    /**
     * Returns all registered signal handlers.
     *
     * @return array<int, callable> An associative array mapping signal numbers
     * to their handler callables.
     */
    public function getHandlers(): array {
        return $this->handlers;
    }

    /**
     * Checks if a handler is registered for a specific signal.
     *
     * @param int $signal The signal number to check.
     *
     * @return bool True if a handler is registered for the signal.
     */
    public function hasHandler(int $signal): bool {
        return isset($this->handlers[$signal]);
    }

    /**
     * Checks if signal handling is currently enabled.
     *
     * @return bool True if enabled, false otherwise.
     */
    public function isEnabled(): bool {
        return $this->enabled;
    }

    /**
     * Checks if signal handling is supported on the current platform.
     *
     * Signal handling requires the pcntl extension which is available
     * on Unix-like systems (Linux, macOS) but not on Windows.
     *
     * @return bool True if signal handling is supported, false otherwise.
     */
    public static function isSupported(): bool {
        return function_exists('pcntl_async_signals');
    }

    /**
     * Registers a handler for a specific signal.
     *
     * If signal handling is not supported, this method does nothing.
     *
     * @param int $signal The signal number (e.g., SIGINT, SIGTERM).
     *
     * @param callable $handler The callback to invoke when the signal is received.
     * The callback receives the signal number as its argument.
     *
     * @return SignalHandler The method returns same instance for chaining.
     */
    public function register(int $signal, callable $handler): self {
        $this->handlers[$signal] = $handler;

        if ($this->enabled && self::isSupported()) {
            pcntl_signal($signal, $handler);
        }

        return $this;
    }

    /**
     * Removes the handler for a specific signal, restoring default behavior.
     *
     * @param int $signal The signal number to remove the handler for.
     *
     * @return SignalHandler The method returns same instance for chaining.
     */
    public function remove(int $signal): self {
        unset($this->handlers[$signal]);

        if ($this->enabled && self::isSupported()) {
            pcntl_signal($signal, SIG_DFL);
        }

        return $this;
    }
}
