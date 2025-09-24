<?php
namespace WebFiori\CLI\Discovery;

use ReflectionClass;
use WebFiori\CLI\Command;
use WebFiori\CLI\Exceptions\CommandDiscoveryException;

/**
 * Utility class for extracting command metadata.
 *
 * @author Ibrahim
 */
class CommandMetadata {
    /**
     * Extract metadata from a command class.
     * 
     * @param string $className The fully qualified class name
     * @return array Associative array with command metadata
     */
    public static function extract(string $className): array {
        if (!class_exists($className)) {
            throw new CommandDiscoveryException("Class {$className} does not exist");
        }

        $reflection = new ReflectionClass($className);

        if (!$reflection->isSubclassOf(Command::class)) {
            throw new CommandDiscoveryException("Class {$className} is not a Command");
        }

        if ($reflection->isAbstract()) {
            throw new CommandDiscoveryException("Class {$className} is abstract");
        }

        return [
            'className' => $className,
            'name' => self::extractCommandName($reflection),
            'description' => self::extractDescription($reflection),
            'group' => self::extractGroup($reflection),
            'aliases' => self::extractAliases($reflection),
            'hidden' => self::isHidden($reflection),
            'file' => $reflection->getFileName()
        ];
    }

    /**
     * Extract aliases from class.
     * 
     * @param ReflectionClass $class
     * @return array
     */
    private static function extractAliases(ReflectionClass $class): array {
        $docComment = $class->getDocComment();

        if (!$docComment) {
            return [];
        }

        if (preg_match('/@Command\s*\([^)]*aliases\s*=\s*\[([^\]]+)\]/', $docComment, $matches)) {
            $aliasesStr = $matches[1];
            $aliases = [];

            if (preg_match_all('/["\']([^"\']+)["\']/', $aliasesStr, $aliasMatches)) {
                $aliases = $aliasMatches[1];
            }

            return $aliases;
        }

        return [];
    }

    /**
     * Extract command name from class.
     * 
     * @param ReflectionClass $class
     * @return string
     */
    private static function extractCommandName(ReflectionClass $class): string {
        // Try to get name from @Command annotation
        $docComment = $class->getDocComment();

        if ($docComment && preg_match('/@Command\s*\(\s*name\s*=\s*["\']([^"\']+)["\']/', $docComment, $matches)) {
            return $matches[1];
        }

        // Fall back to class name convention
        $className = $class->getShortName();
        $name = preg_replace('/Command$/', '', $className);

        // Convert CamelCase to kebab-case
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $name));
    }

    /**
     * Extract description from class docblock.
     * 
     * @param ReflectionClass $class
     * @return string
     */
    private static function extractDescription(ReflectionClass $class): string {
        $docComment = $class->getDocComment();

        if (!$docComment) {
            return '<NO DESCRIPTION>';
        }

        // Try @Command annotation first
        if (preg_match('/@Command\s*\([^)]*description\s*=\s*["\']([^"\']+)["\']/', $docComment, $matches)) {
            return $matches[1];
        }

        // Fall back to first line of docblock
        $lines = explode("\n", $docComment);

        foreach ($lines as $line) {
            $line = trim($line, " \t\n\r\0\x0B/*");

            if (!empty($line) && !str_starts_with($line, '@')) {
                return $line;
            }
        }

        return '<NO DESCRIPTION>';
    }

    /**
     * Extract group/category from class.
     * 
     * @param ReflectionClass $class
     * @return string|null
     */
    private static function extractGroup(ReflectionClass $class): ?string {
        $docComment = $class->getDocComment();

        if ($docComment && preg_match('/@Command\s*\([^)]*group\s*=\s*["\']([^"\']+)["\']/', $docComment, $matches)) {
            return $matches[1];
        }

        // Try to infer from namespace
        $namespace = $class->getNamespaceName();
        $parts = explode('\\', $namespace);

        // Look for Commands subdirectory
        $commandsIndex = array_search('Commands', $parts);

        if ($commandsIndex !== false && isset($parts[$commandsIndex + 1])) {
            return strtolower($parts[$commandsIndex + 1]);
        }

        return null;
    }

    /**
     * Check if command should be hidden.
     * 
     * @param ReflectionClass $class
     * @return bool
     */
    private static function isHidden(ReflectionClass $class): bool {
        $docComment = $class->getDocComment();

        if (!$docComment) {
            return false;
        }

        // Check for @Hidden annotation
        if (strpos($docComment, '@Hidden') !== false) {
            return true;
        }

        // Check for @Command(hidden=true)
        if (preg_match('/@Command\s*\([^)]*hidden\s*=\s*true/', $docComment)) {
            return true;
        }

        return false;
    }
}
