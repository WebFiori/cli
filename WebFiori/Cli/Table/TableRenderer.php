<?php
namespace WebFiori\Cli\Table;

/**
 * TableRenderer - Handles the actual rendering logic for tables.
 * 
 * This class is responsible for calculating widths, formatting content,
 * and generating the final table output with proper styling.
 * 
 * @author WebFiori Framework
 * @version 1.0.0
 */
class TableRenderer {
    private ColumnCalculator $calculator;
    private TableFormatter $formatter;

    private TableStyle $style;
    private ?TableTheme $theme;

    public function __construct(TableStyle $style, ?TableTheme $theme = null) {
        $this->style = $style;
        $this->theme = $theme;
        $this->calculator = new ColumnCalculator();
        $this->formatter = new TableFormatter();
    }

    /**
     * Get current style.
     */
    public function getStyle(): TableStyle {
        return $this->style;
    }

    /**
     * Get current theme.
     */
    public function getTheme(): ?TableTheme {
        return $this->theme;
    }

    /**
     * Render the complete table.
     */
    public function render(
        TableData $data,
        array $columns,
        int $maxWidth,
        bool $showHeaders = true,
        string $title = ''
    ): string {
        if ($data->isEmpty()) {
            return $this->renderEmptyTable($title);
        }

        // Filter visible columns
        $visibleColumns = $this->getVisibleColumns($columns, $data->getColumnCount());
        $visibleHeaders = $this->getVisibleHeaders($data->getHeaders(), $visibleColumns);
        $visibleData = $this->getVisibleData($data, $visibleColumns);

        // Calculate column widths
        $columnWidths = $this->calculator->calculateWidths(
            $visibleData,
            $visibleColumns,
            $maxWidth,
            $this->style
        );

        // Build table parts
        $output = '';

        if (!empty($title)) {
            $output .= $this->renderTitle($title, $columnWidths)."\n";
        }

        if ($this->style->showBorders) {
            $output .= $this->renderTopBorder($columnWidths)."\n";
        }

        if ($showHeaders && !empty($visibleHeaders)) {
            $output .= $this->renderHeaderRow($visibleHeaders, $visibleColumns, $columnWidths)."\n";

            if ($this->style->showHeaderSeparator) {
                $output .= $this->renderHeaderSeparator($columnWidths)."\n";
            }
        }

        $output .= $this->renderDataRows($visibleData, $visibleColumns, $columnWidths);

        if ($this->style->showBorders) {
            $output .= $this->renderBottomBorder($columnWidths);
        }

        return $output;
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
    public function setTheme(?TableTheme $theme): self {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get visible columns based on configuration.
     */
    private function getVisibleColumns(array $columns, int $totalColumns): array {
        $visible = [];

        for ($i = 0; $i < $totalColumns; $i++) {
            $column = $columns[$i] ?? new Column("Column ".($i + 1));

            if ($column->isVisible()) {
                $visible[$i] = $column;
            }
        }

        return $visible;
    }

    /**
     * Get visible data (filter out hidden columns).
     */
    private function getVisibleData(TableData $data, array $visibleColumns): TableData {
        $visibleHeaders = [];
        $visibleRows = [];
        $columnIndexes = array_keys($visibleColumns);

        // Build visible headers
        foreach ($visibleColumns as $index => $column) {
            $visibleHeaders[] = $data->getHeaders()[$index] ?? $column->getName();
        }

        // Build visible rows
        foreach ($data->getRows() as $row) {
            $visibleRow = [];

            foreach ($columnIndexes as $index) {
                $visibleRow[] = $row[$index] ?? '';
            }
            $visibleRows[] = $visibleRow;
        }

        return new TableData($visibleHeaders, $visibleRows);
    }

    /**
     * Get visible headers.
     */
    private function getVisibleHeaders(array $headers, array $visibleColumns): array {
        $visibleHeaders = [];

        foreach ($visibleColumns as $index => $column) {
            $visibleHeaders[] = $headers[$index] ?? $column->getName();
        }

        return $visibleHeaders;
    }

    /**
     * Render bottom border.
     */
    private function renderBottomBorder(array $columnWidths): string {
        if (!$this->style->showBorders) {
            return '';
        }

        $parts = [];
        $parts[] = $this->style->bottomLeft;

        foreach ($columnWidths as $index => $width) {
            $parts[] = str_repeat($this->style->horizontal, $width + $this->style->getTotalPadding());

            if ($index < count($columnWidths) - 1) {
                $parts[] = $this->style->bottomTee;
            }
        }

        $parts[] = $this->style->bottomRight;

        return implode('', $parts);
    }

    /**
     * Render data rows.
     */
    private function renderDataRows(TableData $data, array $columns, array $columnWidths): string {
        $output = '';
        $rows = $data->getRows();
        $columnIndexes = array_keys($columns);

        foreach ($rows as $rowIndex => $row) {
            $cells = [];

            foreach ($row as $cellIndex => $cellValue) {
                if (!isset($columnIndexes[$cellIndex])) {
                    continue;
                }

                $columnIndex = $columnIndexes[$cellIndex];
                $column = $columns[$columnIndex];
                $width = $columnWidths[$cellIndex];

                // Format cell value
                $formattedValue = $column->formatValue($cellValue);

                // Apply colorization
                $colorizedValue = $column->colorizeValue($formattedValue);

                // Apply theme colors if available
                if ($this->theme) {
                    $colorizedValue = $this->theme->applyCellStyle($colorizedValue, $rowIndex, $cellIndex);
                }

                // Truncate and align
                $truncated = $column->truncateText($colorizedValue, $width);
                $aligned = $column->alignText($truncated, $width);

                $cells[] = $aligned;
            }

            $output .= $this->renderRow($cells)."\n";

            // Add row separator if enabled
            if ($this->style->showRowSeparators && $rowIndex < count($rows) - 1) {
                $output .= $this->renderHeaderSeparator($columnWidths)."\n";
            }
        }

        return $output;
    }

    /**
     * Render empty table message.
     */
    private function renderEmptyTable(string $title): string {
        $message = 'No data to display';

        if (!empty($title)) {
            $message = $title."\n".str_repeat('=', strlen($title))."\n\n".$message;
        }

        return $message;
    }

    /**
     * Render header row.
     */
    private function renderHeaderRow(array $headers, array $columns, array $columnWidths): string {
        $cells = [];
        $columnIndexes = array_keys($columns);

        foreach ($headers as $index => $header) {
            $columnIndex = $columnIndexes[$index];
            $column = $columns[$columnIndex];
            $width = $columnWidths[$index];

            // Format header text
            $formattedHeader = $this->formatter->formatHeader($header);

            // Apply theme colors if available
            if ($this->theme) {
                $formattedHeader = $this->theme->applyHeaderStyle($formattedHeader);
            }

            // Truncate and align
            $truncated = $column->truncateText($formattedHeader, $width);
            $aligned = $column->alignText($truncated, $width);

            $cells[] = $aligned;
        }

        return $this->renderRow($cells);
    }

    /**
     * Render header separator.
     */
    private function renderHeaderSeparator(array $columnWidths): string {
        if ($this->style->showBorders) {
            $parts = [];
            $parts[] = $this->style->leftTee;

            foreach ($columnWidths as $index => $width) {
                $parts[] = str_repeat($this->style->horizontal, $width + $this->style->getTotalPadding());

                if ($index < count($columnWidths) - 1) {
                    $parts[] = $this->style->cross;
                }
            }

            $parts[] = $this->style->rightTee;

            return implode('', $parts);
        } else {
            // Simple horizontal line for minimal styles
            $totalWidth = array_sum($columnWidths) + (count($columnWidths) - 1) * 2; // 2 spaces between columns

            return str_repeat($this->style->horizontal, $totalWidth);
        }
    }

    /**
     * Render a single row with cells.
     */
    private function renderRow(array $cells): string {
        $parts = [];

        if ($this->style->showBorders) {
            $parts[] = $this->style->vertical;
        }

        foreach ($cells as $index => $cell) {
            $parts[] = str_repeat(' ', $this->style->paddingLeft);
            $parts[] = $cell;
            $parts[] = str_repeat(' ', $this->style->paddingRight);

            if ($index < count($cells) - 1) {
                $parts[] = $this->style->vertical;
            }
        }

        if ($this->style->showBorders) {
            $parts[] = $this->style->vertical;
        }

        return implode('', $parts);
    }

    /**
     * Render table title.
     */
    private function renderTitle(string $title, array $columnWidths): string {
        $totalWidth = array_sum($columnWidths) + $this->style->getBorderWidth(count($columnWidths)) + 
                     (count($columnWidths) * $this->style->getTotalPadding());

        $titleLength = strlen($title);

        if ($titleLength >= $totalWidth) {
            return $title;
        }

        $padding = $totalWidth - $titleLength;
        $leftPadding = intval($padding / 2);
        $rightPadding = $padding - $leftPadding;

        return str_repeat(' ', $leftPadding).$title.str_repeat(' ', $rightPadding);
    }

    /**
     * Render top border.
     */
    private function renderTopBorder(array $columnWidths): string {
        if (!$this->style->showBorders) {
            return '';
        }

        $parts = [];
        $parts[] = $this->style->topLeft;

        foreach ($columnWidths as $index => $width) {
            $parts[] = str_repeat($this->style->horizontal, $width + $this->style->getTotalPadding());

            if ($index < count($columnWidths) - 1) {
                $parts[] = $this->style->topTee;
            }
        }

        $parts[] = $this->style->topRight;

        return implode('', $parts);
    }
}
