<?php
namespace WebFiori\CLI\Progress;

/**
 * Defines visual styles for progress bars.
 *
 * @author Ibrahim
 */
class ProgressBarStyle {
    /**
     * Arrow style
     */
    public const ARROW = 'arrow';

    /**
     * ASCII style for compatibility
     */
    public const ASCII = 'ascii';
    /**
     * Default style with block characters
     */
    public const DEFAULT = 'default';

    /**
     * Dots style
     */
    public const DOTS = 'dots';

    private string $barChar;
    private string $emptyChar;
    private string $progressChar;

    /**
     * Predefined styles
     */
    private static array $styles = [
        self::DEFAULT => [
            'bar_char' => '█',
            'empty_char' => '░',
            'progress_char' => '█'
        ],
        self::ASCII => [
            'bar_char' => '=',
            'empty_char' => '-',
            'progress_char' => '>'
        ],
        self::DOTS => [
            'bar_char' => '●',
            'empty_char' => '○',
            'progress_char' => '●'
        ],
        self::ARROW => [
            'bar_char' => '▶',
            'empty_char' => '▷',
            'progress_char' => '▶'
        ]
    ];

    /**
     * Creates a new progress bar style.
     * 
     * @param string $barChar Character for completed progress
     * @param string $emptyChar Character for remaining progress
     * @param string $progressChar Character for current progress position
     */
    public function __construct(string $barChar = '█', string $emptyChar = '░', string $progressChar = '█') {
        $this->barChar = $barChar;
        $this->emptyChar = $emptyChar;
        $this->progressChar = $progressChar;
    }

    /**
     * Creates a style from predefined style name.
     * 
     * @param string $styleName One of the predefined style constants
     * @return ProgressBarStyle
     */
    public static function fromName(string $styleName): ProgressBarStyle {
        if (!isset(self::$styles[$styleName])) {
            $styleName = self::DEFAULT;
        }

        $style = self::$styles[$styleName];

        return new self($style['bar_char'], $style['empty_char'], $style['progress_char']);
    }

    /**
     * Gets the character for completed progress.
     * 
     * @return string
     */
    public function getBarChar(): string {
        return $this->barChar;
    }

    /**
     * Gets the character for remaining progress.
     * 
     * @return string
     */
    public function getEmptyChar(): string {
        return $this->emptyChar;
    }

    /**
     * Gets the character for current progress position.
     * 
     * @return string
     */
    public function getProgressChar(): string {
        return $this->progressChar;
    }

    /**
     * Sets the character for completed progress.
     * 
     * @param string $char
     * @return ProgressBarStyle
     */
    public function setBarChar(string $char): ProgressBarStyle {
        $this->barChar = $char;

        return $this;
    }

    /**
     * Sets the character for remaining progress.
     * 
     * @param string $char
     * @return ProgressBarStyle
     */
    public function setEmptyChar(string $char): ProgressBarStyle {
        $this->emptyChar = $char;

        return $this;
    }

    /**
     * Sets the character for current progress position.
     * 
     * @param string $char
     * @return ProgressBarStyle
     */
    public function setProgressChar(string $char): ProgressBarStyle {
        $this->progressChar = $char;

        return $this;
    }
}
