<?php

namespace tests\WebFiori\Cli\Table;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Table\Column;

/**
 * Unit tests for Column class.
 * 
 * Tests column configuration, formatting, alignment,
 * and content processing functionality.
 */
class ColumnTest extends TestCase {
    
    private Column $column;
    
    protected function setUp(): void {
        require_once __DIR__ . '/../../../../../WebFiori/Cli/Table/Column.php';
        
        $this->column = new Column('Test Column');
    }
    
    /**
     * @test
     */
    public function testConstructor() {
        $column = new Column('Test Name');
        
        $this->assertEquals('Test Name', $column->getName());
        $this->assertEquals(Column::ALIGN_AUTO, $column->getAlignment());
        $this->assertTrue($column->shouldTruncate());
        $this->assertTrue($column->isVisible());
    }
    
    /**
     * @test
     */
    public function testConfigure() {
        $config = [
            'width' => 20,
            'align' => Column::ALIGN_RIGHT,
            'truncate' => false,
            'ellipsis' => '...',
            'visible' => false,
            'default' => 'N/A'
        ];
        
        $result = $this->column->configure($config);
        
        $this->assertSame($this->column, $result); // Fluent interface
        $this->assertEquals(20, $this->column->getWidth());
        $this->assertEquals(Column::ALIGN_RIGHT, $this->column->getAlignment());
        $this->assertFalse($this->column->shouldTruncate());
        $this->assertEquals('...', $this->column->getEllipsis());
        $this->assertFalse($this->column->isVisible());
        $this->assertEquals('N/A', $this->column->getDefaultValue());
    }
    
    /**
     * @test
     */
    public function testConfigureWithUnderscoreKeys() {
        $config = [
            'min_width' => 10,
            'max_width' => 50,
            'word_wrap' => true,
            'default_value' => 'Empty'
        ];
        
        $this->column->configure($config);
        
        $this->assertEquals(10, $this->column->getMinWidth());
        $this->assertEquals(50, $this->column->getMaxWidth());
        $this->assertTrue($this->column->shouldWordWrap());
        $this->assertEquals('Empty', $this->column->getDefaultValue());
    }
    
    /**
     * @test
     */
    public function testSetWidth() {
        $result = $this->column->setWidth(25);
        
        $this->assertSame($this->column, $result);
        $this->assertEquals(25, $this->column->getWidth());
    }
    
    /**
     * @test
     */
    public function testSetMinWidth() {
        $result = $this->column->setMinWidth(5);
        
        $this->assertSame($this->column, $result);
        $this->assertEquals(5, $this->column->getMinWidth());
    }
    
    /**
     * @test
     */
    public function testSetMaxWidth() {
        $result = $this->column->setMaxWidth(100);
        
        $this->assertSame($this->column, $result);
        $this->assertEquals(100, $this->column->getMaxWidth());
    }
    
    /**
     * @test
     */
    public function testSetAlignment() {
        $result = $this->column->setAlignment(Column::ALIGN_CENTER);
        
        $this->assertSame($this->column, $result);
        $this->assertEquals(Column::ALIGN_CENTER, $this->column->getAlignment());
    }
    
    /**
     * @test
     */
    public function testSetAlignmentInvalid() {
        $this->column->setAlignment('invalid');
        
        // Should remain unchanged
        $this->assertEquals(Column::ALIGN_AUTO, $this->column->getAlignment());
    }
    
    /**
     * @test
     */
    public function testSetFormatter() {
        $formatter = fn($value) => strtoupper($value);
        $result = $this->column->setFormatter($formatter);
        
        $this->assertSame($this->column, $result);
        $this->assertSame($formatter, $this->column->getFormatter());
    }
    
    /**
     * @test
     */
    public function testSetColorizer() {
        $colorizer = fn($value) => ['color' => 'red'];
        $result = $this->column->setColorizer($colorizer);
        
        $this->assertSame($this->column, $result);
        $this->assertSame($colorizer, $this->column->getColorizer());
    }
    
    /**
     * @test
     */
    public function testSetDefaultValue() {
        $result = $this->column->setDefaultValue('Default');
        
        $this->assertSame($this->column, $result);
        $this->assertEquals('Default', $this->column->getDefaultValue());
    }
    
    /**
     * @test
     */
    public function testSetVisible() {
        $result = $this->column->setVisible(false);
        
        $this->assertSame($this->column, $result);
        $this->assertFalse($this->column->isVisible());
    }
    
    /**
     * @test
     */
    public function testSetMetadata() {
        $result = $this->column->setMetadata('custom_key', 'custom_value');
        
        $this->assertSame($this->column, $result);
        $this->assertEquals('custom_value', $this->column->getMetadata('custom_key'));
    }
    
    /**
     * @test
     */
    public function testGetMetadataWithDefault() {
        $this->assertEquals('default', $this->column->getMetadata('nonexistent', 'default'));
    }
    
    /**
     * @test
     */
    public function testGetAllMetadata() {
        $this->column->setMetadata('key1', 'value1');
        $this->column->setMetadata('key2', 'value2');
        
        $metadata = $this->column->getAllMetadata();
        
        $this->assertIsArray($metadata);
        $this->assertEquals('value1', $metadata['key1']);
        $this->assertEquals('value2', $metadata['key2']);
    }
    
    /**
     * @test
     */
    public function testCalculateIdealWidth() {
        $this->column->setMinWidth(5);
        $this->column->setMaxWidth(20);
        
        $values = ['Short', 'Medium length', 'Very long text that exceeds normal width'];
        $width = $this->column->calculateIdealWidth($values);
        
        $this->assertIsInt($width);
        $this->assertGreaterThanOrEqual(5, $width); // At least min width
        $this->assertLessThanOrEqual(20, $width); // At most max width
    }
    
    /**
     * @test
     */
    public function testFormatValue() {
        $this->assertEquals('test', $this->column->formatValue('test'));
        $this->assertEquals('', $this->column->formatValue(null));
        $this->assertEquals('', $this->column->formatValue(''));
    }
    
    /**
     * @test
     */
    public function testFormatValueWithDefault() {
        $this->column->setDefaultValue('N/A');
        
        $this->assertEquals('N/A', $this->column->formatValue(null));
        $this->assertEquals('N/A', $this->column->formatValue(''));
        $this->assertEquals('test', $this->column->formatValue('test'));
    }
    
    /**
     * @test
     */
    public function testFormatValueWithFormatter() {
        $this->column->setFormatter(fn($value) => strtoupper($value));
        
        $this->assertEquals('TEST', $this->column->formatValue('test'));
    }
    
    /**
     * @test
     */
    public function testColorizeValue() {
        $this->assertEquals('test', $this->column->colorizeValue('test'));
    }
    
    /**
     * @test
     */
    public function testColorizeValueWithColorizer() {
        $this->column->setColorizer(fn($value) => ['color' => 'red']);
        
        $result = $this->column->colorizeValue('test');
        
        $this->assertStringContainsString('test', $result);
        $this->assertStringContainsString("\x1b[", $result); // ANSI escape sequence
    }
    
    /**
     * @test
     */
    public function testTruncateText() {
        $this->column->setTruncate(true);
        $this->column->setEllipsis('...');
        
        $result = $this->column->truncateText('This is a very long text', 10);
        
        $this->assertLessThanOrEqual(10, strlen($result));
        $this->assertStringContainsString('...', $result);
    }
    
    /**
     * @test
     */
    public function testTruncateTextDisabled() {
        $this->column->setTruncate(false);
        
        $text = 'This is a very long text';
        $result = $this->column->truncateText($text, 10);
        
        $this->assertEquals($text, $result);
    }
    
    /**
     * @test
     */
    public function testAlignTextLeft() {
        $this->column->setAlignment(Column::ALIGN_LEFT);
        
        $result = $this->column->alignText('test', 10);
        
        $this->assertEquals('test      ', $result);
    }
    
    /**
     * @test
     */
    public function testAlignTextRight() {
        $this->column->setAlignment(Column::ALIGN_RIGHT);
        
        $result = $this->column->alignText('test', 10);
        
        $this->assertEquals('      test', $result);
    }
    
    /**
     * @test
     */
    public function testAlignTextCenter() {
        $this->column->setAlignment(Column::ALIGN_CENTER);
        
        $result = $this->column->alignText('test', 10);
        
        $this->assertEquals('   test   ', $result);
    }
    
    /**
     * @test
     */
    public function testAlignTextAuto() {
        $this->column->setAlignment(Column::ALIGN_AUTO);
        
        // Text should be left-aligned
        $textResult = $this->column->alignText('text', 10);
        $this->assertEquals('text      ', $textResult);
        
        // Numbers should be right-aligned
        $numberResult = $this->column->alignText('123', 10);
        $this->assertEquals('       123', $numberResult);
    }
    
    /**
     * @test
     */
    public function testStaticCreateMethods() {
        $column = Column::create('Test');
        $this->assertInstanceOf(Column::class, $column);
        $this->assertEquals('Test', $column->getName());
        
        $leftColumn = Column::left('Left', 20);
        $this->assertEquals(Column::ALIGN_LEFT, $leftColumn->getAlignment());
        $this->assertEquals(20, $leftColumn->getWidth());
        
        $rightColumn = Column::right('Right', 15);
        $this->assertEquals(Column::ALIGN_RIGHT, $rightColumn->getAlignment());
        $this->assertEquals(15, $rightColumn->getWidth());
        
        $centerColumn = Column::center('Center', 25);
        $this->assertEquals(Column::ALIGN_CENTER, $centerColumn->getAlignment());
        $this->assertEquals(25, $centerColumn->getWidth());
    }
    
    /**
     * @test
     */
    public function testNumericColumn() {
        $column = Column::numeric('Price', 10, 2);
        
        $this->assertEquals(Column::ALIGN_RIGHT, $column->getAlignment());
        $this->assertEquals(10, $column->getWidth());
        
        $formatter = $column->getFormatter();
        $this->assertIsCallable($formatter);
        
        $result = $formatter(1234.567);
        $this->assertEquals('1,234.57', $result);
    }
    
    /**
     * @test
     */
    public function testDateColumn() {
        $column = Column::date('Created', 12, 'Y-m-d');
        
        $this->assertEquals(Column::ALIGN_LEFT, $column->getAlignment());
        $this->assertEquals(12, $column->getWidth());
        
        $formatter = $column->getFormatter();
        $this->assertIsCallable($formatter);
        
        $result = $formatter('2024-01-15 10:30:00');
        $this->assertEquals('2024-01-15', $result);
    }
    
    /**
     * @test
     */
    public function testDateColumnWithInvalidDate() {
        $column = Column::date('Created');
        $formatter = $column->getFormatter();
        
        $result = $formatter('invalid-date');
        $this->assertEquals('invalid-date', $result);
    }
    
    /**
     * @test
     */
    public function testConstants() {
        $this->assertEquals('left', Column::ALIGN_LEFT);
        $this->assertEquals('right', Column::ALIGN_RIGHT);
        $this->assertEquals('center', Column::ALIGN_CENTER);
        $this->assertEquals('auto', Column::ALIGN_AUTO);
    }
}
