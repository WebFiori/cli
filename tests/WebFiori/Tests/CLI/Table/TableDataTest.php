<?php

namespace tests\WebFiori\CLI\Table;

use PHPUnit\Framework\TestCase;
use WebFiori\CLI\Table\TableData;

/**
 * Unit tests for TableData class.
 * 
 * Tests data container functionality, type detection,
 * statistics calculation, and export capabilities.
 */
class TableDataTest extends TestCase {
    
    private TableData $tableData;
    private array $sampleHeaders;
    private array $sampleRows;
    
    protected function setUp(): void {
        require_once __DIR__ . '/../../../../WebFiori/Cli/Table/TableData.php';
        
        $this->sampleHeaders = ['Name', 'Age', 'City', 'Active'];
        $this->sampleRows = [
            ['John Doe', 30, 'New York', true],
            ['Jane Smith', 25, 'Los Angeles', false],
            ['Bob Johnson', 35, 'Chicago', true]
        ];
        
        $this->tableData = new TableData($this->sampleHeaders, $this->sampleRows);
    }
    
    /**
     * @test
     */
    public function testConstructor() {
        $data = new TableData(['A', 'B'], [['1', '2']]);
        
        $this->assertEquals(['A', 'B'], $data->getHeaders());
        $this->assertEquals([['1', '2']], $data->getRows());
        $this->assertEquals(2, $data->getColumnCount());
        $this->assertEquals(1, $data->getRowCount());
    }
    
    /**
     * @test
     */
    public function testGetHeaders() {
        $this->assertEquals($this->sampleHeaders, $this->tableData->getHeaders());
    }
    
    /**
     * @test
     */
    public function testGetRows() {
        $this->assertEquals($this->sampleRows, $this->tableData->getRows());
    }
    
    /**
     * @test
     */
    public function testGetColumnCount() {
        $this->assertEquals(4, $this->tableData->getColumnCount());
    }
    
    /**
     * @test
     */
    public function testGetRowCount() {
        $this->assertEquals(3, $this->tableData->getRowCount());
    }
    
    /**
     * @test
     */
    public function testGetColumnValues() {
        $nameValues = $this->tableData->getColumnValues(0);
        $expectedNames = ['John Doe', 'Jane Smith', 'Bob Johnson'];
        
        $this->assertEquals($expectedNames, $nameValues);
    }
    
    /**
     * @test
     */
    public function testGetColumnValuesInvalidIndex() {
        $values = $this->tableData->getColumnValues(10);
        
        $this->assertEquals(['', '', ''], $values);
    }
    
    /**
     * @test
     */
    public function testGetColumnType() {
        // Age column should be detected as integer
        $this->assertEquals('integer', $this->tableData->getColumnType(1));
        
        // Name column should be detected as string
        $this->assertEquals('string', $this->tableData->getColumnType(0));
    }
    
    /**
     * @test
     */
    public function testGetColumnStatistics() {
        $ageStats = $this->tableData->getColumnStatistics(1);
        
        $this->assertIsArray($ageStats);
        $this->assertEquals(3, $ageStats['count']);
        $this->assertEquals(3, $ageStats['non_empty']);
        $this->assertEquals(3, $ageStats['unique']);
        $this->assertEquals('integer', $ageStats['type']);
        $this->assertEquals(25, $ageStats['min']);
        $this->assertEquals(35, $ageStats['max']);
        $this->assertEquals(30, $ageStats['avg']);
    }
    
    /**
     * @test
     */
    public function testHasData() {
        $this->assertTrue($this->tableData->hasData());
        
        $emptyData = new TableData(['A'], []);
        $this->assertFalse($emptyData->hasData());
    }
    
    /**
     * @test
     */
    public function testIsEmpty() {
        $this->assertFalse($this->tableData->isEmpty());
        
        $emptyData = new TableData(['A'], []);
        $this->assertTrue($emptyData->isEmpty());
    }
    
    /**
     * @test
     */
    public function testGetCellValue() {
        $this->assertEquals('John Doe', $this->tableData->getCellValue(0, 0));
        $this->assertEquals(30, $this->tableData->getCellValue(0, 1));
        $this->assertNull($this->tableData->getCellValue(10, 0)); // Invalid row
        $this->assertNull($this->tableData->getCellValue(0, 10)); // Invalid column
    }
    
    /**
     * @test
     */
    public function testGetRow() {
        $firstRow = $this->tableData->getRow(0);
        $this->assertEquals($this->sampleRows[0], $firstRow);
        
        $invalidRow = $this->tableData->getRow(10);
        $this->assertEquals([], $invalidRow);
    }
    
    /**
     * @test
     */
    public function testFilterRows() {
        $filtered = $this->tableData->filterRows(function($row) {
            return $row[1] > 30; // Age > 30
        });
        
        $this->assertInstanceOf(TableData::class, $filtered);
        $this->assertEquals(1, $filtered->getRowCount()); // Only Bob Johnson
        $this->assertEquals('Bob Johnson', $filtered->getCellValue(0, 0));
    }
    
    /**
     * @test
     */
    public function testSortByColumn() {
        $sorted = $this->tableData->sortByColumn(1, true); // Sort by age ascending
        
        $this->assertInstanceOf(TableData::class, $sorted);
        $this->assertEquals('Jane Smith', $sorted->getCellValue(0, 0)); // Age 25
        $this->assertEquals('John Doe', $sorted->getCellValue(1, 0)); // Age 30
        $this->assertEquals('Bob Johnson', $sorted->getCellValue(2, 0)); // Age 35
    }
    
    /**
     * @test
     */
    public function testSortByColumnDescending() {
        $sorted = $this->tableData->sortByColumn(1, false); // Sort by age descending
        
        $this->assertEquals('Bob Johnson', $sorted->getCellValue(0, 0)); // Age 35
        $this->assertEquals('John Doe', $sorted->getCellValue(1, 0)); // Age 30
        $this->assertEquals('Jane Smith', $sorted->getCellValue(2, 0)); // Age 25
    }
    
    /**
     * @test
     */
    public function testLimit() {
        $limited = $this->tableData->limit(2);
        
        $this->assertInstanceOf(TableData::class, $limited);
        $this->assertEquals(2, $limited->getRowCount());
        $this->assertEquals('John Doe', $limited->getCellValue(0, 0));
        $this->assertEquals('Jane Smith', $limited->getCellValue(1, 0));
    }
    
    /**
     * @test
     */
    public function testLimitWithOffset() {
        $limited = $this->tableData->limit(1, 1);
        
        $this->assertEquals(1, $limited->getRowCount());
        $this->assertEquals('Jane Smith', $limited->getCellValue(0, 0));
    }
    
    /**
     * @test
     */
    public function testAddRow() {
        $newData = $this->tableData->addRow(['Alice Brown', 28, 'Boston', true]);
        
        $this->assertInstanceOf(TableData::class, $newData);
        $this->assertEquals(4, $newData->getRowCount());
        $this->assertEquals('Alice Brown', $newData->getCellValue(3, 0));
    }
    
    /**
     * @test
     */
    public function testRemoveRow() {
        $newData = $this->tableData->removeRow(1); // Remove Jane Smith
        
        $this->assertInstanceOf(TableData::class, $newData);
        $this->assertEquals(2, $newData->getRowCount());
        $this->assertEquals('John Doe', $newData->getCellValue(0, 0));
        $this->assertEquals('Bob Johnson', $newData->getCellValue(1, 0));
    }
    
    /**
     * @test
     */
    public function testTransform() {
        $transformed = $this->tableData->transform(function($row) {
            $row[0] = strtoupper($row[0]); // Uppercase names
            return $row;
        });
        
        $this->assertInstanceOf(TableData::class, $transformed);
        $this->assertEquals('JOHN DOE', $transformed->getCellValue(0, 0));
        $this->assertEquals('JANE SMITH', $transformed->getCellValue(1, 0));
    }
    
    /**
     * @test
     */
    public function testGetUniqueValues() {
        $data = new TableData(['Status'], [['Active'], ['Inactive'], ['Active'], ['Pending']]);
        $unique = $data->getUniqueValues(0);
        
        $this->assertCount(3, $unique);
        $this->assertContains('Active', $unique);
        $this->assertContains('Inactive', $unique);
        $this->assertContains('Pending', $unique);
    }
    
    /**
     * @test
     */
    public function testGetValueCounts() {
        $data = new TableData(['Status'], [['Active'], ['Inactive'], ['Active'], ['Pending']]);
        $counts = $data->getValueCounts(0);
        
        $this->assertEquals(2, $counts['Active']);
        $this->assertEquals(1, $counts['Inactive']);
        $this->assertEquals(1, $counts['Pending']);
    }
    
    /**
     * @test
     */
    public function testToArray() {
        $array = $this->tableData->toArray(true);
        
        $this->assertIsArray($array);
        $this->assertEquals($this->sampleHeaders, $array[0]);
        $this->assertEquals($this->sampleRows[0], $array[1]);
        $this->assertCount(4, $array); // 3 rows + 1 header
    }
    
    /**
     * @test
     */
    public function testToArrayWithoutHeaders() {
        $array = $this->tableData->toArray(false);
        
        $this->assertIsArray($array);
        $this->assertEquals($this->sampleRows, $array);
        $this->assertCount(3, $array); // Only rows
    }
    
    /**
     * @test
     */
    public function testToAssociativeArray() {
        $assoc = $this->tableData->toAssociativeArray();
        
        $this->assertIsArray($assoc);
        $this->assertCount(3, $assoc);
        $this->assertEquals('John Doe', $assoc[0]['Name']);
        $this->assertEquals(30, $assoc[0]['Age']);
        $this->assertEquals('New York', $assoc[0]['City']);
    }
    
    /**
     * @test
     */
    public function testToJson() {
        $json = $this->tableData->toJson();
        
        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
        $this->assertCount(3, $decoded);
        $this->assertEquals('John Doe', $decoded[0]['Name']);
    }
    
    /**
     * @test
     */
    public function testToJsonPrettyPrint() {
        $json = $this->tableData->toJson(true);
        
        $this->assertIsString($json);
        $this->assertStringContainsString("\n", $json); // Pretty printed
        $this->assertStringContainsString("    ", $json); // Indentation
    }
    
    /**
     * @test
     */
    public function testToCsv() {
        $csv = $this->tableData->toCsv(true);
        
        $this->assertIsString($csv);
        $this->assertStringContainsString('Name,Age,City,Active', $csv);
        $this->assertStringContainsString('John Doe,30,New York,1', $csv);
    }
    
    /**
     * @test
     */
    public function testToCsvWithoutHeaders() {
        $csv = $this->tableData->toCsv(false);
        
        $this->assertIsString($csv);
        $this->assertStringNotContainsString('Name,Age,City,Active', $csv);
        $this->assertStringContainsString('John Doe,30,New York,1', $csv);
    }
    
    /**
     * @test
     */
    public function testFromArray() {
        $data = [
            ['John', 30],
            ['Jane', 25]
        ];
        
        $tableData = TableData::fromArray($data, ['Name', 'Age']);
        
        $this->assertInstanceOf(TableData::class, $tableData);
        $this->assertEquals(['Name', 'Age'], $tableData->getHeaders());
        $this->assertEquals(2, $tableData->getRowCount());
    }
    
    /**
     * @test
     */
    public function testFromArrayWithAssociativeData() {
        $data = [
            ['name' => 'John', 'age' => 30],
            ['name' => 'Jane', 'age' => 25]
        ];
        
        $tableData = TableData::fromArray($data);
        
        $this->assertEquals(['name', 'age'], $tableData->getHeaders());
        $this->assertEquals(2, $tableData->getRowCount());
    }
    
    /**
     * @test
     */
    public function testFromJson() {
        $json = '[{"name":"John","age":30},{"name":"Jane","age":25}]';
        
        $tableData = TableData::fromJson($json);
        
        $this->assertInstanceOf(TableData::class, $tableData);
        $this->assertEquals(['name', 'age'], $tableData->getHeaders());
        $this->assertEquals(2, $tableData->getRowCount());
    }
    
    /**
     * @test
     */
    public function testFromJsonInvalid() {
        $this->expectException(\InvalidArgumentException::class);
        
        TableData::fromJson('invalid json');
    }
    
    /**
     * @test
     */
    public function testFromCsv() {
        $csv = "Name,Age\nJohn,30\nJane,25";
        
        $tableData = TableData::fromCsv($csv, true);
        
        $this->assertInstanceOf(TableData::class, $tableData);
        $this->assertEquals(['Name', 'Age'], $tableData->getHeaders());
        $this->assertEquals(2, $tableData->getRowCount());
        $this->assertEquals('John', $tableData->getCellValue(0, 0));
    }
    
    /**
     * @test
     */
    public function testFromCsvWithoutHeaders() {
        $csv = "John,30\nJane,25";
        
        $tableData = TableData::fromCsv($csv, false);
        
        $this->assertEquals([], $tableData->getHeaders());
        $this->assertEquals(2, $tableData->getRowCount());
    }
    
    /**
     * @test
     */
    public function testNormalizeRowsWithMismatchedColumns() {
        $headers = ['A', 'B', 'C'];
        $rows = [
            ['1', '2'], // Missing column
            ['1', '2', '3', '4'] // Extra column
        ];
        
        $tableData = new TableData($headers, $rows);
        
        $this->assertEquals(['1', '2', ''], $tableData->getRow(0));
        $this->assertEquals(['1', '2', '3'], $tableData->getRow(1));
    }
    
    /**
     * @test
     */
    public function testTypeDetection() {
        $data = new TableData(
            ['Integer', 'Float', 'String', 'Boolean'],
            [
                [1, 1.5, 'text', true],
                [2, 2.7, 'more text', false],
                [3, 3.14, 'even more', true]
            ]
        );
        
        $this->assertEquals('integer', $data->getColumnType(0));
        $this->assertEquals('float', $data->getColumnType(1));
        $this->assertEquals('string', $data->getColumnType(2));
        $this->assertEquals('boolean', $data->getColumnType(3));
    }
}
