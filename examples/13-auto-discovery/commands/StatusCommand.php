<?php

use WebFiori\Cli\Command;

/**
 * A command that requires a version string injected via constructor.
 */
class StatusCommand extends Command {
    private string $version;

    public function __construct(string $version) {
        parent::__construct('status', [], 'Show application status');
        $this->version = $version;
    }

    public function exec(): int {
        $this->println('Application is running.');
        $this->println('Version: ' . $this->version);

        return 0;
    }
}
