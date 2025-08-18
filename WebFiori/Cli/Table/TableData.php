<?php

namespace WebFiori\Cli\Table;

/**
 * TableData - Data container and processor for table content.
 * 
 * This class handles data validation, type detection, and content
 * processing for table rendering.
 * 
 * @author WebFiori Framework
 * @version 1.0.0
 */
class TableData {
    
    private array $headers;
    private array $rows;
    private array $columnTypes = [];
    private array $statistics = [];
    
    public function __construct(array $headers, array $rows) {
        $this->headers = $headers;
        $this->rows = $this->normalizeRows($rows);
        $this->analyzeData();
    }
    
    /**
     * Get table headers.
     */
    public function getHeaders(): array {
        return $this->headers;
    }
    
    /**
     * Get table rows.
     */
    public function getRows(): array {
        return $this->rows;
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
     * Get values for a specific column.
     */
    public function getColumnValues(int $columnIndex): array {
        $values = [];
        
        foreach ($this->rows as $row) {
            $values[] = $row[$columnIndex] ?? '';
        }
        
        return $values;
    }
    
    /**
     * Get detected type for a column.
     */
    public function getColumnType(int $columnIndex): string {
        return $this->columnTypes[$columnIndex] ?? 'string';
    }
    
    /**
     * Get all column types.
     */
    public function getColumnTypes(): array {
        return $this->columnTypes;
    }
    
    /**
     * Get statistics for a column.
     */
    public function getColumnStatistics(int $columnIndex): array {
        return $this->statistics[$columnIndex] ?? [];
    }
    
    /**
     * Get all statistics.
     */
    public function getAllStatistics(): array {
        return $this->statistics;
    }
    
    /**
     * Check if table has data.
     */
    public function hasData(): bool {
        return !empty($this->rows);
    }
    
    /**
     * Check if table is empty.
     */
    public function isEmpty(): bool {
        return empty($this->rows);
    }
    
    /**
     * Get a specific cell value.
     */
    public function getCellValue(int $rowIndex, int $columnIndex): mixed {
        return $this->rows[$rowIndex][$columnIndex] ?? null;
    }
    
    /**
     * Get a specific row.
     */
    public function getRow(int $rowIndex): array {
        return $this->rows[$rowIndex] ?? [];
    }
    
    /**
     * Filter rows based on a condition.
     */
    public function filterRows(callable $condition): self {
        $filteredRows = array_filter($this->rows, $condition);
        return new self($this->headers, array_values($filteredRows));
    }
    
    /**
     * Sort rows by a specific column.
     */
    public function sortByColumn(int $columnIndex, bool $ascending = true): self {
        $sortedRows = $this->rows;
        
        usort($sortedRows, function($a, $b) use ($columnIndex, $ascending) {
            $valueA = $a[$columnIndex] ?? '';
            $valueB = $b[$columnIndex] ?? '';
            
            // Handle numeric comparison
            if (is_numeric($valueA) && is_numeric($valueB)) {
                $result = $valueA <=> $valueB;
            } else {
                $result = strcasecmp((string)$valueA, (string)$valueB);
            }
            
            return $ascending ? $result : -$result;
        });
        
        return new self($this->headers, $sortedRows);
    }
    
    /**
     * Limit the number of rows.
     */
    public function limit(int $count, int $offset = 0): self {
        $limitedRows = array_slice($this->rows, $offset, $count);
        return new self($this->headers, $limitedRows);
    }
    
    /**
     * Add a new row.
     */
    public function addRow(array $row): self {
        $normalizedRow = $this->normalizeRow($row);
        $newRows = $this->rows;
        $newRows[] = $normalizedRow;
        
        return new self($this->headers, $newRows);
    }
    
    /**
     * Remove a row by index.
     */
    public function removeRow(int $index): self {
        $newRows = $this->rows;
        unset($newRows[$index]);
        
        return new self($this->headers, array_values($newRows));
    }
    
    /**
     * Transform data using a callback.
     */
    public function transform(callable $transformer): self {
        $transformedRows = array_map($transformer, $this->rows);
        return new self($this->headers, $transformedRows);
    }
    
    /**
     * Get unique values for a column.
     */
    public function getUniqueValues(int $columnIndex): array {
        $values = $this->getColumnValues($columnIndex);
        return array_unique($values);
    }
    
    /**
     * Count occurrences of values in a column.
     */
    public function getValueCounts(int $columnIndex): array {
        $values = $this->getColumnValues($columnIndex);
        return array_count_values(array_map('strval', $values));
    }
    
    /**
     * Export data to array format.
     */
    public function toArray(bool $includeHeaders = true): array {
        if ($includeHeaders) {
            return array_merge([$this->headers], $this->rows);
        }
        
        return $this->rows;
    }
    
    /**
     * Export data to associative array format.
     */
    public function toAssociativeArray(): array {
        $result = [];
        
        foreach ($this->rows as $row) {
            $assocRow = [];
            foreach ($this->headers as $index => $header) {
                $assocRow[$header] = $row[$index] ?? null;
            }
            $result[] = $assocRow;
        }
        
        return $result;
    }
    
    /**
     * Export data to JSON.
     */
    public function toJson(bool $prettyPrint = false): string {
        $data = $this->toAssociativeArray();
        $flags = $prettyPrint ? JSON_PRETTY_PRINT : 0;
        
        return json_encode($data, $flags);
    }
    
    /**
     * Export data to CSV format.
     */
    public function toCsv(bool $includeHeaders = true, string $delimiter = ','): string {
        $output = '';
        
        if ($includeHeaders) {
            $output .= implode($delimiter, array_map([$this, 'escapeCsvValue'], $this->headers)) . "\n";
        }
        
        foreach ($this->rows as $row) {
            $output .= implode($delimiter, array_map([$this, 'escapeCsvValue'], $row)) . "\n";
        }
        
        return $output;
    }
    
    /**
     * Create TableData from various input formats.
     */
    public static function fromArray(array $data, ?array $headers = null): self {
        if (empty($data)) {
            return new self($headers ?? [], []);
        }
        
        $firstRow = reset($data);
        
        // If no headers provided and first row is associative, use keys as headers
        if ($headers === null && is_array($firstRow) && !empty($firstRow)) {
            $keys = array_keys($firstRow);
            if (!is_numeric($keys[0])) {
                $headers = $keys;
            }
        }
        
        // Default headers if still not set
        if ($headers === null) {
            $maxColumns = 0;
            foreach ($data as $row) {
                if (is_array($row)) {
                    $maxColumns = max($maxColumns, count($row));
                }
            }
            
            $headers = [];
            for ($i = 0; $i < $maxColumns; $i++) {
                $headers[] = "Column " . ($i + 1);
            }
        }
        
        return new self($headers, $data);
    }
    
    /**
     * Create TableData from JSON.
     */
    public static function fromJson(string $json, ?array $headers = null): self {
        $data = json_decode($json, true);
        
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON data for table');
        }
        
        return self::fromArray($data, $headers);
    }
    
    /**
     * Create TableData from CSV.
     */
    public static function fromCsv(string $csv, bool $hasHeaders = true, string $delimiter = ','): self {
        $lines = explode("\n", trim($csv));
        $data = [];
        $headers = null;
        
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            
            $row = str_getcsv($line, $delimiter);
            
            if ($hasHeaders && $headers === null) {
                $headers = $row;
            } else {
                $data[] = $row;
            }
        }
        
        return new self($headers ?? [], $data);
    }
    
    /**
     * Normalize rows to ensure consistent structure.
     */
    private function normalizeRows(array $rows): array {
        $normalized = [];
        $columnCount = count($this->headers);
        
        foreach ($rows as $row) {
            $normalized[] = $this->normalizeRow($row, $columnCount);
        }
        
        return $normalized;
    }
    
    /**
     * Normalize a single row.
     */
    private function normalizeRow(array $row, ?int $expectedColumns = null): array {
        $expectedColumns = $expectedColumns ?? count($this->headers);
        
        // If associative array, convert to indexed based on headers
        if (!empty($row) && !is_numeric(array_keys($row)[0])) {
            $normalizedRow = [];
            foreach ($this->headers as $header) {
                $normalizedRow[] = $row[$header] ?? '';
            }
            $row = $normalizedRow;
        }
        
        // Pad or trim to match expected column count
        if (count($row) < $expectedColumns) {
            $row = array_pad($row, $expectedColumns, '');
        } elseif (count($row) > $expectedColumns) {
            $row = array_slice($row, 0, $expectedColumns);
        }
        
        return $row;
    }
    
    /**
     * Analyze data to detect types and calculate statistics.
     */
    private function analyzeData(): void {
        $columnCount = $this->getColumnCount();
        
        for ($i = 0; $i < $columnCount; $i++) {
            $values = $this->getColumnValues($i);
            $this->columnTypes[$i] = $this->detectColumnType($values);
            $this->statistics[$i] = $this->calculateColumnStatistics($values, $this->columnTypes[$i]);
        }
    }
    
    /**
     * Detect the type of a column based on its values.
     */
    private function detectColumnType(array $values): string {
        $types = ['integer' => 0, 'float' => 0, 'date' => 0, 'boolean' => 0, 'string' => 0];
        $totalValues = 0;
        
        foreach ($values as $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            
            $totalValues++;
            
            // Check for integer
            if (is_int($value) || (is_string($value) && ctype_digit(trim($value)))) {
                $types['integer']++;
                continue;
            }
            
            // Check for float
            if (is_float($value) || (is_string($value) && is_numeric(trim($value)))) {
                $types['float']++;
                continue;
            }
            
            // Check for boolean
            if (is_bool($value) || in_array(strtolower(trim((string)$value)), ['true', 'false', '1', '0', 'yes', 'no'])) {
                $types['boolean']++;
                continue;
            }
            
            // Check for date
            if (is_string($value) && $this->isDateString($value)) {
                $types['date']++;
                continue;
            }
            
            // Default to string
            $types['string']++;
        }
        
        if ($totalValues === 0) {
            return 'string';
        }
        
        // Return the type with the highest percentage (>= 80%)
        arsort($types);
        $topType = array_key_first($types);
        $percentage = $types[$topType] / $totalValues;
        
        return $percentage >= 0.8 ? $topType : 'string';
    }
    
    /**
     * Calculate statistics for a column.
     */
    private function calculateColumnStatistics(array $values, string $type): array {
        $stats = [
            'count' => count($values),
            'non_empty' => 0,
            'unique' => 0,
            'type' => $type
        ];
        
        $nonEmptyValues = array_filter($values, fn($v) => $v !== '' && $v !== null);
        $stats['non_empty'] = count($nonEmptyValues);
        $stats['unique'] = count(array_unique($nonEmptyValues));
        
        if (empty($nonEmptyValues)) {
            return $stats;
        }
        
        // Type-specific statistics
        if (in_array($type, ['integer', 'float'])) {
            $numericValues = array_map('floatval', $nonEmptyValues);
            $stats['min'] = min($numericValues);
            $stats['max'] = max($numericValues);
            $stats['avg'] = array_sum($numericValues) / count($numericValues);
            $stats['sum'] = array_sum($numericValues);
        }
        
        if ($type === 'string') {
            $lengths = array_map('strlen', array_map('strval', $nonEmptyValues));
            $stats['min_length'] = min($lengths);
            $stats['max_length'] = max($lengths);
            $stats['avg_length'] = array_sum($lengths) / count($lengths);
        }
        
        return $stats;
    }
    
    /**
     * Check if a string represents a date.
     */
    private function isDateString(string $value): bool {
        $dateFormats = [
            'Y-m-d', 'Y-m-d H:i:s', 'Y/m/d', 'Y/m/d H:i:s',
            'd-m-Y', 'd-m-Y H:i:s', 'd/m/Y', 'd/m/Y H:i:s',
            'm-d-Y', 'm-d-Y H:i:s', 'm/d/Y', 'm/d/Y H:i:s'
        ];
        
        foreach ($dateFormats as $format) {
            $date = \DateTime::createFromFormat($format, trim($value));
            if ($date && $date->format($format) === trim($value)) {
                return true;
            }
        }
        
        // Try strtotime as fallback
        return strtotime($value) !== false;
    }
    
    /**
     * Escape a value for CSV output.
     */
    private function escapeCsvValue(mixed $value): string {
        $value = (string)$value;
        
        // If value contains comma, quote, or newline, wrap in quotes and escape quotes
        if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false) {
            $value = '"' . str_replace('"', '""', $value) . '"';
        }
        
        return $value;
    }
}
