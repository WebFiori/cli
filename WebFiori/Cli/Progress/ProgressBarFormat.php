<?php
declare(strict_types=1);
namespace WebFiori\Cli\Progress;

/**
 * Handles format string parsing and rendering for progress bars.
 *
 * @author Ibrahim
 */
class ProgressBarFormat {
    /**
     * Default format string
     */
    public const DEFAULT_FORMAT = '[{bar}] {percent}% ({current}/{total})';

    /**
     * Format with ETA
     */
    public const ETA_FORMAT = '[{bar}] {percent}% ({current}/{total}) ETA: {eta}';

    /**
     * Format with rate
     */
    public const RATE_FORMAT = '[{bar}] {percent}% ({current}/{total}) {rate}/s';

    /**
     * Verbose format with all information
     */
    public const VERBOSE_FORMAT = '[{bar}] {percent}% ({current}/{total}) {elapsed} ETA: {eta} {rate}/s {memory}';

    private string $format;

    /**
     * Creates a new format instance.
     * 
     * @param string $format Format string with placeholders
     */
    public function __construct(string $format = self::DEFAULT_FORMAT) {
        $this->format = $format;
    }

    /**
     * Formats time duration in human-readable format.
     * 
     * @param float $seconds Duration in seconds
     * @return string Formatted duration
     */
    public static function formatDuration(float $seconds): string {
        if ($seconds < 0) {
            return '--:--';
        }

        $totalSeconds = (int) $seconds;
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $secs = $totalSeconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%02d:%02d', $minutes, $secs);
    }

    /**
     * Formats memory usage in human-readable format.
     * 
     * @param int $bytes Memory usage in bytes
     * @return string Formatted memory usage
     */
    public static function formatMemory(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return sprintf('%.1f%s', $bytes, $units[$unitIndex]);
    }

    /**
     * Formats rate in human-readable format.
     * 
     * @param float $rate Rate per second
     * @return string Formatted rate
     */
    public static function formatRate(float $rate): string {
        if ($rate < 1) {
            return sprintf('%.2f', $rate);
        } elseif ($rate < 10) {
            return sprintf('%.1f', $rate);
        } else {
            return sprintf('%.0f', $rate);
        }
    }

    /**
     * Gets the format string.
     * 
     * @return string
     */
    public function getFormat(): string {
        return $this->format;
    }

    /**
     * Gets all placeholders used in the format string.
     * 
     * @return array Array of placeholder names
     */
    public function getPlaceholders(): array {
        preg_match_all('/\{([^}]+)\}/', $this->format, $matches);

        return $matches[1] ?? [];
    }

    /**
     * Checks if the format contains a specific placeholder.
     * 
     * @param string $placeholder Placeholder name without braces
     * @return bool
     */
    public function hasPlaceholder(string $placeholder): bool {
        return strpos($this->format, '{'.$placeholder.'}') !== false;
    }

    /**
     * Renders the format string with provided values.
     * 
     * @param array $values Associative array of placeholder values
     * @return string Rendered format string
     */
    public function render(array $values): string {
        $output = $this->format;

        foreach ($values as $placeholder => $value) {
            $output = str_replace('{'.$placeholder.'}', (string)$value, $output);
        }

        return $output;
    }

    /**
     * Sets the format string.
     * 
     * @param string $format
     * @return ProgressBarFormat
     */
    public function setFormat(string $format): ProgressBarFormat {
        $this->format = $format;

        return $this;
    }
}
