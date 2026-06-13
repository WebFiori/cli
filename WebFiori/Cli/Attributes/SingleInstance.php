<?php

declare(strict_types=1);
namespace WebFiori\Cli\Attributes;

use Attribute;

/**
 * Attribute to prevent concurrent execution of a command.
 *
 * When applied to a command class, only one instance of the command
 * can run at a time. Subsequent attempts will fail with a warning.
 *
 * @author Ibrahim
 */
#[Attribute(Attribute::TARGET_CLASS)]
class SingleInstance {
    public readonly int $exitCode;
    public readonly ?string $lockPath;

    public function __construct(?string $lockPath = null, int $exitCode = 1) {
        $this->lockPath = $lockPath;
        $this->exitCode = $exitCode;
    }
}
