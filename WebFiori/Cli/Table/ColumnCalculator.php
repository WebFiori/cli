<?php
namespace WebFiori\Cli\Table;

/**
 * ColumnCalculator - Advanced width calculation algorithms for table columns.
 * 
 * This class handles intelligent column sizing, responsive width distribution,
 * and terminal width awareness for optimal table layout.
 * 
 * @author Ibrahim
 */
class ColumnCalculator {
    private const MIN_COLUMN_WIDTH = 3;

    /**
     * Auto-detect optimal column configuration based on data.
     */
    public function autoConfigureColumns(TableData $data): array {
        $columns = [];
        $headers = $data->getHeaders();
        $columnCount = $data->getColumnCount();

        for ($i = 0; $i < $columnCount; $i++) {
            $header = $headers[$i] ?? "Column ".($i + 1);
            $column = new Column($header);

            // Auto-configure based on data type

            $type = $data->getColumnType($i);
            $stats = $data->getColumnStatistics($i);

            // Set alignment based on type
            switch ($type) {
                case 'integer':
                case 'float':
                    $column->setAlignment(Column::ALIGN_RIGHT);
                    break;
                case 'date':
                    $column->setAlignment(Column::ALIGN_LEFT);
                    break;
                default:
                    $column->setAlignment(Column::ALIGN_LEFT);
            }

            // Set reasonable width constraints
            if (isset($stats['max_length'])) {
                $maxWidth = min(50, max(10, $stats['max_length'] + 2));
                $column->setMaxWidth($maxWidth);
            }

            $columns[$i] = $column;
        }

        return $columns;
    }

    /**
     * Calculate responsive column widths for narrow terminals.
     */
    public function calculateResponsiveWidths(
        TableData $data,
        array $columns,
        int $maxWidth,
        TableStyle $style
    ): array {
        // If terminal is very narrow, use stacked layout or hide less important columns
        $minRequiredWidth = $this->calculateMinimumTableWidth($columns, $style);

        if ($maxWidth < $minRequiredWidth) {
            return $this->calculateNarrowWidths($columns, $maxWidth, $style);
        }

        return $this->calculateWidths($data, $columns, $maxWidth, $style);
    }

    /**
     * Calculate optimal column widths for the table.
     */
    public function calculateWidths(
        TableData $data,
        array $columns,
        int $maxWidth,
        TableStyle $style
    ): array {
        $columnCount = count($columns);

        if ($columnCount === 0) {
            return [];
        }

        // Calculate available width for content
        $availableWidth = $this->calculateAvailableWidth($maxWidth, $columnCount, $style);

        // Get ideal widths for each column
        $idealWidths = $this->calculateIdealWidths($data, $columns);

        // Get minimum widths for each column
        $minWidths = $this->calculateMinimumWidths($data, $columns);

        // Get maximum widths for each column (from configuration)
        $maxWidths = $this->getConfiguredMaxWidths($columns);

        // Distribute available width among columns
        return $this->distributeWidth($idealWidths, $minWidths, $maxWidths, $availableWidth);
    }

    /**
     * Allocate ideal widths where possible.
     */
    private function allocateIdealWidths(
        array &$finalWidths,
        array $idealWidths,
        array $maxWidths,
        int $remainingWidth
    ): int {
        $columnCount = count($finalWidths);

        // Sort columns by their ideal width requirement (smallest first)
        $requirements = [];

        for ($i = 0; $i < $columnCount; $i++) {
            $maxAllowed = $maxWidths[$i] ? min($maxWidths[$i], $idealWidths[$i]) : $idealWidths[$i];
            $actualNeeded = max(0, $maxAllowed - $finalWidths[$i]);

            if ($actualNeeded > 0) {
                $requirements[] = ['index' => $i, 'needed' => $actualNeeded];
            }
        }

        // Sort by requirement (smallest first for fair distribution)
        usort($requirements, fn($a, $b) => $a['needed'] <=> $b['needed']);

        // Allocate width to columns that need it
        foreach ($requirements as $req) {
            $index = $req['index'];
            $needed = $req['needed'];
            $allocated = min($needed, $remainingWidth);

            $finalWidths[$index] += $allocated;
            $remainingWidth -= $allocated;

            if ($remainingWidth <= 0) {
                break;
            }
        }

        return $remainingWidth;
    }

    /**
     * Calculate available width for table content.
     */
    private function calculateAvailableWidth(int $maxWidth, int $columnCount, TableStyle $style): int {
        // Account for borders and padding
        $borderWidth = $style->getBorderWidth($columnCount);
        $paddingWidth = $columnCount * $style->getTotalPadding();

        return max(
            $columnCount * self::MIN_COLUMN_WIDTH,
            $maxWidth - $borderWidth - $paddingWidth
        );
    }

    /**
     * Calculate content width for a column's values.
     */
    private function calculateContentWidth(array $values, Column $column): int {
        $maxWidth = 0;

        foreach ($values as $value) {
            $formatted = $column->formatValue($value);
            $width = $this->getDisplayWidth($formatted);
            $maxWidth = max($maxWidth, $width);
        }

        return $maxWidth;
    }

    /**
     * Calculate ideal width for each column based on content.
     */
    private function calculateIdealWidths(TableData $data, array $columns): array {
        $idealWidths = [];
        $headers = $data->getHeaders();
        $columnIndexes = array_keys($columns);

        foreach ($columnIndexes as $index) {
            $column = $columns[$index];

            // Start with header width
            $headerWidth = strlen($headers[$index] ?? $column->getName());

            // Check content width
            $values = $data->getColumnValues($index);
            $contentWidth = $this->calculateContentWidth($values, $column);

            // Use the larger of header or content width
            $idealWidth = max($headerWidth, $contentWidth);

            // Apply column-specific width if configured
            if ($column->getWidth() !== null) {
                $idealWidth = $column->getWidth();
            }

            $idealWidths[] = $idealWidth;
        }

        return $idealWidths;
    }

    /**
     * Calculate minimum required table width.
     */
    private function calculateMinimumTableWidth(array $columns, TableStyle $style): int {
        $columnCount = count($columns);
        $minContentWidth = $columnCount * self::MIN_COLUMN_WIDTH;
        $borderWidth = $style->getBorderWidth($columnCount);
        $paddingWidth = $columnCount * $style->getTotalPadding();

        return $minContentWidth + $borderWidth + $paddingWidth;
    }

    /**
     * Calculate minimum width for each column.
     */
    private function calculateMinimumWidths(TableData $data, array $columns): array {
        $minWidths = [];
        $headers = $data->getHeaders();
        $columnIndexes = array_keys($columns);

        foreach ($columnIndexes as $index) {
            $column = $columns[$index];

            // Use configured minimum width if available
            if ($column->getMinWidth() !== null) {
                $minWidths[] = max($column->getMinWidth(), self::MIN_COLUMN_WIDTH);
                continue;
            }

            // Calculate minimum based on header and ellipsis
            $headerWidth = strlen($headers[$index] ?? $column->getName());
            $ellipsisWidth = strlen($column->getEllipsis());

            $minWidth = max(
                self::MIN_COLUMN_WIDTH,
                min($headerWidth, $ellipsisWidth + 1)
            );

            $minWidths[] = $minWidth;
        }

        return $minWidths;
    }

    /**
     * Calculate widths for narrow terminals.
     */
    private function calculateNarrowWidths(
        array $columns,
        int $maxWidth,
        TableStyle $style
    ): array {
        // Strategy: Hide less important columns or use very minimal widths
        $columnCount = count($columns);
        $availableWidth = $this->calculateAvailableWidth($maxWidth, $columnCount, $style);

        // Give each column the minimum width
        $widthPerColumn = max(self::MIN_COLUMN_WIDTH, intval($availableWidth / $columnCount));

        return array_fill(0, $columnCount, $widthPerColumn);
    }

    /**
     * Distribute any remaining width proportionally.
     */
    private function distributeRemainingWidth(
        array &$finalWidths,
        array $maxWidths,
        int $remainingWidth
    ): void {
        $columnCount = count($finalWidths);

        if ($remainingWidth <= 0) {
            return;
        }

        // Find columns that can still grow
        $growableColumns = [];
        $totalGrowthPotential = 0;

        for ($i = 0; $i < $columnCount; $i++) {
            $currentWidth = $finalWidths[$i];
            $maxAllowed = $maxWidths[$i] ?? PHP_INT_MAX;

            if ($currentWidth < $maxAllowed) {
                $growthPotential = $maxAllowed - $currentWidth;
                $growableColumns[$i] = $growthPotential;
                $totalGrowthPotential += $growthPotential;
            }
        }

        if (empty($growableColumns)) {
            return;
        }

        // Distribute proportionally based on growth potential
        foreach ($growableColumns as $index => $growthPotential) {
            $proportion = $growthPotential / $totalGrowthPotential;
            $allocation = min(
                intval($remainingWidth * $proportion),
                $growthPotential,
                $remainingWidth
            );

            $finalWidths[$index] += $allocation;
            $remainingWidth -= $allocation;

            if ($remainingWidth <= 0) {
                break;
            }
        }

        // Distribute any leftover width to the first growable columns
        while ($remainingWidth > 0 && !empty($growableColumns)) {
            foreach ($growableColumns as $index => $growthPotential) {
                if ($remainingWidth <= 0) {
                    break;
                }

                $currentWidth = $finalWidths[$index];
                $maxAllowed = $maxWidths[$index] ?? PHP_INT_MAX;

                if ($currentWidth < $maxAllowed) {
                    $finalWidths[$index]++;
                    $remainingWidth--;
                } else {
                    unset($growableColumns[$index]);
                }
            }
        }
    }

    /**
     * Distribute available width among columns using intelligent algorithm.
     */
    private function distributeWidth(
        array $idealWidths,
        array $minWidths,
        array $maxWidths,
        int $availableWidth
    ): array {
        $columnCount = count($idealWidths);
        $finalWidths = array_fill(0, $columnCount, 0);

        // Phase 1: Allocate minimum widths
        $remainingWidth = $availableWidth;

        for ($i = 0; $i < $columnCount; $i++) {
            $finalWidths[$i] = $minWidths[$i];
            $remainingWidth -= $minWidths[$i];
        }

        if ($remainingWidth <= 0) {
            return $finalWidths;
        }

        // Phase 2: Try to satisfy ideal widths
        $remainingWidth = $this->allocateIdealWidths($finalWidths, $idealWidths, $maxWidths, $remainingWidth);

        if ($remainingWidth <= 0) {
            return $finalWidths;
        }

        // Phase 3: Distribute remaining width proportionally
        $this->distributeRemainingWidth($finalWidths, $maxWidths, $remainingWidth);

        return $finalWidths;
    }

    /**
     * Get configured maximum widths for columns.
     */
    private function getConfiguredMaxWidths(array $columns): array {
        $maxWidths = [];

        foreach ($columns as $column) {
            $maxWidths[] = $column->getMaxWidth();
        }

        return $maxWidths;
    }

    /**
     * Get display width of text (accounting for ANSI codes).
     */
    private function getDisplayWidth(string $text): int {
        // Remove ANSI escape sequences for width calculation
        $cleaned = preg_replace('/\x1b\[[0-9;]*m/', '', $text);

        return strlen($cleaned ?? $text);
    }
}
