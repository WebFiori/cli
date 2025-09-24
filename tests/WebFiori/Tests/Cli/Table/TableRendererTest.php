<?php

namespace tests\WebFiori\Cli\Table;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Table\TableRenderer;
use WebFiori\Cli\Table\TableData;
use WebFiori\Cli\Table\TableStyle;
use WebFiori\Cli\Table\TableTheme;
use WebFiori\Cli\Table\Column;

/**
 * Unit tests for TableRenderer class.
 * 
 * Tests the core rendering engine, output generation,
 * and visual formatting functionality.
 */
class TableRendererTest extends TestCase {
    
    private TableRenderer $renderer;
    private TableData $tableData;
    private array $columns;
    
    protected function setUp(): void {
        require_once __DIR__ . '/../../../../../WebFiori/Cli/Table/Column.php';
        require_once __DIR__ . '/../../../../../WebFiori/Cli/Table/TableData.php';
        require_once __DIR__ . '/../../../../../WebFiori/Cli/Table/TableStyle.php';
        require_once __DIR__ . '/../../../../../WebFiori/Cli/Table/TableTheme.php';
        require_once __DIR__ . '/../../../../../WebFiori/Cli/Table/ColumnCalculator.php';
        require_once __DIR__ . '/../../../../../WebFiori/Cli/Table/TableFormatter.php';
        require_once __DIR__ . '/../../../../../WebFiori/Cli/Table/TableRenderer.php';
        
        $style = TableStyle::default();
        $theme = TableTheme::default();
        
        $this->renderer = new TableRenderer($style, $theme);
        
        $headers = ['Name', 'Age', 'City'];
        $rows = [
            ['John Doe', 30, 'New York'],
            ['Jane Smith', 25, 'Los Angeles']
        ];
        
        $this->tableData = new TableData($headers, $rows);
        
        $this->columns = [
            0 => new Column('Name'),
            1 => new Column('Age'),
            2 => new Column('City')
        ];
    }
    
    /**
     * @test
     */
    public function testConstructor() {
        $style = TableStyle::simple();
        $theme = TableTheme::dark();
        
        $renderer = new TableRenderer($style, $theme);
        
        $this->assertInstanceOf(TableRenderer::class, $renderer);
        $this->assertSame($style, $renderer->getStyle());
        $this->assertSame($theme, $renderer->getTheme());
    }
    
    /**
     * @test
     */
    public function testConstructorWithoutTheme() {
        $style = TableStyle::default();
        
        $renderer = new TableRenderer($style);
        
        $this->assertInstanceOf(TableRenderer::class, $renderer);
        $this->assertSame($style, $renderer->getStyle());
        $this->assertNull($renderer->getTheme());
    }
    
    /**
     * @test
     */
    public function testRender() {
        $output = $this->renderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            ''
        );
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('Age', $output);
        $this->assertStringContainsString('City', $output);
        $this->assertStringContainsString('John Doe', $output);
        $this->assertStringContainsString('Jane Smith', $output);
    }
    
    /**
     * @test
     */
    public function testRenderWithTitle() {
        $output = $this->renderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            'User List'
        );
        
        $this->assertStringContainsString('User List', $output);
    }
    
    /**
     * @test
     */
    public function testRenderWithoutHeaders() {
        $output = $this->renderer->render(
            $this->tableData,
            $this->columns,
            80,
            false,
            ''
        );
        
        $this->assertIsString($output);
        // Should still contain data
        $this->assertStringContainsString('John Doe', $output);
        $this->assertStringContainsString('Jane Smith', $output);
    }
    
    /**
     * @test
     */
    public function testRenderEmptyTable() {
        $emptyData = new TableData(['Name'], []);
        
        $output = $this->renderer->render(
            $emptyData,
            [0 => new Column('Name')],
            80,
            true,
            ''
        );
        
        $this->assertStringContainsString('No data to display', $output);
    }
    
    /**
     * @test
     */
    public function testRenderEmptyTableWithTitle() {
        $emptyData = new TableData(['Name'], []);
        
        $output = $this->renderer->render(
            $emptyData,
            [0 => new Column('Name')],
            80,
            true,
            'Empty List'
        );
        
        $this->assertStringContainsString('Empty List', $output);
        $this->assertStringContainsString('No data to display', $output);
    }
    
    /**
     * @test
     */
    public function testRenderWithHiddenColumns() {
        $this->columns[1]->setVisible(false); // Hide Age column
        
        $output = $this->renderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            ''
        );
        
        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('City', $output);
        $this->assertStringNotContainsString('Age', $output);
        $this->assertStringContainsString('John Doe', $output);
        $this->assertStringContainsString('New York', $output);
    }
    
    /**
     * @test
     */
    public function testRenderWithDifferentStyles() {
        $simpleStyle = TableStyle::simple();
        $simpleRenderer = new TableRenderer($simpleStyle);
        
        $output = $simpleRenderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            ''
        );
        
        $this->assertIsString($output);
        $this->assertStringContainsString('+', $output); // Simple style uses +
        $this->assertStringContainsString('-', $output); // Simple style uses -
        $this->assertStringContainsString('|', $output); // Simple style uses |
    }
    
    /**
     * @test
     */
    public function testRenderWithMinimalStyle() {
        $minimalStyle = TableStyle::minimal();
        $minimalRenderer = new TableRenderer($minimalStyle);
        
        $output = $minimalRenderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            ''
        );
        
        $this->assertIsString($output);
        $this->assertStringContainsString('─', $output); // Minimal style uses horizontal line
    }
    
    /**
     * @test
     */
    public function testRenderWithTheme() {
        $colorfulTheme = TableTheme::colorful();
        $themedRenderer = new TableRenderer(TableStyle::default(), $colorfulTheme);
        
        $output = $themedRenderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            ''
        );
        
        $this->assertIsString($output);
        $this->assertStringContainsString("\x1b[", $output); // Should contain ANSI codes
    }
    
    /**
     * @test
     */
    public function testRenderWithColumnFormatting() {
        $this->columns[1]->setFormatter(fn($value) => $value . ' years');
        $this->columns[1]->setAlignment(Column::ALIGN_RIGHT);
        
        $output = $this->renderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            ''
        );
        
        $this->assertStringContainsString('30 years', $output);
        $this->assertStringContainsString('25 years', $output);
    }
    
    /**
     * @test
     */
    public function testRenderWithColumnColors() {
        $this->columns[0]->setColorizer(fn($value) => ['color' => 'green']);
        
        $output = $this->renderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            ''
        );
        
        $this->assertStringContainsString("\x1b[", $output); // Should contain ANSI codes
        $this->assertStringContainsString('John Doe', $output);
    }
    
    /**
     * @test
     */
    public function testSetStyle() {
        $newStyle = TableStyle::simple();
        $result = $this->renderer->setStyle($newStyle);
        
        $this->assertSame($this->renderer, $result); // Fluent interface
        $this->assertSame($newStyle, $this->renderer->getStyle());
    }
    
    /**
     * @test
     */
    public function testSetTheme() {
        $newTheme = TableTheme::dark();
        $result = $this->renderer->setTheme($newTheme);
        
        $this->assertSame($this->renderer, $result); // Fluent interface
        $this->assertSame($newTheme, $this->renderer->getTheme());
    }
    
    /**
     * @test
     */
    public function testSetThemeToNull() {
        $result = $this->renderer->setTheme(null);
        
        $this->assertSame($this->renderer, $result);
        $this->assertNull($this->renderer->getTheme());
    }
    
    /**
     * @test
     */
    public function testRenderWithNarrowWidth() {
        $output = $this->renderer->render(
            $this->tableData,
            $this->columns,
            40, // Narrow width
            true,
            ''
        );
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('John Doe', $output);
    }
    
    /**
     * @test
     */
    public function testRenderWithWideWidth() {
        $output = $this->renderer->render(
            $this->tableData,
            $this->columns,
            120, // Wide width
            true,
            ''
        );
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('John Doe', $output);
    }
    
    /**
     * @test
     */
    public function testRenderConsistency() {
        // Multiple renders should produce identical output
        $output1 = $this->renderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            ''
        );
        
        $output2 = $this->renderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            ''
        );
        
        $this->assertEquals($output1, $output2);
    }
    
    /**
     * @test
     */
    public function testRenderWithComplexData() {
        $headers = ['ID', 'Product', 'Price', 'In Stock', 'Rating'];
        $rows = [
            [1, 'Laptop Pro', 1299.99, true, 4.8],
            [2, 'Wireless Mouse', 29.99, false, 4.2],
            [3, 'Mechanical Keyboard', 149.99, true, 4.6]
        ];
        
        $complexData = new TableData($headers, $rows);
        $complexColumns = [
            0 => Column::create('ID')->setWidth(4)->setAlignment(Column::ALIGN_CENTER),
            1 => Column::create('Product')->setWidth(20),
            2 => Column::create('Price')->setWidth(10)->setAlignment(Column::ALIGN_RIGHT),
            3 => Column::create('In Stock')->setWidth(10)->setAlignment(Column::ALIGN_CENTER),
            4 => Column::create('Rating')->setWidth(8)->setAlignment(Column::ALIGN_RIGHT)
        ];
        
        $output = $this->renderer->render(
            $complexData,
            $complexColumns,
            80,
            true,
            'Product Catalog'
        );
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Product Catalog', $output);
        $this->assertStringContainsString('Laptop Pro', $output);
        $this->assertStringContainsString('1299.99', $output);
    }
    
    /**
     * @test
     */
    public function testRenderBorderGeneration() {
        $style = TableStyle::bordered();
        $renderer = new TableRenderer($style);
        
        $output = $renderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            ''
        );
        
        // Should contain Unicode box-drawing characters
        $this->assertStringContainsString('┌', $output); // Top-left
        $this->assertStringContainsString('┐', $output); // Top-right
        $this->assertStringContainsString('└', $output); // Bottom-left
        $this->assertStringContainsString('┘', $output); // Bottom-right
        $this->assertStringContainsString('─', $output); // Horizontal
        $this->assertStringContainsString('│', $output); // Vertical
    }
    
    /**
     * @test
     */
    public function testRenderWithLongTitle() {
        $longTitle = 'This is a very long title that might exceed the table width';
        
        $output = $this->renderer->render(
            $this->tableData,
            $this->columns,
            50, // Narrow width
            true,
            $longTitle
        );
        
        $this->assertStringContainsString($longTitle, $output);
    }
    
    /**
     * @test
     */
    public function testRenderOutputStructure() {
        $output = $this->renderer->render(
            $this->tableData,
            $this->columns,
            80,
            true,
            'Test Table'
        );
        
        $lines = explode("\n", $output);
        
        // Should have multiple lines
        $this->assertGreaterThan(5, count($lines));
        
        // Should not end with extra newlines
        $this->assertNotEquals('', end($lines));
    }
}
