<?php
declare(strict_types=1);
namespace WebFiori\Cli\Discovery;

/**
 * Handles caching of discovered commands.
 *
 * @author Ibrahim
 */
class CommandCache {
    private string $cacheFile;
    private bool $enabled;

    /**
     * Creates new cache instance.
     * 
     * @param string $cacheFile Path to cache file
     * @param bool $enabled Whether caching is enabled
     */
    public function __construct(string $cacheFile = 'cache/commands.json', bool $enabled = true) {
        $this->cacheFile = $cacheFile;
        $this->enabled = $enabled;
    }

    /**
     * Clear the cache.
     */
    public function clear(): void {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    /**
     * Get cached commands if valid.
     * 
     * @return array|null Array of command metadata or null if cache invalid
     */
    public function get(): ?array {
        if (!$this->enabled || !file_exists($this->cacheFile)) {
            return null;
        }

        $content = file_get_contents($this->cacheFile);

        if ($content === false) {
            return null;
        }

        $cache = json_decode($content, true);

        if (!$cache || !isset($cache['commands'], $cache['files'], $cache['timestamp'])) {
            return null;
        }

        // Check if cache is still valid
        if (!$this->isCacheValid($cache)) {
            return null;
        }

        return $cache['commands'];
    }

    /**
     * Get cache file path.
     * 
     * @return string
     */
    public function getCacheFile(): string {
        return $this->cacheFile;
    }

    /**
     * Check if caching is enabled.
     * 
     * @return bool
     */
    public function isEnabled(): bool {
        return $this->enabled;
    }

    /**
     * Set cache file path.
     * 
     * @param string $cacheFile
     */
    public function setCacheFile(string $cacheFile): void {
        $this->cacheFile = $cacheFile;
    }

    /**
     * Enable or disable caching.
     * 
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void {
        $this->enabled = $enabled;
    }

    /**
     * Store commands in cache.
     * 
     * @param array $commands Array of command metadata
     * @param array $files Array of file paths that were scanned
     */
    public function store(array $commands, array $files): void {
        if (!$this->enabled) {
            return;
        }

        $this->ensureCacheDirectory();

        $fileInfo = [];

        foreach ($files as $file) {
            if (file_exists($file)) {
                $fileInfo[$file] = filemtime($file);
            }
        }

        $cache = [
            'timestamp' => time(),
            'commands' => $commands,
            'files' => $fileInfo
        ];

        file_put_contents($this->cacheFile, json_encode($cache, JSON_PRETTY_PRINT));
    }

    /**
     * Ensure cache directory exists.
     */
    private function ensureCacheDirectory(): void {
        $dir = dirname($this->cacheFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Check if cache is valid by comparing file modification times.
     * 
     * @param array $cache
     * @return bool
     */
    private function isCacheValid(array $cache): bool {
        foreach ($cache['files'] as $file => $cachedMtime) {
            if (!file_exists($file)) {
                return false;
            }

            $currentMtime = filemtime($file);

            if ($currentMtime > $cachedMtime) {
                return false;
            }
        }

        return true;
    }
}
