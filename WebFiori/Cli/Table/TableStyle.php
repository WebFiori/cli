<?php
namespace WebFiori\Cli\Table;

/**
 * TableStyle - Defines visual styling for tables.
 * 
 * This class contains all the characters and formatting rules used
 * to render tables in different visual styles.
 * 
 * @author Ibrahim
 */
class TableStyle {
    /**
     * Bordered style (alias for default).
     * 
     * @var string
     */
    const BORDERED = 'bordered';

    /**
     * Compact style with minimal spacing.
     * 
     * @var string
     */
    const COMPACT = 'compact';

    /**
     * Style name constants for supported table styles.
     * 
     * These constants provide type safety and IDE autocompletion when
     * specifying table styles in configuration.
     */

    /**
     * Default bordered style with Unicode box-drawing characters.
     * 
     * @var string
     */
    const DEFAULT = 'default';

    /**
     * Double-line bordered style.
     * 
     * @var string
     */
    const DOUBLE_BORDERED = 'double-bordered';

    /**
     * Heavy/thick borders style.
     * 
     * @var string
     */
    const HEAVY = 'heavy';

    /**
     * Markdown-compatible table style.
     * 
     * @var string
     */
    const MARKDOWN = 'markdown';

    /**
     * Minimal style with reduced borders.
     * 
     * @var string
     */
    const MINIMAL = 'minimal';

    /**
     * No borders style - just data with spacing.
     * 
     * @var string
     */
    const NONE = 'none';

    /**
     * Rounded corners style.
     * 
     * @var string
     */
    const ROUNDED = 'rounded';

    /**
     * Simple ASCII style for maximum compatibility.
     * 
     * @var string
     */
    const SIMPLE = 'simple';
    public readonly string $bottomLeft;
    public readonly string $bottomRight;
    public readonly string $bottomTee;
    public readonly string $cross;
    public readonly string $horizontal;
    public readonly string $leftTee;
    public readonly int $paddingLeft;
    public readonly int $paddingRight;
    public readonly string $rightTee;
    public readonly bool $showBorders;
    public readonly bool $showHeaderSeparator;
    public readonly bool $showRowSeparators;

    public readonly string $topLeft;
    public readonly string $topRight;
    public readonly string $topTee;
    public readonly string $vertical;

    public function __construct(array $components = []) {
        // Default values for all table components
        $defaults = [
            'topLeft' => '┌',
            'topRight' => '┐',
            'bottomLeft' => '└',
            'bottomRight' => '┘',
            'horizontal' => '─',
            'vertical' => '│',
            'cross' => '┼',
            'topTee' => '┬',
            'bottomTee' => '┴',
            'leftTee' => '├',
            'rightTee' => '┤',
            'paddingLeft' => 1,
            'paddingRight' => 1,
            'showBorders' => true,
            'showHeaderSeparator' => true,
            'showRowSeparators' => false
        ];

        // Merge provided components with defaults
        $config = array_merge($defaults, $components);

        // Assign values to readonly properties
        $this->topLeft = $config['topLeft'];
        $this->topRight = $config['topRight'];
        $this->bottomLeft = $config['bottomLeft'];
        $this->bottomRight = $config['bottomRight'];
        $this->horizontal = $config['horizontal'];
        $this->vertical = $config['vertical'];
        $this->cross = $config['cross'];
        $this->topTee = $config['topTee'];
        $this->bottomTee = $config['bottomTee'];
        $this->leftTee = $config['leftTee'];
        $this->rightTee = $config['rightTee'];
        $this->paddingLeft = $config['paddingLeft'];
        $this->paddingRight = $config['paddingRight'];
        $this->showBorders = $config['showBorders'];
        $this->showHeaderSeparator = $config['showHeaderSeparator'];
        $this->showRowSeparators = $config['showRowSeparators'];
    }

    /**
     * Bordered style (same as default).
     */
    public static function bordered(): self {
        return self::default();
    }

    /**
     * Compact style with minimal spacing.
     */
    public static function compact(): self {
        return new self([
            'paddingLeft' => 0,
            'paddingRight' => 1,
            'showBorders' => false,
            'showHeaderSeparator' => true
        ]);
    }

    /**
     * Create a style by name.
     * 
     * @param string $name The style name
     * @return self The style instance
     */
    public static function create(string $name): self {
        return match (strtolower($name)) {
            self::DEFAULT, self::BORDERED => self::default(),
            self::SIMPLE => self::simple(),
            self::MINIMAL => self::minimal(),
            self::COMPACT => self::compact(),
            self::MARKDOWN => self::markdown(),
            self::DOUBLE_BORDERED, 'double-bordered', 'doublebordered' => self::doubleBordered(),
            self::ROUNDED => self::rounded(),
            self::HEAVY => self::heavy(),
            self::NONE => self::none(),
            default => self::default()
        };
    }

    /**
     * Create a custom style with specific overrides.
     */
    public static function custom(array $overrides): self {
        return new self($overrides);
    }

    /**
     * Default bordered style with Unicode box-drawing characters.
     */
    public static function default(): self {
        return new self();
    }

    /**
     * Double-line bordered style.
     */
    public static function doubleBordered(): self {
        return new self([
            'topLeft' => '╔',
            'topRight' => '╗',
            'bottomLeft' => '╚',
            'bottomRight' => '╝',
            'horizontal' => '═',
            'vertical' => '║',
            'cross' => '╬',
            'topTee' => '╦',
            'bottomTee' => '╩',
            'leftTee' => '╠',
            'rightTee' => '╣'
        ]);
    }

    /**
     * Get ASCII fallback for this style.
     */
    public function getAsciiFallback(): self {
        if (!$this->isUnicode()) {
            return $this;
        }

        return self::simple();
    }

    /**
     * Get all available style names.
     * 
     * @return array Array of supported style names
     */
    public static function getAvailableStyles(): array {
        return [
            self::DEFAULT,
            self::BORDERED,
            self::SIMPLE,
            self::MINIMAL,
            self::COMPACT,
            self::MARKDOWN,
            self::DOUBLE_BORDERED,
            self::ROUNDED,
            self::HEAVY,
            self::NONE
        ];
    }

    /**
     * Get border width (number of characters used for borders).
     */
    public function getBorderWidth(int $columnCount): int {
        if (!$this->showBorders) {
            return 0;
        }

        // Left border + right border + (columnCount - 1) separators
        return 2 + ($columnCount - 1);
    }

    /**
     * Get total padding width (left + right).
     */
    public function getTotalPadding(): int {
        return $this->paddingLeft + $this->paddingRight;
    }

    /**
     * Heavy/thick borders style.
     */
    public static function heavy(): self {
        return new self([
            'topLeft' => '┏',
            'topRight' => '┓',
            'bottomLeft' => '┗',
            'bottomRight' => '┛',
            'horizontal' => '━',
            'vertical' => '┃',
            'cross' => '╋',
            'topTee' => '┳',
            'bottomTee' => '┻',
            'leftTee' => '┣',
            'rightTee' => '┫'
        ]);
    }

    /**
     * Check if this style uses Unicode characters.
     */
    public function isUnicode(): bool {
        $chars = [
            $this->topLeft, $this->topRight, $this->bottomLeft, $this->bottomRight,
            $this->horizontal, $this->vertical, $this->cross,
            $this->topTee, $this->bottomTee, $this->leftTee, $this->rightTee
        ];

        foreach ($chars as $char) {
            if (strlen($char) > 1 || ord($char) > 127) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a style name is valid.
     * 
     * @param string $styleName The style name to validate
     * @return bool True if the style is supported, false otherwise
     */
    public static function isValidStyle(string $styleName): bool {
        return in_array(strtolower($styleName), array_map('strtolower', self::getAvailableStyles()), true);
    }

    /**
     * Markdown-compatible table style.
     */
    public static function markdown(): self {
        return new self([
            'topLeft' => '',
            'topRight' => '',
            'bottomLeft' => '',
            'bottomRight' => '',
            'horizontal' => '-',
            'vertical' => '|',
            'cross' => '|',
            'topTee' => '',
            'bottomTee' => '',
            'leftTee' => '|',
            'rightTee' => '|',
            'paddingLeft' => 1,
            'paddingRight' => 1,
            'showBorders' => true,
            'showHeaderSeparator' => true,
            'showRowSeparators' => false
        ]);
    }

    /**
     * Minimal style with reduced borders.
     */
    public static function minimal(): self {
        return new self([
            'topLeft' => '',
            'topRight' => '',
            'bottomLeft' => '',
            'bottomRight' => '',
            'horizontal' => '─',
            'vertical' => '',
            'cross' => '',
            'topTee' => '',
            'bottomTee' => '',
            'leftTee' => '',
            'rightTee' => '',
            'showBorders' => false,
            'showHeaderSeparator' => true
        ]);
    }

    /**
     * No borders style - just data with spacing.
     */
    public static function none(): self {
        return new self([
            'topLeft' => '',
            'topRight' => '',
            'bottomLeft' => '',
            'bottomRight' => '',
            'horizontal' => '',
            'vertical' => '',
            'cross' => '',
            'topTee' => '',
            'bottomTee' => '',
            'leftTee' => '',
            'rightTee' => '',
            'paddingLeft' => 0,
            'paddingRight' => 2,
            'showBorders' => false,
            'showHeaderSeparator' => false,
            'showRowSeparators' => false
        ]);
    }

    /**
     * Rounded corners style.
     */
    public static function rounded(): self {
        return new self([
            'topLeft' => '╭',
            'topRight' => '╮',
            'bottomLeft' => '╰',
            'bottomRight' => '╯',
            'horizontal' => '─',
            'vertical' => '│',
            'cross' => '┼',
            'topTee' => '┬',
            'bottomTee' => '┴',
            'leftTee' => '├',
            'rightTee' => '┤'
        ]);
    }

    /**
     * Simple ASCII style for maximum compatibility.
     */
    public static function simple(): self {
        return new self([
            'topLeft' => '+',
            'topRight' => '+',
            'bottomLeft' => '+',
            'bottomRight' => '+',
            'horizontal' => '-',
            'vertical' => '|',
            'cross' => '+',
            'topTee' => '+',
            'bottomTee' => '+',
            'leftTee' => '+',
            'rightTee' => '+'
        ]);
    }
}
