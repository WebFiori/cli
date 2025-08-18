<?php
namespace WebFiori\Cli\Discovery;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use WebFiori\Cli\Command;
use WebFiori\Cli\Exceptions\CommandDiscoveryException;

/**
 * Discovers CLI commands automatically from specified directories.
 *
 * @author Ibrahim
 */
class CommandDiscovery {
    private CommandCache $cache;
    private array $errors = [];
    private array $excludePatterns = [];
    private array $searchPaths = [];
    private bool $strictMode = false;

    /**
     * Creates new command discovery instance.
     * 
     * @param CommandCache|null $cache Cache instance, creates default if null
     */
    public function __construct(?CommandCache $cache = null) {
        $this->cache = $cache ?? new CommandCache();
    }

    /**
     * Add a directory path to search for commands.
     * 
     * @param string $path Directory path to search
     * @return self
     */
    public function addSearchPath(string $path): self {
        $realPath = realpath($path);

        if ($realPath === false) {
            throw new CommandDiscoveryException("Search path does not exist: {$path}");
        }

        if (!in_array($realPath, $this->searchPaths)) {
            $this->searchPaths[] = $realPath;
        }

        return $this;
    }

    /**
     * Add multiple search paths.
     * 
     * @param array $paths Array of directory paths
     * @return self
     */
    public function addSearchPaths(array $paths): self {
        foreach ($paths as $path) {
            $this->addSearchPath($path);
        }

        return $this;
    }

    /**
     * Discover commands from configured search paths.
     * 
     * @return array Array of Command instances
     * @throws CommandDiscoveryException If strict mode is enabled and errors occur
     */
    public function discover(): array {
        $this->errors = [];

        // Try to get from cache first
        $cachedCommands = $this->cache->get();

        if ($cachedCommands !== null) {
            return $this->instantiateCommands($cachedCommands);
        }

        // Discover commands
        $commandMetadata = [];
        $scannedFiles = [];

        foreach ($this->searchPaths as $path) {
            $files = $this->scanDirectory($path);
            $scannedFiles = array_merge($scannedFiles, $files);

            foreach ($files as $file) {
                try {
                    $className = $this->extractClassName($file);

                    if ($className && $this->isValidCommand($className)) {
                        $metadata = CommandMetadata::extract($className);
                        $commandMetadata[] = $metadata;
                    }
                } catch (\Exception $e) {
                    $this->errors[] = "Failed to process {$file}: ".$e->getMessage();
                }
            }
        }

        // Handle errors
        if (!empty($this->errors) && $this->strictMode) {
            throw CommandDiscoveryException::fromErrors($this->errors);
        }

        // Cache the results
        $this->cache->store($commandMetadata, $scannedFiles);

        return $this->instantiateCommands($commandMetadata);
    }

    /**
     * Add a pattern to exclude files/directories.
     * 
     * @param string $pattern Glob pattern to exclude
     * @return self
     */
    public function excludePattern(string $pattern): self {
        if (!in_array($pattern, $this->excludePatterns)) {
            $this->excludePatterns[] = $pattern;
        }

        return $this;
    }

    /**
     * Add multiple exclude patterns.
     * 
     * @param array $patterns Array of glob patterns
     * @return self
     */
    public function excludePatterns(array $patterns): self {
        foreach ($patterns as $pattern) {
            $this->excludePattern($pattern);
        }

        return $this;
    }

    /**
     * Get the cache instance.
     * 
     * @return CommandCache
     */
    public function getCache(): CommandCache {
        return $this->cache;
    }

    /**
     * Get discovery errors from last discovery attempt.
     * 
     * @return array
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * Enable or disable strict mode.
     * In strict mode, any discovery error will throw an exception.
     * 
     * @param bool $strict
     * @return self
     */
    public function setStrictMode(bool $strict): self {
        $this->strictMode = $strict;

        return $this;
    }

    /**
     * Extract class name from PHP file.
     * 
     * @param string $filePath
     * @return string|null
     */
    private function extractClassName(string $filePath): ?string {
        $content = file_get_contents($filePath);

        if ($content === false) {
            return null;
        }

        // Extract namespace
        $namespace = null;

        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = trim($matches[1]);
        }

        // Extract class name
        $className = null;

        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            $className = $matches[1];
        }

        if (!$className) {
            return null;
        }

        return $namespace ? $namespace.'\\'.$className : $className;
    }

    /**
     * Instantiate commands from metadata.
     * 
     * @param array $commandMetadata
     * @return array Array of Command instances
     */
    private function instantiateCommands(array $commandMetadata): array {
        $commands = [];

        foreach ($commandMetadata as $metadata) {
            try {
                $className = $metadata['className'];

                if (class_exists($className)) {
                    // Check if class implements AutoDiscoverable before instantiating
                    if (is_subclass_of($className, AutoDiscoverable::class)) {
                        if (!$className::shouldAutoRegister()) {
                            continue; // Skip this command
                        }
                    }

                    $commands[] = new $className();
                }
            } catch (\Exception $e) {
                $this->errors[] = "Failed to instantiate {$metadata['className']}: ".$e->getMessage();

                if ($this->strictMode) {
                    throw new CommandDiscoveryException("Failed to instantiate {$metadata['className']}: ".$e->getMessage());
                }
            }
        }

        return $commands;
    }

    /**
     * Check if class is a valid command.
     * 
     * @param string $className
     * @return bool
     */
    private function isValidCommand(string $className): bool {
        try {
            if (!class_exists($className)) {
                return false;
            }

            $reflection = new ReflectionClass($className);

            return $reflection->isSubclassOf(Command::class) 
                && !$reflection->isAbstract()
                && !$reflection->isInterface()
                && !$reflection->isTrait();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Scan directory for PHP files.
     * 
     * @param string $directory
     * @return array Array of file paths
     */
    private function scanDirectory(string $directory): array {
        $files = [];

        if (!is_dir($directory)) {
            return $files;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $filePath = $file->getRealPath();

            if ($this->shouldExcludeFile($filePath)) {
                continue;
            }

            $files[] = $filePath;
        }

        return $files;
    }

    /**
     * Check if file should be excluded based on patterns.
     * 
     * @param string $filePath
     * @return bool
     */
    private function shouldExcludeFile(string $filePath): bool {
        foreach ($this->excludePatterns as $pattern) {
            if (fnmatch($pattern, $filePath) || fnmatch($pattern, basename($filePath))) {
                return true;
            }
        }

        return false;
    }
}
