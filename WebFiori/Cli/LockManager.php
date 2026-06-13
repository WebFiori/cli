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
 * Manages file-based locks for single-instance command execution.
 *
 * Uses flock() for non-blocking exclusive locks. The lock is automatically
 * released when the file handle is closed (including on process crash).
 *
 * @author Ibrahim
 */
class LockManager {
    /**
     * @var resource|null
     */
    private $handle;

    /**
     * @var string|null
     */
    private $lockPath;

    public function __construct() {
        $this->handle = null;
        $this->lockPath = null;
    }

    /**
     * Attempts to acquire an exclusive non-blocking lock.
     *
     * @param string $commandName The command name used to generate the lock file path.
     *
     * @param string|null $customPath Optional custom lock file path.
     *
     * @return bool True if the lock was acquired, false otherwise.
     */
    public function acquire(string $commandName, ?string $customPath = null): bool {
        $this->lockPath = $customPath ?? sys_get_temp_dir().'/wfcli-'.$commandName.'.lock';

        $handle = @fopen($this->lockPath, 'w');

        if ($handle === false) {
            return false;
        }

        if (!flock($handle, LOCK_EX | LOCK_NB)) {
            fclose($handle);

            return false;
        }

        $this->handle = $handle;
        fwrite($this->handle, (string) getmypid());
        fflush($this->handle);

        return true;
    }

    /**
     * Returns the lock file path.
     *
     * @return string|null The path, or null if no lock has been attempted.
     */
    public function getLockPath(): ?string {
        return $this->lockPath;
    }

    /**
     * Checks if the lock is currently held.
     *
     * @return bool True if a lock is held.
     */
    public function isLocked(): bool {
        return $this->handle !== null;
    }

    /**
     * Releases the lock and closes the file handle.
     */
    public function release(): void {
        if ($this->handle !== null) {
            flock($this->handle, LOCK_UN);
            fclose($this->handle);
            $this->handle = null;
        }

        if ($this->lockPath !== null && file_exists($this->lockPath)) {
            @unlink($this->lockPath);
            $this->lockPath = null;
        }
    }
}
