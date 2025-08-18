<?php
namespace WebFiori\Cli\Table;

/**
 * Column - Represents individual column configuration.
 * 
 * This class handles column-specific settings like width, alignment,
 * formatting, and content processing rules.
 * 
 * @author Ibrahim
 * @version 1.0.0
 */
class Column {
    public const ALIGN_AUTO = 'auto';
    public const ALIGN_CENTER = 'center';

    public const ALIGN_LEFT = 'left';
    public const ALIGN_RIGHT = 'right';
    private string $alignment = self::ALIGN_AUTO;
    private $colorizer = null;
    private mixed $defaultValue = '';
    private string $ellipsis = '...';
    private $formatter = null;
    private ?int $maxWidth = null;
    private array $metadata = [];
    private ?int $minWidth = null;

    private string $name;
    private bool $truncate = true;
    private bool $visible = true;
    private ?int $width = null;
    private bool $wordWrap = false;

    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * Align text within specified width.
     */
    public function alignText(string $text, int $width): string {
        $displayLength = $this->getDisplayLength($text);

        if ($displayLength >= $width) {
            return $text;
        }

        $padding = $width - $displayLength;
        $alignment = $this->resolveAlignment($text);

        return match ($alignment) {
            self::ALIGN_RIGHT => str_repeat(' ', $padding).$text,
            self::ALIGN_CENTER => str_repeat(' ', intval($padding / 2)).$text.str_repeat(' ', $padding - intval($padding / 2)),
            default => $text.str_repeat(' ', $padding) // LEFT
        };
    }

    /**
     * Calculate ideal width based on content.
     */
    public function calculateIdealWidth(array $values): int {
        $maxLength = strlen($this->name); // Start with header length

        foreach ($values as $value) {
            $formatted = $this->formatValue($value);
            $length = $this->getDisplayLength($formatted);
            $maxLength = max($maxLength, $length);
        }

        // Apply constraints
        if ($this->minWidth !== null) {
            $maxLength = max($maxLength, $this->minWidth);
        }

        if ($this->maxWidth !== null) {
            $maxLength = min($maxLength, $this->maxWidth);
        }

        return $maxLength;
    }

    /**
     * Create a center-aligned column.
     */
    public static function center(string $name, ?int $width = null): self {
        return (new self($name))->setAlignment(self::ALIGN_CENTER)->setWidth($width);
    }

    /**
     * Apply color to a value using the column's colorizer.
     */
    public function colorizeValue(string $value): string {
        if ($this->colorizer === null) {
            return $value;
        }

        $colorConfig = call_user_func($this->colorizer, $value);

        if (!is_array($colorConfig) || empty($colorConfig)) {
            return $value;
        }

        return $this->applyAnsiColors($value, $colorConfig);
    }

    /**
     * Configure column with array of options.
     */
    public function configure(array $config): self {
        foreach ($config as $key => $value) {
            match ($key) {
                'width' => $this->setWidth($value),
                'minWidth', 'min_width' => $this->setMinWidth($value),
                'maxWidth', 'max_width' => $this->setMaxWidth($value),
                'alignment', 'align' => $this->setAlignment($value),
                'truncate' => $this->setTruncate($value),
                'ellipsis' => $this->setEllipsis($value),
                'wordWrap', 'word_wrap' => $this->setWordWrap($value),
                'formatter' => $this->setFormatter($value),
                'colorizer' => $this->setColorizer($value),
                'defaultValue', 'default_value', 'default' => $this->setDefaultValue($value),
                'visible' => $this->setVisible($value),
                default => $this->setMetadata($key, $value)
            };
        }

        return $this;
    }

    /**
     * Create a quick column configuration.
     */
    public static function create(string $name): self {
        return new self($name);
    }

    /**
     * Create a date column with formatting.
     */
    public static function date(string $name, ?int $width = null, string $format = 'Y-m-d'): self {
        return (new self($name))
            ->setAlignment(self::ALIGN_LEFT)
            ->setWidth($width)
            ->setFormatter(function ($value) use ($format) {
                if (empty($value)) {
                    return '';
                }

                try {
                    if (is_string($value)) {
                        $date = new \DateTime($value);
                    } elseif ($value instanceof \DateTime) {
                        $date = $value;
                    } else {
                        return (string)$value;
                    }

                    return $date->format($format);
                } catch (\Exception $e) {
                    return (string)$value;
                }
            });
    }

    /**
     * Format a value using the column's formatter.
     */
    public function formatValue(mixed $value): string {
        // Handle null/empty values
        if ($value === null || $value === '') {
            return (string)$this->defaultValue;
        }

        // Apply custom formatter if set
        if ($this->formatter !== null) {
            $value = call_user_func($this->formatter, $value);
        }

        return (string)$value;
    }

    /**
     * Get alignment.
     */
    public function getAlignment(): string {
        return $this->alignment;
    }

    /**
     * Get all metadata.
     */
    public function getAllMetadata(): array {
        return $this->metadata;
    }

    /**
     * Get colorizer function.
     */
    public function getColorizer() {
        return $this->colorizer;
    }

    /**
     * Get default value.
     */
    public function getDefaultValue(): mixed {
        return $this->defaultValue;
    }

    /**
     * Get ellipsis string.
     */
    public function getEllipsis(): string {
        return $this->ellipsis;
    }

    /**
     * Get formatter function.
     */
    public function getFormatter() {
        return $this->formatter;
    }

    /**
     * Get maximum width.
     */
    public function getMaxWidth(): ?int {
        return $this->maxWidth;
    }

    /**
     * Get metadata value.
     */
    public function getMetadata(string $key, mixed $default = null): mixed {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Get minimum width.
     */
    public function getMinWidth(): ?int {
        return $this->minWidth;
    }

    /**
     * Get column name.
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Get column width.
     */
    public function getWidth(): ?int {
        return $this->width;
    }

    /**
     * Check if column is visible.
     */
    public function isVisible(): bool {
        return $this->visible;
    }

    /**
     * Create a left-aligned column.
     */
    public static function left(string $name, ?int $width = null): self {
        return (new self($name))->setAlignment(self::ALIGN_LEFT)->setWidth($width);
    }

    /**
     * Create a numeric column (right-aligned with number formatting).
     */
    public static function numeric(string $name, ?int $width = null, int $decimals = 2): self {
        return (new self($name))
            ->setAlignment(self::ALIGN_RIGHT)
            ->setWidth($width)
            ->setFormatter(fn($value) => is_numeric($value) ? number_format((float)$value, $decimals) : $value);
    }

    /**
     * Create a right-aligned column.
     */
    public static function right(string $name, ?int $width = null): self {
        return (new self($name))->setAlignment(self::ALIGN_RIGHT)->setWidth($width);
    }

    /**
     * Set text alignment.
     */
    public function setAlignment(string $alignmentValue): self {
        $validAlignments = [self::ALIGN_LEFT, self::ALIGN_RIGHT, self::ALIGN_CENTER, self::ALIGN_AUTO];

        if (in_array($alignmentValue, $validAlignments)) {
            $this->alignment = $alignmentValue;
        }

        return $this;
    }

    /**
     * Set color function.
     */
    public function setColorizer($colorizer): self {
        $this->colorizer = $colorizer;

        return $this;
    }

    /**
     * Set default value for empty cells.
     */
    public function setDefaultValue(mixed $defaultValue): self {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Set ellipsis string for truncated text.
     */
    public function setEllipsis(string $ellipsis): self {
        $this->ellipsis = $ellipsis;

        return $this;
    }

    /**
     * Set content formatter function.
     */
    public function setFormatter($formatter): self {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * Set maximum width.
     */
    public function setMaxWidth(?int $maxWidth): self {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    /**
     * Set custom metadata.
     */
    public function setMetadata(string $key, mixed $value): self {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * Set minimum width.
     */
    public function setMinWidth(?int $minWidth): self {
        $this->minWidth = $minWidth;

        return $this;
    }

    /**
     * Enable/disable text truncation.
     */
    public function setTruncate(bool $truncate): self {
        $this->truncate = $truncate;

        return $this;
    }

    /**
     * Set column visibility.
     */
    public function setVisible(bool $visible): self {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Set column width.
     */
    public function setWidth(?int $width): self {
        $this->width = $width;

        return $this;
    }

    /**
     * Enable/disable word wrapping.
     */
    public function setWordWrap(bool $wordWrap): self {
        $this->wordWrap = $wordWrap;

        return $this;
    }

    /**
     * Check if truncation is enabled.
     */
    public function shouldTruncate(): bool {
        return $this->truncate;
    }

    /**
     * Check if word wrap is enabled.
     */
    public function shouldWordWrap(): bool {
        return $this->wordWrap;
    }

    /**
     * Truncate text to fit column width.
     */
    public function truncateText(string $text, int $width): string {
        if (!$this->truncate) {
            return $text;
        }

        $displayLength = $this->getDisplayLength($text);

        if ($displayLength <= $width) {
            return $text;
        }

        $ellipsisLength = strlen($this->ellipsis);
        $maxLength = $width - $ellipsisLength;

        if ($maxLength <= 0) {
            return str_repeat('.', min($width, 3));
        }

        // Simple truncation for now - could be enhanced for word boundaries
        $truncated = substr($text, 0, $maxLength);

        return $truncated.$this->ellipsis;
    }

    /**
     * Apply ANSI colors to text.
     */
    private function applyAnsiColors(string $text, array $colorConfig): string {
        $codes = [];

        // Foreground colors
        if (isset($colorConfig['color'])) {
            $codes[] = $this->getAnsiColorCode($colorConfig['color']);
        }

        // Background colors
        if (isset($colorConfig['background'])) {
            $codes[] = $this->getAnsiColorCode($colorConfig['background'], true);
        }

        // Text styles
        if (isset($colorConfig['bold']) && $colorConfig['bold']) {
            $codes[] = '1';
        }

        if (isset($colorConfig['underline']) && $colorConfig['underline']) {
            $codes[] = '4';
        }

        if (empty($codes)) {
            return $text;
        }

        return "\x1b[".implode(';', $codes)."m".$text."\x1b[0m";
    }

    /**
     * Get ANSI color code for color name.
     */
    private function getAnsiColorCode(string $color, bool $background = false): string {
        $colors = [
            'black' => $background ? '40' : '30',
            'red' => $background ? '41' : '31',
            'green' => $background ? '42' : '32',
            'yellow' => $background ? '43' : '33',
            'blue' => $background ? '44' : '34',
            'magenta' => $background ? '45' : '35',
            'cyan' => $background ? '46' : '36',
            'white' => $background ? '47' : '37',
            'light-red' => $background ? '101' : '91',
            'light-green' => $background ? '102' : '92',
            'light-yellow' => $background ? '103' : '93',
            'light-blue' => $background ? '104' : '94',
            'light-magenta' => $background ? '105' : '95',
            'light-cyan' => $background ? '106' : '96',
        ];

        return $colors[strtolower($color)] ?? ($background ? '40' : '30');
    }

    /**
     * Get display length of text (accounting for ANSI codes).
     */
    private function getDisplayLength(string $text): int {
        // Remove ANSI escape sequences for length calculation
        $cleaned = preg_replace('/\x1b\[[0-9;]*m/', '', $text);

        return strlen($cleaned ?? $text);
    }

    /**
     * Resolve auto alignment based on content.
     */
    private function resolveAlignment(string $text): string {
        if ($this->alignment !== self::ALIGN_AUTO) {
            return $this->alignment;
        }

        // Auto-detect: numbers right-aligned, text left-aligned
        if (is_numeric(trim($text))) {
            return self::ALIGN_RIGHT;
        }

        return self::ALIGN_LEFT;
    }
}
