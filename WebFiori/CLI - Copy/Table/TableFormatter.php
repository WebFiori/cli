<?php
namespace WebFiori\CLI\Table;

/**
 * TableFormatter - Content-specific formatting logic for table cells.
 * 
 * This class handles formatting of different data types, number formatting,
 * date formatting, and other content-specific transformations.
 * 
 * @author Ibrahim
 */
class TableFormatter {
    private array $formatters = [];
    private array $globalFormatters = [];

    public function __construct() {
        $this->initializeDefaultFormatters();
    }

    /**
     * Clear all custom formatters.
     */
    public function clearFormatters(): self {
        $this->formatters = [];
        $this->globalFormatters = [];
        $this->initializeDefaultFormatters();

        return $this;
    }

    /**
     * Create a column-specific formatter.
     */
    public static function createColumnFormatter(string $type, array $options = []): callable {
        return function ($value) use ($type, $options) {
            $formatter = new self();

            return match ($type) {
                'currency' => $formatter->formatCurrency(
                    $value,
                    $options['symbol'] ?? '$',
                    $options['decimals'] ?? 2,
                    $options['symbol_first'] ?? true
                ),
                'percentage' => $formatter->formatPercentage(
                    $value,
                    $options['decimals'] ?? 1
                ),
                'date' => $formatter->formatDate(
                    $value,
                    $options['format'] ?? 'Y-m-d'
                ),
                'filesize' => $formatter->formatFileSize(
                    $value,
                    $options['precision'] ?? 2
                ),
                'duration' => $formatter->formatDuration($value),
                'boolean' => $formatter->formatBoolean(
                    $value,
                    $options['true_text'] ?? 'Yes',
                    $options['false_text'] ?? 'No'
                ),
                'number' => $formatter->formatNumber(
                    $value,
                    $options['decimals'] ?? 2,
                    $options['decimal_separator'] ?? '.',
                    $options['thousands_separator'] ?? ','
                ),
                default => (string)$value
            };
        };
    }

    /**
     * Format a boolean value.
     */
    public function formatBoolean(mixed $value, string $trueText = 'Yes', string $falseText = 'No'): string {
        if (is_bool($value)) {
            return $value ? $trueText : $falseText;
        }

        $stringValue = strtolower(trim((string)$value));

        return match ($stringValue) {
            'true', '1', 'yes', 'on', 'enabled' => $trueText,
            'false', '0', 'no', 'off', 'disabled' => $falseText,
            default => (string)$value
        };
    }

    /**
     * Format a cell value based on its type and column configuration.
     */
    public function formatCell(mixed $value, Column $column, string $type = 'string'): string {
        // Handle null/empty values
        if ($value === null || $value === '') {
            return (string)$column->getDefaultValue();
        }

        // Apply column-specific formatter first
        $formatter = $column->getFormatter();

        if ($formatter !== null && is_callable($formatter)) {
            $value = call_user_func($formatter, $value);
        }

        // Apply type-specific formatting
        $formatted = $this->applyTypeFormatting($value, $type);

        // Apply global formatters
        $formatted = $this->applyGlobalFormatters($formatted, $type);

        return (string)$formatted;
    }

    /**
     * Format a currency value.
     */
    public function formatCurrency(
        float|int $amount,
        string $currency = '$',
        int $decimals = 2,
        bool $currencyFirst = true
    ): string {
        $formatted = $this->formatNumber($amount, $decimals);

        return $currencyFirst ? $currency.$formatted : $formatted.' '.$currency;
    }

    /**
     * Format a date value.
     */
    public function formatDate(mixed $date, string $format = 'Y-m-d'): string {
        if (empty($date)) {
            return '';
        }

        try {
            $dateObj = null;

            if (is_string($date)) {
                $dateObj = new \DateTime($date);
            } elseif ($date instanceof \DateTime) {
                $dateObj = $date;
            } elseif (is_int($date)) {
                $dateObj = new \DateTime('@'.$date);
            }

            if ($dateObj !== null) {
                return $dateObj->format($format);
            }
        } catch (\Exception $e) {
            // Fall through to default return
        }

        return (string)$date;
    }

    /**
     * Format duration in human-readable format.
     */
    public function formatDuration(int $seconds): string {
        if ($seconds < 60) {
            return $seconds.'s';
        }

        if ($seconds < 3600) {
            $minutes = intval($seconds / 60);
            $remainingSeconds = $seconds % 60;

            return $minutes.'m'.($remainingSeconds > 0 ? ' '.$remainingSeconds.'s' : '');
        }

        if ($seconds < 86400) {
            $hours = intval($seconds / 3600);
            $remainingMinutes = intval(($seconds % 3600) / 60);

            return $hours.'h'.($remainingMinutes > 0 ? ' '.$remainingMinutes.'m' : '');
        }

        $days = intval($seconds / 86400);
        $remainingHours = intval(($seconds % 86400) / 3600);

        return $days.'d'.($remainingHours > 0 ? ' '.$remainingHours.'h' : '');
    }

    /**
     * Format file size in human-readable format.
     */
    public function formatFileSize(int $bytes, int $precision = 2): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        // For bytes (B), don't show decimal places
        if ($i === 0) {
            return round($bytes).' '.$units[$i];
        }

        return number_format($bytes, $precision).' '.$units[$i];
    }

    /**
     * Format a header value.
     */
    public function formatHeader(string $header): string {
        // Apply any header-specific formatting (but not cell formatters)
        return $this->applyHeaderFormatting($header);
    }

    /**
     * Format a number with specified precision and thousands separator.
     */
    public function formatNumber(
        float|int $number,
        int $decimals = 2,
        string $decimalSeparator = '.',
        string $thousandsSeparator = ','
    ): string {
        return number_format((float)$number, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    /**
     * Format a percentage value.
     */
    public function formatPercentage(float|int $value, int $decimals = 1): string {
        return $this->formatNumber($value, $decimals).'%';
    }

    /**
     * Get available formatter types.
     */
    public function getAvailableTypes(): array {
        return array_merge(
            ['string', 'integer', 'float', 'date', 'boolean'],
            array_keys($this->formatters)
        );
    }

    /**
     * Register a custom formatter for a specific type.
     */
    public function registerFormatter(string $type, callable $formatter): self {
        $this->formatters[$type] = $formatter;

        return $this;
    }

    /**
     * Register a global formatter that applies to all values.
     */
    public function registerGlobalFormatter(callable $formatter): self {
        $this->globalFormatters[] = $formatter;

        return $this;
    }

    /**
     * Truncate text with smart word boundary detection.
     */
    public function smartTruncate(string $text, int $maxLength, string $ellipsis = '...'): string {
        if (strlen($text) <= $maxLength) {
            return $text;
        }

        $ellipsisLength = strlen($ellipsis);
        $maxContentLength = $maxLength - $ellipsisLength;

        if ($maxContentLength <= 0) {
            return str_repeat('.', min($maxLength, 3));
        }

        // Try to break at word boundary
        $truncated = substr($text, 0, $maxContentLength);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false && $lastSpace > $maxContentLength * 0.7) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated.$ellipsis;
    }

    /**
     * Apply global formatters to a value.
     */
    private function applyGlobalFormatters(mixed $value, string $type): mixed {
        foreach ($this->globalFormatters as $formatter) {
            $value = call_user_func($formatter, $value, $type);
        }

        return $value;
    }

    /**
     * Apply header-specific formatting.
     */
    private function applyHeaderFormatting(string $header): string {
        // Convert to title case and clean up
        $formatted = ucwords(str_replace(['_', '-'], ' ', $header));

        // Apply any registered header formatters
        if (isset($this->formatters['header'])) {
            $formatted = call_user_func($this->formatters['header'], $formatted);
        }

        return $formatted;
    }

    /**
     * Apply type-specific formatting.
     */
    private function applyTypeFormatting(mixed $value, string $type): mixed {
        // Check for registered custom formatter
        if (isset($this->formatters[$type])) {
            return call_user_func($this->formatters[$type], $value);
        }

        // Apply built-in type formatting
        return match ($type) {
            'integer' => $this->formatInteger($value),
            'float' => $this->formatFloat($value),
            'date' => $this->formatDate($value),
            'boolean' => $this->formatBoolean($value),
            'currency' => $this->formatCurrency($value),
            'percentage' => $this->formatPercentage($value),
            'filesize' => $this->formatFileSize($value),
            'duration' => $this->formatDuration($value),
            default => $value
        };
    }

    /**
     * Format float values.
     */
    private function formatFloat(mixed $value): string {
        if (!is_numeric($value)) {
            return (string)$value;
        }

        // Auto-detect decimal places needed
        $floatValue = (float)$value;
        $decimals = 2;

        // If it's a whole number, show no decimals
        if ($floatValue == intval($floatValue)) {
            $decimals = 0;
        }

        return number_format($floatValue, $decimals, '.', ',');
    }

    /**
     * Format integer values.
     */
    private function formatInteger(mixed $value): string {
        if (!is_numeric($value)) {
            return (string)$value;
        }

        return number_format((int)$value, 0, '.', ',');
    }

    /**
     * Initialize default formatters.
     */
    private function initializeDefaultFormatters(): void {
        // Email formatter
        $this->registerFormatter('email', function ($value) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return $value;
            }

            return (string)$value;
        });

        // URL formatter
        $this->registerFormatter('url', function ($value) {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }

            return (string)$value;
        });

        // Phone number formatter (basic)
        $this->registerFormatter('phone', function ($value) {
            $cleaned = preg_replace('/[^0-9]/', '', (string)$value);

            if (strlen($cleaned) === 10) {
                return sprintf('(%s) %s-%s', 
                    substr($cleaned, 0, 3),
                    substr($cleaned, 3, 3),
                    substr($cleaned, 6)
                );
            }

            return (string)$value;
        });

        // Status formatter with color hints
        $this->registerFormatter('status', function ($value) {
            $status = strtolower(trim((string)$value));

            return match ($status) {
                'active', 'enabled', 'online', 'success', 'completed' => '✅ '.ucfirst($status),
                'inactive', 'disabled', 'offline', 'failed', 'error' => '❌ '.ucfirst($status),
                'pending', 'processing', 'warning' => '⚠️ '.ucfirst($status),
                'unknown', 'n/a', '' => '❓ Unknown',
                default => ucfirst($status)
            };
        });
    }
}
