<?php

namespace tests\WebFiori\Cli\Table;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Table\ColumnCalculator;
use WebFiori\Cli\Table\TableData;
use WebFiori\Cli\Table\TableStyle;
use WebFiori\Cli\Table\Column;

/**
 * Unit tests for ColumnCalculator class.
 * 
 * Tests width calculation algorithms, responsive design,
 * and column sizing optimization.
 */
class ColumnCalculatorTest extends TestCase {
    
    private ColumnCalculator $calculator;
    private TableData $tableData;
    private TableStyle $style;
    private array $columns;
    
    protected function setUp(): void {
        require_once __DIR__ . '/../../../../WebFiori/Cli/Table/Column.php';
        require_once __DIR__ . '/../../../../WebFiori/Cli/Table/TableData.php';
        require_once __DIR__ . '/../../../../WebFiori/Cli/Table/TableStyle.php';
        require_once __DIR__ . '/../../../../WebFiori/Cli/Table/ColumnCalculator.php';
        
        $this->calculator = new ColumnCalculator();
        
        $headers = ['Name', 'Age', 'City'];
        $rows = [
            ['John Doe', 30, 'New York'],
            ['Jane Smith', 25, 'Los Angeles'],
            ['Bob Johnson', 35, 'Chicago']
        ];
        
        $this->tableData = new TableData($headers, $rows);
        $this->style = TableStyle::default();
        
        $this->columns = [
            0 => new Column('Name'),
            1 => new Column('Age'),
            2 => new Column('City')
        ];
    }
    
    /**
     * @test
     */
    public function testCalculateWidths() {
        $maxWidth = 80;
        
        $widths = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $this->assertIsArray($widths);
        $this->assertCount(3, $widths);
        
        // All widths should be positive integers
        foreach ($widths as $width) {
            $this->assertIsInt($width);
            $this->assertGreaterThan(0, $width);
        }
        
        // Total width should not exceed available space
        $totalWidth = array_sum($widths);
        $borderWidth = $this->style->getBorderWidth(3);
        $paddingWidth = 3 * $this->style->getTotalPadding();
        
        $this->assertLessThanOrEqual($maxWidth - $borderWidth - $paddingWidth, $totalWidth);
    }
    
    /**
     * @test
     */
    public function testCalculateWidthsWithFixedColumnWidth() {
        $this->columns[0]->setWidth(20);
        $maxWidth = 80;
        
        $widths = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $this->assertEquals(20, $widths[0]);
    }
    
    /**
     * @test
     */
    public function testCalculateWidthsWithMinWidth() {
        $this->columns[1]->setMinWidth(15);
        $maxWidth = 80;
        
        $widths = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $this->assertGreaterThanOrEqual(15, $widths[1]);
    }
    
    /**
     * @test
     */
    public function testCalculateWidthsWithMaxWidth() {
        $this->columns[0]->setMaxWidth(10);
        $maxWidth = 80;
        
        $widths = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $this->assertLessThanOrEqual(10, $widths[0]);
    }
    
    /**
     * @test
     */
    public function testCalculateWidthsEmptyColumns() {
        $widths = $this->calculator->calculateWidths(
            $this->tableData,
            [],
            80,
            $this->style
        );
        
        $this->assertIsArray($widths);
        $this->assertEmpty($widths);
    }
    
    /**
     * @test
     */
    public function testCalculateWidthsNarrowTerminal() {
        $maxWidth = 30; // Very narrow
        
        $widths = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $this->assertIsArray($widths);
        $this->assertCount(3, $widths);
        
        // Should still provide minimum widths
        foreach ($widths as $width) {
            $this->assertGreaterThanOrEqual(3, $width); // MIN_COLUMN_WIDTH
        }
    }
    
    /**
     * @test
     */
    public function testCalculateResponsiveWidths() {
        $maxWidth = 120; // Wide terminal
        
        $widths = $this->calculator->calculateResponsiveWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $this->assertIsArray($widths);
        $this->assertCount(3, $widths);
        
        foreach ($widths as $width) {
            $this->assertIsInt($width);
            $this->assertGreaterThan(0, $width);
        }
    }
    
    /**
     * @test
     */
    public function testCalculateResponsiveWidthsNarrow() {
        $maxWidth = 25; // Very narrow terminal
        
        $widths = $this->calculator->calculateResponsiveWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $this->assertIsArray($widths);
        $this->assertCount(3, $widths);
        
        // Should use narrow width strategy
        foreach ($widths as $width) {
            $this->assertGreaterThanOrEqual(3, $width);
        }
    }
    
    /**
     * @test
     */
    public function testAutoConfigureColumns() {
        $columns = $this->calculator->autoConfigureColumns($this->tableData);
        
        $this->assertIsArray($columns);
        $this->assertCount(3, $columns);
        
        foreach ($columns as $column) {
            $this->assertInstanceOf(Column::class, $column);
        }
        
        // Age column should be right-aligned (numeric)
        $this->assertEquals(Column::ALIGN_RIGHT, $columns[1]->getAlignment());
        
        // Name and City should be left-aligned (string)
        $this->assertEquals(Column::ALIGN_LEFT, $columns[0]->getAlignment());
        $this->assertEquals(Column::ALIGN_LEFT, $columns[2]->getAlignment());
    }
    
    /**
     * @test
     */
    public function testAutoConfigureColumnsWithDifferentTypes() {
        $headers = ['Name', 'Price', 'Date', 'Active'];
        $rows = [
            ['Product A', 19.99, '2024-01-15', true],
            ['Product B', 29.99, '2024-01-16', false]
        ];
        
        $tableData = new TableData($headers, $rows);
        $columns = $this->calculator->autoConfigureColumns($tableData);
        
        $this->assertCount(4, $columns);
        
        // Name should be left-aligned
        $this->assertEquals(Column::ALIGN_LEFT, $columns[0]->getAlignment());
        
        // Price should be right-aligned (float)
        $this->assertEquals(Column::ALIGN_RIGHT, $columns[1]->getAlignment());
        
        // Date should be left-aligned
        $this->assertEquals(Column::ALIGN_LEFT, $columns[2]->getAlignment());
        
        // Active should be left-aligned (boolean treated as string by default)
        $this->assertEquals(Column::ALIGN_LEFT, $columns[3]->getAlignment());
    }
    
    /**
     * @test
     */
    public function testAutoConfigureColumnsWithMaxWidth() {
        // Create data with very long content
        $headers = ['Description'];
        $rows = [
            ['This is a very long description that should trigger max width constraints'],
            ['Another long description that exceeds normal column width limits']
        ];
        
        $tableData = new TableData($headers, $rows);
        $columns = $this->calculator->autoConfigureColumns($tableData);
        
        $this->assertCount(1, $columns);
        
        // Should have max width constraint
        $maxWidth = $columns[0]->getMaxWidth();
        $this->assertNotNull($maxWidth);
        $this->assertLessThanOrEqual(50, $maxWidth); // Should be capped at 50
    }
    
    /**
     * @test
     */
    public function testWidthDistributionWithConstraints() {
        // Test complex scenario with mixed constraints
        $this->columns[0]->setMinWidth(10);
        $this->columns[0]->setMaxWidth(20);
        $this->columns[1]->setWidth(8); // Fixed width
        $this->columns[2]->setMinWidth(15);
        
        $maxWidth = 60;
        
        $widths = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        // Column 0: between 10 and 20
        $this->assertGreaterThanOrEqual(10, $widths[0]);
        $this->assertLessThanOrEqual(20, $widths[0]);
        
        // Column 1: exactly 8 (fixed)
        $this->assertEquals(8, $widths[1]);
        
        // Column 2: at least 15
        $this->assertGreaterThanOrEqual(15, $widths[2]);
    }
    
    /**
     * @test
     */
    public function testWidthCalculationWithDifferentStyles() {
        $simpleStyle = TableStyle::simple();
        $minimalStyle = TableStyle::minimal();
        
        $widthsDefault = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            80,
            $this->style
        );
        
        $widthsSimple = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            80,
            $simpleStyle
        );
        
        $widthsMinimal = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            80,
            $minimalStyle
        );
        
        // All should return valid widths
        $this->assertCount(3, $widthsDefault);
        $this->assertCount(3, $widthsSimple);
        $this->assertCount(3, $widthsMinimal);
        
        // Minimal style might allow more content width (less borders)
        $totalDefault = array_sum($widthsDefault);
        $totalMinimal = array_sum($widthsMinimal);
        
        $this->assertGreaterThanOrEqual($totalDefault, $totalMinimal);
    }
    
    /**
     * @test
     */
    public function testEdgeCaseVerySmallWidth() {
        $maxWidth = 15; // Extremely small
        
        $widths = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $this->assertIsArray($widths);
        $this->assertCount(3, $widths);
        
        // Should still provide minimum viable widths
        foreach ($widths as $width) {
            $this->assertGreaterThanOrEqual(3, $width);
        }
    }
    
    /**
     * @test
     */
    public function testWidthCalculationConsistency() {
        // Multiple calls should return consistent results
        $maxWidth = 80;
        
        $widths1 = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $widths2 = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $this->assertEquals($widths1, $widths2);
    }
    
    /**
     * @test
     */
    public function testWidthCalculationWithEmptyData() {
        $emptyData = new TableData(['A', 'B', 'C'], []);
        
        $widths = $this->calculator->calculateWidths(
            $emptyData,
            $this->columns,
            80,
            $this->style
        );
        
        $this->assertIsArray($widths);
        $this->assertCount(3, $widths);
        
        // Should base widths on headers only
        foreach ($widths as $width) {
            $this->assertGreaterThan(0, $width);
        }
    }
    
    /**
     * @test
     */
    public function testProportionalWidthDistribution() {
        // Test that remaining width is distributed proportionally
        $maxWidth = 100;
        
        // Set one column to fixed small width
        $this->columns[1]->setWidth(5);
        
        $widths = $this->calculator->calculateWidths(
            $this->tableData,
            $this->columns,
            $maxWidth,
            $this->style
        );
        
        $this->assertEquals(5, $widths[1]);
        
        // Other columns should share remaining space
        $this->assertGreaterThan(5, $widths[0]);
        $this->assertGreaterThan(5, $widths[2]);
    }
}
