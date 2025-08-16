<?php
namespace WebFiori\Tests\Cli\Discovery\TestCommands;

use WebFiori\Cli\CLICommand;
use WebFiori\Cli\Discovery\AutoDiscoverable;

/**
 * A test command that implements AutoDiscoverable.
 */
class AutoDiscoverableCommand extends CLICommand implements AutoDiscoverable {
    private static bool $shouldRegister = true;
    
    public function __construct() {
        parent::__construct('auto-discoverable', [], 'Auto-discoverable test command');
    }
    
    public function exec(): int {
        $this->println('Auto-discoverable command executed');
        return 0;
    }
    
    public static function shouldAutoRegister(): bool {
        return self::$shouldRegister;
    }
    
    public static function setShouldRegister(bool $should): void {
        self::$shouldRegister = $should;
    }
}
