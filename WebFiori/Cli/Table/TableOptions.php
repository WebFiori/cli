<?php
declare(strict_types=1);
namespace WebFiori\Cli\Table;

/**
 * A class that contains constants for table options used in the Command::table() method.
 * 
 * This class provides a centralized location for all option keys that can be used
 * when configuring tables through the Command::table() method. Using these constants
 * helps prevent typos and provides better IDE support with autocompletion.
 * 
 * @author Ibrahim
 */
class TableOptions {
    /**
     * Auto-width calculation option key.
     * 
     * Specifies whether to automatically calculate column widths based on content.
     * 
     * Supported values:
     * - true (default): Automatically calculate column widths
     * - false: Use fixed column widths
     * 
     * @var string
     */
    const AUTO_WIDTH = 'autoWidth';

    /**
     * Column colorization option key.
     * 
     * Specifies column-specific colorization rules as an associative array.
     * The key should be the column name or index, and the value should be
     * a callable that returns ANSI color configuration.
     * 
     * Example:
     * ```php
     * [
     *     'Status' => function($value) {
     *         return match(strtolower($value)) {
     *             'active' => ['color' => 'green', 'bold' => true],
     *             'inactive' => ['color' => 'red'],
     *             default => []
     *         };
     *     }
     * ]
     * ```
     * 
     * @var string
     */
    const COLORIZE = 'colorize';

    /**
     * Column configuration option key.
     * 
     * Specifies column-specific configuration as an associative array.
     * The key should be the column name or index, and the value should be
     * an array of column configuration options.
     * 
     * Example:
     * ```php
     * [
     *     'Price' => [
     *         'align' => 'right',
     *         'width' => 10,
     *         'formatter' => fn($v) => '$' . number_format($v, 2)
     *     ]
     * ]
     * ```
     * 
     * @var string
     */
    const COLUMNS = 'columns';

    /**
     * Truncation ellipsis option key.
     * 
     * Specifies the string to use when truncating long content.
     * 
     * Default value: '...'
     * 
     * @var string
     */
    const ELLIPSIS = 'ellipsis';

    /**
     * Filter option key.
     * 
     * Specifies filtering configuration for the table data.
     * 
     * Should be a callable that receives a row and returns true/false:
     * ```php
     * function($row) {
     *     return $row['status'] === 'active';
     * }
     * ```
     * 
     * @var string
     */
    const FILTER = 'filter';

    /**
     * Limit option key.
     * 
     * Specifies the maximum number of rows to display.
     * 
     * Can be:
     * - An integer: Maximum number of rows
     * - An array: ['limit' => 10, 'offset' => 0]
     * 
     * @var string
     */
    const LIMIT = 'limit';

    /**
     * Table padding option key.
     * 
     * Specifies the padding configuration for table cells.
     * 
     * Can be:
     * - An integer: Same padding for all sides
     * - An array: ['left' => 1, 'right' => 1, 'top' => 0, 'bottom' => 0]
     * 
     * @var string
     */
    const PADDING = 'padding';

    /**
     * Header separator option key.
     * 
     * Specifies whether to show a separator between headers and data.
     * 
     * Supported values:
     * - true (default): Show header separator
     * - false: Hide header separator
     * 
     * @var string
     */
    const SHOW_HEADER_SEPARATOR = 'showHeaderSeparator';

    /**
     * Show headers option key.
     * 
     * Specifies whether to display column headers.
     * 
     * Supported values:
     * - true (default): Show column headers
     * - false: Hide column headers
     * 
     * @var string
     */
    const SHOW_HEADERS = 'showHeaders';

    /**
     * Row separators option key.
     * 
     * Specifies whether to show separators between data rows.
     * 
     * Supported values:
     * - true: Show row separators
     * - false (default): Hide row separators
     * 
     * @var string
     */
    const SHOW_ROW_SEPARATORS = 'showRowSeparators';

    /**
     * Sort option key.
     * 
     * Specifies sorting configuration for the table data.
     * 
     * Can be:
     * - A string: Column name to sort by (ascending)
     * - An array: ['column' => 'Name', 'direction' => 'asc|desc']
     * 
     * @var string
     */
    const SORT = 'sort';

    /**
     * Table style option key.
     * 
     * Specifies the visual style of the table borders and layout.
     * 
     * Supported values:
     * - 'bordered' (default): Unicode box-drawing characters
     * - 'simple': ASCII characters for compatibility
     * - 'minimal': Clean look with minimal borders
     * - 'compact': Space-efficient layout
     * - 'markdown': Markdown-compatible format
     * 
     * @var string
     */
    const STYLE = 'style';

    /**
     * Color theme option key.
     * 
     * Specifies the color scheme to apply to the table.
     * 
     * Supported values:
     * - 'default' (default): Standard theme with basic colors
     * - 'dark': Optimized for dark terminals
     * - 'light': Optimized for light terminals
     * - 'colorful': Vibrant colors and styling
     * - 'professional': Business-appropriate styling
     * - 'minimal': No colors, just formatting
     * 
     * @var string
     */
    const THEME = 'theme';

    /**
     * Table title option key.
     * 
     * Specifies a title to display above the table.
     * The title will be centered and styled according to the current theme.
     * 
     * @var string
     */
    const TITLE = 'title';

    /**
     * Maximum table width option key.
     * 
     * Specifies the maximum width of the table in characters.
     * If not specified, the terminal width will be auto-detected.
     * 
     * @var string
     */
    const WIDTH = 'width';

    /**
     * Word wrap option key.
     * 
     * Specifies whether to enable word wrapping for long content.
     * 
     * Supported values:
     * - true: Enable word wrapping
     * - false (default): Disable word wrapping (content will be truncated)
     * 
     * @var string
     */
    const WORD_WRAP = 'wordWrap';

    /**
     * Get all available option keys.
     * 
     * Returns an array of all available option constants that can be used
     * with the Command::table() method.
     * 
     * @return array Array of option key constants
     */
    public static function getAllOptions(): array {
        return [
            self::STYLE,
            self::THEME,
            self::TITLE,
            self::WIDTH,
            self::SHOW_HEADERS,
            self::COLUMNS,
            self::COLORIZE,
            self::AUTO_WIDTH,
            self::SHOW_ROW_SEPARATORS,
            self::SHOW_HEADER_SEPARATOR,
            self::PADDING,
            self::WORD_WRAP,
            self::ELLIPSIS,
            self::SORT,
            self::LIMIT,
            self::FILTER
        ];
    }

    /**
     * Get data-related option keys.
     * 
     * Returns an array of option keys that affect how data is processed
     * and displayed in the table.
     * 
     * @return array Array of data-related option keys
     */
    public static function getDataOptions(): array {
        return [
            self::COLUMNS,
            self::COLORIZE,
            self::SORT,
            self::LIMIT,
            self::FILTER
        ];
    }

    /**
     * Get default values for table options.
     * 
     * Returns an array of default values for table options.
     * 
     * @return array Array of default option values
     */
    public static function getDefaults(): array {
        return [
            self::STYLE => 'bordered',
            self::THEME => 'default',
            self::TITLE => null,
            self::WIDTH => 0, // Auto-detect
            self::SHOW_HEADERS => true,
            self::COLUMNS => [],
            self::COLORIZE => [],
            self::AUTO_WIDTH => true,
            self::SHOW_ROW_SEPARATORS => false,
            self::SHOW_HEADER_SEPARATOR => true,
            self::PADDING => ['left' => 1, 'right' => 1],
            self::WORD_WRAP => false,
            self::ELLIPSIS => '...',
            self::SORT => null,
            self::LIMIT => null,
            self::FILTER => null
        ];
    }

    /**
     * Get layout-related option keys.
     * 
     * Returns an array of option keys that affect the layout and sizing
     * of the table.
     * 
     * @return array Array of layout-related option keys
     */
    public static function getLayoutOptions(): array {
        return [
            self::WIDTH,
            self::AUTO_WIDTH,
            self::WORD_WRAP,
            self::SHOW_HEADERS,
            self::TITLE
        ];
    }

    /**
     * Get style-related option keys.
     * 
     * Returns an array of option keys that affect the visual appearance
     * of the table.
     * 
     * @return array Array of style-related option keys
     */
    public static function getStyleOptions(): array {
        return [
            self::STYLE,
            self::THEME,
            self::SHOW_ROW_SEPARATORS,
            self::SHOW_HEADER_SEPARATOR,
            self::PADDING,
            self::ELLIPSIS
        ];
    }

    /**
     * Validate option key.
     * 
     * Checks if the given option key is a valid table option.
     * 
     * @param string $optionKey The option key to validate
     * @return bool True if the option key is valid, false otherwise
     */
    public static function isValidOption(string $optionKey): bool {
        return in_array($optionKey, self::getAllOptions(), true);
    }
}
