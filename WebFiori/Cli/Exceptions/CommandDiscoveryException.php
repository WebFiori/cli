<?php
namespace WebFiori\Cli\Exceptions;

use Exception;

/**
 * Exception thrown when command discovery fails.
 *
 * @author Ibrahim
 */
class CommandDiscoveryException extends Exception {
    /**
     * Creates new instance with multiple error messages.
     * 
     * @param array $errors Array of error messages
     * @param int $code Error code
     */
    public static function fromErrors(array $errors, int $code = 0): self {
        $message = "Command discovery failed with the following errors:\n" . implode("\n", $errors);
        return new self($message, $code);
    }
}
