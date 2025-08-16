<?php

namespace WebFiori\Cli\Table;

/**
 * TableTheme - Higher-level theming system for tables.
 * 
 * This class provides color schemes, style combinations, and CLI integration
 * for consistent table appearance across applications.
 * 
 * @author WebFiori Framework
 * @version 1.0.0
 */
class TableTheme {
    
    private array $headerColors = [];
    private array $cellColors = [];
    private array $alternatingRowColors = [];
    private bool $useAlternatingRows = false;
    private array $statusColors = [];
    private $headerStyler = null;
    private $cellStyler = null;
    
    public function __construct(array $config = []) {
        $this->configure($config);
    }
    
    /**
     * Configure theme with options array.
     */
    public function configure(array $config): self {
        foreach ($config as $key => $value) {
            match($key) {
                'headerColors', 'header_colors' => $this->headerColors = $value,
                'cellColors', 'cell_colors' => $this->cellColors = $value,
                'alternatingRowColors', 'alternating_row_colors' => $this->alternatingRowColors = $value,
                'useAlternatingRows', 'use_alternating_rows' => $this->useAlternatingRows = $value,
                'statusColors', 'status_colors' => $this->statusColors = $value,
                'headerStyler', 'header_styler' => $this->headerStyler = $value,
                'cellStyler', 'cell_styler' => $this->cellStyler = $value,
                default => null
            };
        }
        
        return $this;
    }
    
    /**
     * Apply header styling.
     */
    public function applyHeaderStyle(string $text): string {
        // Apply custom header styler if available
        if ($this->headerStyler !== null) {
            $text = call_user_func($this->headerStyler, $text);
        }
        
        // Apply header colors
        if (!empty($this->headerColors)) {
            $text = $this->applyColors($text, $this->headerColors);
        }
        
        return $text;
    }
    
    /**
     * Apply cell styling.
     */
    public function applyCellStyle(string $text, int $rowIndex, int $columnIndex): string {
        // Apply custom cell styler if available
        if ($this->cellStyler !== null) {
            $text = call_user_func($this->cellStyler, $text, $rowIndex, $columnIndex);
        }
        
        // Apply alternating row colors
        if ($this->useAlternatingRows && !empty($this->alternatingRowColors)) {
            $colorIndex = $rowIndex % count($this->alternatingRowColors);
            $colors = $this->alternatingRowColors[$colorIndex];
            $text = $this->applyColors($text, $colors);
        }
        
        // Apply general cell colors
        elseif (!empty($this->cellColors)) {
            $text = $this->applyColors($text, $this->cellColors);
        }
        
        // Apply status-based colors
        $text = $this->applyStatusColors($text);
        
        return $text;
    }
    
    /**
     * Set header colors.
     */
    public function setHeaderColors(array $colors): self {
        $this->headerColors = $colors;
        return $this;
    }
    
    /**
     * Set cell colors.
     */
    public function setCellColors(array $colors): self {
        $this->cellColors = $colors;
        return $this;
    }
    
    /**
     * Set alternating row colors.
     */
    public function setAlternatingRowColors(array $colors): self {
        $this->alternatingRowColors = $colors;
        $this->useAlternatingRows = !empty($colors);
        return $this;
    }
    
    /**
     * Enable/disable alternating rows.
     */
    public function useAlternatingRows(bool $use = true): self {
        $this->useAlternatingRows = $use;
        return $this;
    }
    
    /**
     * Set status-based colors.
     */
    public function setStatusColors(array $colors): self {
        $this->statusColors = $colors;
        return $this;
    }
    
    /**
     * Set custom header styler function.
     */
    public function setHeaderStyler($styler): self {
        $this->headerStyler = $styler;
        return $this;
    }
    
    /**
     * Set custom cell styler function.
     */
    public function setCellStyler($styler): self {
        $this->cellStyler = $styler;
        return $this;
    }
    
    /**
     * Create a default theme.
     */
    public static function default(): self {
        return new self([
            'headerColors' => ['color' => 'white', 'bold' => true],
            'cellColors' => [],
            'useAlternatingRows' => false
        ]);
    }
    
    /**
     * Create a dark theme.
     */
    public static function dark(): self {
        return new self([
            'headerColors' => ['color' => 'light-cyan', 'bold' => true],
            'cellColors' => ['color' => 'white'],
            'alternatingRowColors' => [
                [],
                ['background' => 'black']
            ],
            'useAlternatingRows' => true,
            'statusColors' => [
                'success' => ['color' => 'light-green'],
                'error' => ['color' => 'light-red'],
                'warning' => ['color' => 'light-yellow'],
                'info' => ['color' => 'light-blue']
            ]
        ]);
    }
    
    /**
     * Create a light theme.
     */
    public static function light(): self {
        return new self([
            'headerColors' => ['color' => 'blue', 'bold' => true],
            'cellColors' => ['color' => 'black'],
            'alternatingRowColors' => [
                [],
                ['background' => 'white']
            ],
            'useAlternatingRows' => true,
            'statusColors' => [
                'success' => ['color' => 'green'],
                'error' => ['color' => 'red'],
                'warning' => ['color' => 'yellow'],
                'info' => ['color' => 'blue']
            ]
        ]);
    }
    
    /**
     * Create a colorful theme.
     */
    public static function colorful(): self {
        return new self([
            'headerColors' => ['color' => 'magenta', 'bold' => true, 'underline' => true],
            'cellColors' => [],
            'alternatingRowColors' => [
                ['color' => 'cyan'],
                ['color' => 'light-cyan']
            ],
            'useAlternatingRows' => true,
            'statusColors' => [
                'active' => ['color' => 'green', 'bold' => true],
                'inactive' => ['color' => 'red'],
                'pending' => ['color' => 'yellow'],
                'success' => ['color' => 'light-green', 'bold' => true],
                'error' => ['color' => 'light-red', 'bold' => true],
                'warning' => ['color' => 'light-yellow'],
                'info' => ['color' => 'light-blue']
            ]
        ]);
    }
    
    /**
     * Create a minimal theme (no colors).
     */
    public static function minimal(): self {
        return new self([
            'headerColors' => ['bold' => true],
            'cellColors' => [],
            'useAlternatingRows' => false
        ]);
    }
    
    /**
     * Create a professional theme.
     */
    public static function professional(): self {
        return new self([
            'headerColors' => ['color' => 'white', 'background' => 'blue', 'bold' => true],
            'cellColors' => [],
            'alternatingRowColors' => [
                [],
                ['background' => 'light-blue']
            ],
            'useAlternatingRows' => true,
            'statusColors' => [
                'active' => ['color' => 'green'],
                'inactive' => ['color' => 'red'],
                'pending' => ['color' => 'yellow']
            ]
        ]);
    }
    
    /**
     * Create a high contrast theme for accessibility.
     */
    public static function highContrast(): self {
        return new self([
            'headerColors' => ['color' => 'white', 'background' => 'black', 'bold' => true],
            'cellColors' => ['color' => 'white', 'background' => 'black'],
            'useAlternatingRows' => false,
            'statusColors' => [
                'success' => ['color' => 'white', 'background' => 'green', 'bold' => true],
                'error' => ['color' => 'white', 'background' => 'red', 'bold' => true],
                'warning' => ['color' => 'black', 'background' => 'yellow', 'bold' => true],
                'info' => ['color' => 'white', 'background' => 'blue', 'bold' => true]
            ]
        ]);
    }
    
    /**
     * Create theme from CLI environment.
     */
    public static function fromEnvironment(): self {
        // Detect terminal capabilities and user preferences
        $supportsColor = $this->detectColorSupport();
        $isDarkTerminal = $this->detectDarkTerminal();
        
        if (!$supportsColor) {
            return self::minimal();
        }
        
        return $isDarkTerminal ? self::dark() : self::light();
    }
    
    /**
     * Apply ANSI colors to text.
     */
    private function applyColors(string $text, array $colors): string {
        if (empty($colors)) {
            return $text;
        }
        
        $codes = [];
        
        // Foreground colors
        if (isset($colors['color'])) {
            $codes[] = $this->getColorCode($colors['color']);
        }
        
        // Background colors
        if (isset($colors['background'])) {
            $codes[] = $this->getColorCode($colors['background'], true);
        }
        
        // Text styles
        if (isset($colors['bold']) && $colors['bold']) {
            $codes[] = '1';
        }
        
        if (isset($colors['underline']) && $colors['underline']) {
            $codes[] = '4';
        }
        
        if (isset($colors['italic']) && $colors['italic']) {
            $codes[] = '3';
        }
        
        if (empty($codes)) {
            return $text;
        }
        
        return "\x1b[" . implode(';', $codes) . "m" . $text . "\x1b[0m";
    }
    
    /**
     * Apply status-based colors.
     */
    private function applyStatusColors(string $text): string {
        if (empty($this->statusColors)) {
            return $text;
        }
        
        $lowerText = strtolower(trim($text));
        
        foreach ($this->statusColors as $status => $colors) {
            if (strpos($lowerText, strtolower($status)) !== false) {
                return $this->applyColors($text, $colors);
            }
        }
        
        return $text;
    }
    
    /**
     * Get ANSI color code.
     */
    private function getColorCode(string $color, bool $background = false): string {
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
     * Detect if terminal supports colors.
     */
    private static function detectColorSupport(): bool {
        // Check environment variables
        $term = getenv('TERM');
        $colorTerm = getenv('COLORTERM');
        
        if ($colorTerm) {
            return true;
        }
        
        if ($term && (
            strpos($term, 'color') !== false ||
            strpos($term, '256') !== false ||
            strpos($term, 'xterm') !== false
        )) {
            return true;
        }
        
        // Check if running in a known terminal
        if (getenv('TERM_PROGRAM')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Detect if terminal has dark background.
     */
    private static function detectDarkTerminal(): bool {
        // This is a best guess - terminal background detection is limited
        $term = getenv('TERM');
        $termProgram = getenv('TERM_PROGRAM');
        
        // Some terminals are typically dark by default
        if ($termProgram && in_array($termProgram, ['iTerm.app', 'Terminal.app'])) {
            return true;
        }
        
        // Default assumption for most terminals
        return true;
    }
    
    /**
     * Create a custom theme with specific colors.
     */
    public static function custom(array $config): self {
        return new self($config);
    }
    
    /**
     * Get available theme names.
     */
    public static function getAvailableThemes(): array {
        return [
            'default',
            'dark',
            'light',
            'colorful',
            'minimal',
            'professional',
            'high-contrast'
        ];
    }
    
    /**
     * Create theme by name.
     */
    public static function create(string $name): self {
        return match(strtolower($name)) {
            'dark' => self::dark(),
            'light' => self::light(),
            'colorful' => self::colorful(),
            'minimal' => self::minimal(),
            'professional' => self::professional(),
            'high-contrast', 'highcontrast' => self::highContrast(),
            'environment', 'auto' => self::fromEnvironment(),
            default => self::default()
        };
    }
}
