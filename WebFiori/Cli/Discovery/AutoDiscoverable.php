<?php
namespace WebFiori\Cli\Discovery;

/**
 * Interface for commands that can control their auto-discovery behavior.
 *
 * @author Ibrahim
 */
interface AutoDiscoverable {
    /**
     * Determine if this command should be automatically registered.
     * 
     * This method allows commands to control whether they should be
     * included in auto-discovery based on runtime conditions like
     * environment, configuration, or other factors.
     * 
     * @return bool True if the command should be auto-registered
     */
    public static function shouldAutoRegister(): bool;
}
