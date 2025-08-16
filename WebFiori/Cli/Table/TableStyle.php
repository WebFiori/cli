<?php

namespace WebFiori\Cli\Table;

/**
 * TableStyle - Defines visual styling for tables.
 * 
 * This class contains all the characters and formatting rules used
 * to render tables in different visual styles.
 * 
 * @author WebFiori Framework
 * @version 1.0.0
 */
class TableStyle {
    
    public readonly string $topLeft;
    public readonly string $topRight;
    public readonly string $bottomLeft;
    public readonly string $bottomRight;
    public readonly string $horizontal;
    public readonly string $vertical;
    public readonly string $cross;
    public readonly string $topTee;
    public readonly string $bottomTee;
    public readonly string $leftTee;
    public readonly string $rightTee;
    public readonly int $paddingLeft;
    public readonly int $paddingRight;
    public readonly bool $showBorders;
    public readonly bool $showHeaderSeparator;
    public readonly bool $showRowSeparators;
    
    public function __construct(
        string $topLeft = '┌',
        string $topRight = '┐',
        string $bottomLeft = '└',
        string $bottomRight = '┘',
        string $horizontal = '─',
        string $vertical = '│',
        string $cross = '┼',
        string $topTee = '┬',
        string $bottomTee = '┴',
        string $leftTee = '├',
        string $rightTee = '┤',
        int $paddingLeft = 1,
        int $paddingRight = 1,
        bool $showBorders = true,
        bool $showHeaderSeparator = true,
        bool $showRowSeparators = false
    ) {
        $this->topLeft = $topLeft;
        $this->topRight = $topRight;
        $this->bottomLeft = $bottomLeft;
        $this->bottomRight = $bottomRight;
        $this->horizontal = $horizontal;
        $this->vertical = $vertical;
        $this->cross = $cross;
        $this->topTee = $topTee;
        $this->bottomTee = $bottomTee;
        $this->leftTee = $leftTee;
        $this->rightTee = $rightTee;
        $this->paddingLeft = $paddingLeft;
        $this->paddingRight = $paddingRight;
        $this->showBorders = $showBorders;
        $this->showHeaderSeparator = $showHeaderSeparator;
        $this->showRowSeparators = $showRowSeparators;
    }
    
    /**
     * Default bordered style with Unicode box-drawing characters.
     */
    public static function default(): self {
        return new self();
    }
    
    /**
     * Bordered style (same as default).
     */
    public static function bordered(): self {
        return self::default();
    }
    
    /**
     * Simple ASCII style for maximum compatibility.
     */
    public static function simple(): self {
        return new self(
            topLeft: '+',
            topRight: '+',
            bottomLeft: '+',
            bottomRight: '+',
            horizontal: '-',
            vertical: '|',
            cross: '+',
            topTee: '+',
            bottomTee: '+',
            leftTee: '+',
            rightTee: '+'
        );
    }
    
    /**
     * Minimal style with reduced borders.
     */
    public static function minimal(): self {
        return new self(
            topLeft: '',
            topRight: '',
            bottomLeft: '',
            bottomRight: '',
            horizontal: '─',
            vertical: '',
            cross: '',
            topTee: '',
            bottomTee: '',
            leftTee: '',
            rightTee: '',
            showBorders: false,
            showHeaderSeparator: true
        );
    }
    
    /**
     * Compact style with minimal spacing.
     */
    public static function compact(): self {
        return new self(
            paddingLeft: 0,
            paddingRight: 1,
            showBorders: false,
            showHeaderSeparator: true
        );
    }
    
    /**
     * Markdown-compatible table style.
     */
    public static function markdown(): self {
        return new self(
            topLeft: '',
            topRight: '',
            bottomLeft: '',
            bottomRight: '',
            horizontal: '-',
            vertical: '|',
            cross: '|',
            topTee: '',
            bottomTee: '',
            leftTee: '|',
            rightTee: '|',
            paddingLeft: 1,
            paddingRight: 1,
            showBorders: true,
            showHeaderSeparator: true,
            showRowSeparators: false
        );
    }
    
    /**
     * Double-line bordered style.
     */
    public static function doubleBordered(): self {
        return new self(
            topLeft: '╔',
            topRight: '╗',
            bottomLeft: '╚',
            bottomRight: '╝',
            horizontal: '═',
            vertical: '║',
            cross: '╬',
            topTee: '╦',
            bottomTee: '╩',
            leftTee: '╠',
            rightTee: '╣'
        );
    }
    
    /**
     * Rounded corners style.
     */
    public static function rounded(): self {
        return new self(
            topLeft: '╭',
            topRight: '╮',
            bottomLeft: '╰',
            bottomRight: '╯',
            horizontal: '─',
            vertical: '│',
            cross: '┼',
            topTee: '┬',
            bottomTee: '┴',
            leftTee: '├',
            rightTee: '┤'
        );
    }
    
    /**
     * Heavy/thick borders style.
     */
    public static function heavy(): self {
        return new self(
            topLeft: '┏',
            topRight: '┓',
            bottomLeft: '┗',
            bottomRight: '┛',
            horizontal: '━',
            vertical: '┃',
            cross: '╋',
            topTee: '┳',
            bottomTee: '┻',
            leftTee: '┣',
            rightTee: '┫'
        );
    }
    
    /**
     * No borders style - just data with spacing.
     */
    public static function none(): self {
        return new self(
            topLeft: '',
            topRight: '',
            bottomLeft: '',
            bottomRight: '',
            horizontal: '',
            vertical: '',
            cross: '',
            topTee: '',
            bottomTee: '',
            leftTee: '',
            rightTee: '',
            paddingLeft: 0,
            paddingRight: 2,
            showBorders: false,
            showHeaderSeparator: false,
            showRowSeparators: false
        );
    }
    
    /**
     * Get total padding width (left + right).
     */
    public function getTotalPadding(): int {
        return $this->paddingLeft + $this->paddingRight;
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
     * Get ASCII fallback for this style.
     */
    public function getAsciiFallback(): self {
        if (!$this->isUnicode()) {
            return $this;
        }
        
        return self::simple();
    }
    
    /**
     * Create a custom style with specific overrides.
     */
    public static function custom(array $overrides): self {
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
        
        $config = array_merge($defaults, $overrides);
        
        return new self(
            topLeft: $config['topLeft'],
            topRight: $config['topRight'],
            bottomLeft: $config['bottomLeft'],
            bottomRight: $config['bottomRight'],
            horizontal: $config['horizontal'],
            vertical: $config['vertical'],
            cross: $config['cross'],
            topTee: $config['topTee'],
            bottomTee: $config['bottomTee'],
            leftTee: $config['leftTee'],
            rightTee: $config['rightTee'],
            paddingLeft: $config['paddingLeft'],
            paddingRight: $config['paddingRight'],
            showBorders: $config['showBorders'],
            showHeaderSeparator: $config['showHeaderSeparator'],
            showRowSeparators: $config['showRowSeparators']
        );
    }
}
