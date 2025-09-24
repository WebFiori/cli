<?php

namespace tests\WebFiori\CLI\Table;

use PHPUnit\Framework\TestCase;
use WebFiori\CLI\Table\TableBuilder;
use WebFiori\CLI\Table\TableStyle;
use WebFiori\CLI\Table\TableTheme;
use WebFiori\CLI\Table\Column;

/**
 * Unit tests for TableBuilder class.
 * 
 * Tests the main interface for creating and configuring tables,
 * including fluent interface methods, data management, and rendering.
 */
class TableBuilderTest extends TestCase {
    
    private TableBuilder $table;
    
    protected function setUp(): void {
        // Include required classes
        require_once __DIR__ . '/../../../../../WebFiori/CLI/Table/TableStyle.php';
        require_once __DIR__ . '/../../../../../WebFiori/CLI/Table/Column.php';
        require_once __DIR__ . '/../../../../../WebFiori/CLI/Table/TableData.php';
        require_once __DIR__ . '/../../../../../WebFiori/CLI/Table/ColumnCalculator.php';
        require_once __DIR__ . '/../../../../../WebFiori/CLI/Table/TableFormatter.php';
        require_once __DIR__ . '/../../../../../WebFiori/CLI/Table/TableTheme.php';
        require_once __DIR__ . '/../../../../../WebFiori/CLI/Table/TableRenderer.php';
        require_once __DIR__ . '/../../../../../WebFiori/CLI/Table/TableBuilder.php';
        
        $this->table = new TableBuilder();
    }
    
    /**
     * @test
     */
    public function testCreateStaticMethod() {
        $table = TableBuilder::create();
        $this->assertInstanceOf(TableBuilder::class, $table);
    }
    
    /**
     * @test
     */
    public function testSetHeaders() {
        $headers = ['Name', 'Age', 'City'];
        $result = $this->table->setHeaders($headers);
        
        $this->assertSame($this->table, $result); // Fluent interface
        $this->assertEquals(3, $this->table->getColumnCount());
    }
    
    /**
     * @test
     */
    public function testAddRow() {
        $this->table->setHeaders(['Name', 'Age']);
        $result = $this->table->addRow(['John', 30]);
        
        $this->assertSame($this->table, $result); // Fluent interface
        $this->assertEquals(1, $this->table->getRowCount());
    }
    
    /**
     * @test
     */
    public function testAddRows() {
        $this->table->setHeaders(['Name', 'Age']);
        $rows = [
            ['John', 30],
            ['Jane', 25],
            ['Bob', 35]
        ];
        
        $result = $this->table->addRows($rows);
        
        $this->assertSame($this->table, $result); // Fluent interface
        $this->assertEquals(3, $this->table->getRowCount());
    }
    
    /**
     * @test
     */
    public function testSetDataWithIndexedArray() {
        $data = [
            ['John', 30, 'New York'],
            ['Jane', 25, 'Los Angeles']
        ];
        
        $this->table->setHeaders(['Name', 'Age', 'City']);
        $result = $this->table->setData($data);
        
        $this->assertSame($this->table, $result);
        $this->assertEquals(2, $this->table->getRowCount());
    }
    
    /**
     * @test
     */
    public function testSetDataWithAssociativeArray() {
        $data = [
            ['name' => 'John', 'age' => 30, 'city' => 'New York'],
            ['name' => 'Jane', 'age' => 25, 'city' => 'Los Angeles']
        ];
        
        $result = $this->table->setData($data);
        
        $this->assertSame($this->table, $result);
        $this->assertEquals(3, $this->table->getColumnCount());
        $this->assertEquals(2, $this->table->getRowCount());
    }
    
    /**
     * @test
     */
    public function testConfigureColumnByName() {
        $this->table->setHeaders(['Name', 'Age', 'City']);
        
        $result = $this->table->configureColumn('Name', [
            'width' => 20,
            'align' => 'left'
        ]);
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testConfigureColumnByIndex() {
        $this->table->setHeaders(['Name', 'Age', 'City']);
        
        $result = $this->table->configureColumn(1, [
            'width' => 10,
            'align' => 'right'
        ]);
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testSetStyle() {
        $style = TableStyle::simple();
        $result = $this->table->setStyle($style);
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testUseStyle() {
        $result = $this->table->useStyle('simple');
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testUseStyleWithInvalidName() {
        $result = $this->table->useStyle('invalid');
        
        $this->assertSame($this->table, $result); // Should fallback to default
    }
    
    /**
     * @test
     */
    public function testSetTheme() {
        $theme = TableTheme::dark();
        $result = $this->table->setTheme($theme);
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testSetMaxWidth() {
        $result = $this->table->setMaxWidth(100);
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testSetAutoWidth() {
        $result = $this->table->setAutoWidth(false);
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testShowHeaders() {
        $result = $this->table->showHeaders(false);
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testSetTitle() {
        $result = $this->table->setTitle('Test Table');
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testColorizeColumn() {
        $this->table->setHeaders(['Name', 'Status']);
        
        $colorizer = function($value) {
            return ['color' => 'green'];
        };
        
        $result = $this->table->colorizeColumn('Status', $colorizer);
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testHasData() {
        $this->assertFalse($this->table->hasData());
        
        $this->table->setHeaders(['Name']);
        $this->table->addRow(['John']);
        
        $this->assertTrue($this->table->hasData());
    }
    
    /**
     * @test
     */
    public function testClear() {
        $this->table->setHeaders(['Name']);
        $this->table->addRow(['John']);
        
        $this->assertTrue($this->table->hasData());
        
        $result = $this->table->clear();
        
        $this->assertSame($this->table, $result);
        $this->assertFalse($this->table->hasData());
        $this->assertEquals(1, $this->table->getColumnCount()); // Headers preserved
    }
    
    /**
     * @test
     */
    public function testReset() {
        $this->table->setHeaders(['Name']);
        $this->table->addRow(['John']);
        $this->table->setTitle('Test');
        
        $result = $this->table->reset();
        
        $this->assertSame($this->table, $result);
        $this->assertFalse($this->table->hasData());
        $this->assertEquals(0, $this->table->getColumnCount());
    }
    
    /**
     * @test
     */
    public function testRenderEmptyTable() {
        $output = $this->table->render();
        
        $this->assertIsString($output);
        $this->assertStringContainsString('No data to display', $output);
    }
    
    /**
     * @test
     */
    public function testRenderWithData() {
        $this->table
            ->setHeaders(['Name', 'Age'])
            ->addRow(['John', 30])
            ->addRow(['Jane', 25]);
        
        $output = $this->table->render();
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('Age', $output);
        $this->assertStringContainsString('John', $output);
        $this->assertStringContainsString('Jane', $output);
    }
    
    /**
     * @test
     */
    public function testRenderWithTitle() {
        $this->table
            ->setHeaders(['Name'])
            ->addRow(['John'])
            ->setTitle('User List');
        
        $output = $this->table->render();
        
        $this->assertStringContainsString('User List', $output);
    }
    
    /**
     * @test
     */
    public function testFluentInterface() {
        $result = $this->table
            ->setHeaders(['Name', 'Age'])
            ->addRow(['John', 30])
            ->setTitle('Test')
            ->useStyle('simple')
            ->setMaxWidth(80)
            ->showHeaders(true);
        
        $this->assertSame($this->table, $result);
    }
    
    /**
     * @test
     */
    public function testGetColumnCount() {
        $this->assertEquals(0, $this->table->getColumnCount());
        
        $this->table->setHeaders(['A', 'B', 'C']);
        $this->assertEquals(3, $this->table->getColumnCount());
    }
    
    /**
     * @test
     */
    public function testGetRowCount() {
        $this->assertEquals(0, $this->table->getRowCount());
        
        $this->table->setHeaders(['Name']);
        $this->table->addRow(['John']);
        $this->table->addRow(['Jane']);
        
        $this->assertEquals(2, $this->table->getRowCount());
    }
    
    /**
     * @test
     */
    public function testDisplay() {
        $this->table
            ->setHeaders(['Name'])
            ->addRow(['John']);
        
        // Capture output
        ob_start();
        $this->table->display();
        $output = ob_get_clean();
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('John', $output);
    }
}
