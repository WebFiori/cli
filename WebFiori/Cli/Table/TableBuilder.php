<?php
declare(strict_types=1);
namespace WebFiori\Cli\Table;

/**
 * TableBuilder - Main interface for creating and configuring tables.
 * 
 * This class provides a fluent interface for building tables with various
 * styling options, column configurations, and data formatting capabilities.
 * 
 * @author Ibrahim
 */
class TableBuilder {
    private bool $autoWidth = true;
    private array $columns = [];

    private array $headers = [];
    private int $maxWidth = 0;
    private array $rows = [];
    private bool $showHeaders = true;
    private TableStyle $style;
    private ?TableTheme $theme = null;
    private string $title = '';

    public function __construct() {
        $this->style = TableStyle::default();
        $this->maxWidth = $this->getTerminalWidth();
    }

    /**
     * Add a single row of data.
     */
    public function addRow(array $row): self {
        $this->rows[] = $row;

        return $this;
    }

    /**
     * Add multiple rows of data.
     */
    public function addRows(array $rows): self {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /**
     * Clear all data but keep configuration.
     */
    public function clear(): self {
        $this->rows = [];

        return $this;
    }

    /**
     * Apply color to a specific column based on value.
     */
    public function colorizeColumn($column, $colorizer): self {
        $index = is_string($column) ? array_search($column, $this->headers) : $column;

        if ($index !== false && $index !== null) {
            if (!isset($this->columns[$index])) {
                $this->columns[$index] = new Column($this->headers[$index] ?? '');
            }

            $this->columns[$index]->setColorizer($colorizer);
        }

        return $this;
    }

    /**
     * Configure a specific column.
     */
    public function configureColumn($column, array $config): self {
        $index = is_string($column) ? array_search($column, $this->headers) : $column;

        if ($index !== false && $index !== null) {
            if (!isset($this->columns[$index])) {
                $this->columns[$index] = new Column($this->headers[$index] ?? '');
            }

            $this->columns[$index]->configure($config);
        }

        return $this;
    }

    /**
     * Create a new table builder instance.
     */
    public static function create(): self {
        return new self();
    }

    /**
     * Render and output the table directly.
     */
    public function display(): void {
        echo $this->render();
    }

    /**
     * Get column count.
     */
    public function getColumnCount(): int {
        return count($this->headers);
    }

    /**
     * Get row count.
     */
    public function getRowCount(): int {
        return count($this->rows);
    }

    /**
     * Check if table has data.
     */
    public function hasData(): bool {
        return !empty($this->rows);
    }

    /**
     * Render the table and return as string.
     */
    public function render(): string {
        $tableData = new TableData($this->headers, $this->rows);
        $renderer = new TableRenderer($this->style, $this->theme);

        return $renderer->render(
            $tableData,
            $this->columns,
            $this->maxWidth,
            $this->showHeaders,
            $this->title
        );
    }

    /**
     * Reset table to initial state.
     */
    public function reset(): self {
        $this->headers = [];
        $this->rows = [];
        $this->columns = [];
        $this->style = TableStyle::default();
        $this->theme = null;
        $this->maxWidth = $this->getTerminalWidth();
        $this->autoWidth = true;
        $this->showHeaders = true;
        $this->title = '';

        return $this;
    }

    /**
     * Enable/disable auto width calculation.
     */
    public function setAutoWidth(bool $auto): self {
        $this->autoWidth = $auto;

        if ($auto) {
            $this->maxWidth = $this->getTerminalWidth();
        }

        return $this;
    }

    /**
     * Set all data at once (headers will be array keys if associative).
     */
    public function setData(array $data): self {
        if (empty($data)) {
            return $this;
        }

        $firstRow = reset($data);

        // If associative array, use keys as headers
        if (is_array($firstRow) && !empty($firstRow)) {
            $keys = array_keys($firstRow);

            if (!is_numeric($keys[0])) {
                $this->setHeaders($keys);
            }
        }

        $this->addRows($data);

        return $this;
    }

    /**
     * Set table headers.
     */
    public function setHeaders(array $headers): self {
        $this->headers = $headers;

        // Initialize columns if not already configured
        foreach ($headers as $index => $header) {
            if (!isset($this->columns[$index])) {
                $this->columns[$index] = new Column($header);
            }
        }

        return $this;
    }

    /**
     * Set maximum table width.
     */
    public function setMaxWidth(int $width): self {
        $this->maxWidth = $width;
        $this->autoWidth = false;

        return $this;
    }

    /**
     * Set table style.
     */
    public function setStyle(TableStyle $style): self {
        $this->style = $style;

        return $this;
    }

    /**
     * Set table theme.
     */
    public function setTheme(TableTheme $theme): self {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Set table title.
     */
    public function setTitle(string $title): self {
        $this->title = $title;

        return $this;
    }

    /**
     * Show/hide table headers.
     */
    public function showHeaders(bool $show = true): self {
        $this->showHeaders = $show;

        return $this;
    }

    /**
     * Use a predefined style.
     */
    public function useStyle(string $styleName): self {
        $this->style = match (strtolower($styleName)) {
            'simple' => TableStyle::simple(),
            'bordered' => TableStyle::bordered(),
            'minimal' => TableStyle::minimal(),
            'compact' => TableStyle::compact(),
            'markdown' => TableStyle::markdown(),
            default => TableStyle::default()
        };

        return $this;
    }

    /**
     * Get terminal width.
     */
    private function getTerminalWidth(): int {
        // Try to get terminal width from environment
        $width = getenv('COLUMNS');

        if ($width !== false && is_numeric($width)) {
            return (int)$width;
        }

        // Try using tput command
        $width = exec('tput cols 2>/dev/null');

        if (is_numeric($width)) {
            return (int)$width;
        }

        // Default fallback
        return 80;
    }
}
