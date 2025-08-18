<?php

namespace tests\WebFiori\Cli\Table;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Table\TableStyle;

/**
 * Unit tests for TableStyle class.
 * 
 * Tests visual styling definitions, predefined styles,
 * and style configuration options.
 */
class TableStyleTest extends TestCase {
    
    protected function setUp(): void {
        require_once __DIR__ . '/../../../../WebFiori/Cli/Table/TableStyle.php';
    }
    
    /**
     * @test
     */
    public function testDefaultStyle() {
        $style = TableStyle::default();
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertEquals('┌', $style->topLeft);
        $this->assertEquals('┐', $style->topRight);
        $this->assertEquals('└', $style->bottomLeft);
        $this->assertEquals('┘', $style->bottomRight);
        $this->assertEquals('─', $style->horizontal);
        $this->assertEquals('│', $style->vertical);
        $this->assertTrue($style->showBorders);
        $this->assertTrue($style->showHeaderSeparator);
    }
    
    /**
     * @test
     */
    public function testBorderedStyle() {
        $style = TableStyle::bordered();
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertTrue($style->showBorders);
    }
    
    /**
     * @test
     */
    public function testSimpleStyle() {
        $style = TableStyle::simple();
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertEquals('+', $style->topLeft);
        $this->assertEquals('+', $style->topRight);
        $this->assertEquals('-', $style->horizontal);
        $this->assertEquals('|', $style->vertical);
        $this->assertTrue($style->showBorders);
    }
    
    /**
     * @test
     */
    public function testMinimalStyle() {
        $style = TableStyle::minimal();
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertFalse($style->showBorders);
        $this->assertTrue($style->showHeaderSeparator);
    }
    
    /**
     * @test
     */
    public function testCompactStyle() {
        $style = TableStyle::compact();
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertEquals(0, $style->paddingLeft);
        $this->assertEquals(1, $style->paddingRight);
        $this->assertFalse($style->showBorders);
    }
    
    /**
     * @test
     */
    public function testMarkdownStyle() {
        $style = TableStyle::markdown();
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertEquals('|', $style->vertical);
        $this->assertEquals('-', $style->horizontal);
        $this->assertTrue($style->showBorders);
        $this->assertTrue($style->showHeaderSeparator);
        $this->assertFalse($style->showRowSeparators);
    }
    
    /**
     * @test
     */
    public function testDoubleBorderedStyle() {
        $style = TableStyle::doubleBordered();
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertEquals('╔', $style->topLeft);
        $this->assertEquals('╗', $style->topRight);
        $this->assertEquals('═', $style->horizontal);
        $this->assertEquals('║', $style->vertical);
    }
    
    /**
     * @test
     */
    public function testRoundedStyle() {
        $style = TableStyle::rounded();
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertEquals('╭', $style->topLeft);
        $this->assertEquals('╮', $style->topRight);
        $this->assertEquals('╰', $style->bottomLeft);
        $this->assertEquals('╯', $style->bottomRight);
    }
    
    /**
     * @test
     */
    public function testHeavyStyle() {
        $style = TableStyle::heavy();
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertEquals('┏', $style->topLeft);
        $this->assertEquals('┓', $style->topRight);
        $this->assertEquals('━', $style->horizontal);
        $this->assertEquals('┃', $style->vertical);
    }
    
    /**
     * @test
     */
    public function testNoneStyle() {
        $style = TableStyle::none();
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertFalse($style->showBorders);
        $this->assertFalse($style->showHeaderSeparator);
        $this->assertFalse($style->showRowSeparators);
        $this->assertEquals(0, $style->paddingLeft);
        $this->assertEquals(2, $style->paddingRight);
    }
    
    /**
     * @test
     */
    public function testCustomStyle() {
        $overrides = [
            'topLeft' => 'A',
            'topRight' => 'B',
            'horizontal' => 'C',
            'vertical' => 'D',
            'paddingLeft' => 3,
            'showBorders' => false
        ];
        
        $style = TableStyle::custom($overrides);
        
        $this->assertInstanceOf(TableStyle::class, $style);
        $this->assertEquals('A', $style->topLeft);
        $this->assertEquals('B', $style->topRight);
        $this->assertEquals('C', $style->horizontal);
        $this->assertEquals('D', $style->vertical);
        $this->assertEquals(3, $style->paddingLeft);
        $this->assertFalse($style->showBorders);
    }
    
    /**
     * @test
     */
    public function testGetTotalPadding() {
        $style = new TableStyle(['paddingLeft' => 2, 'paddingRight' => 3]);
        
        $this->assertEquals(5, $style->getTotalPadding());
    }
    
    /**
     * @test
     */
    public function testGetBorderWidth() {
        $style = new TableStyle(['showBorders' => true]);
        
        // 3 columns = left border + right border + 2 separators = 4
        $this->assertEquals(4, $style->getBorderWidth(3));
    }
    
    /**
     * @test
     */
    public function testGetBorderWidthNoBorders() {
        $style = new TableStyle(['showBorders' => false]);
        
        $this->assertEquals(0, $style->getBorderWidth(3));
    }
    
    /**
     * @test
     */
    public function testIsUnicodeWithUnicodeCharacters() {
        $style = TableStyle::default(); // Uses Unicode characters
        
        $this->assertTrue($style->isUnicode());
    }
    
    /**
     * @test
     */
    public function testIsUnicodeWithAsciiCharacters() {
        $style = TableStyle::simple(); // Uses ASCII characters
        
        $this->assertFalse($style->isUnicode());
    }
    
    /**
     * @test
     */
    public function testGetAsciiFallback() {
        $unicodeStyle = TableStyle::default();
        $fallback = $unicodeStyle->getAsciiFallback();
        
        $this->assertInstanceOf(TableStyle::class, $fallback);
        $this->assertFalse($fallback->isUnicode());
    }
    
    /**
     * @test
     */
    public function testGetAsciiFallbackForAsciiStyle() {
        $asciiStyle = TableStyle::simple();
        $fallback = $asciiStyle->getAsciiFallback();
        
        $this->assertSame($asciiStyle, $fallback);
    }
    
    /**
     * @test
     */
    public function testConstructorWithAllParameters() {
        $style = new TableStyle([
            'topLeft' => 'A',
            'topRight' => 'B',
            'bottomLeft' => 'C',
            'bottomRight' => 'D',
            'horizontal' => 'E',
            'vertical' => 'F',
            'cross' => 'G',
            'topTee' => 'H',
            'bottomTee' => 'I',
            'leftTee' => 'J',
            'rightTee' => 'K',
            'paddingLeft' => 2,
            'paddingRight' => 3,
            'showBorders' => false,
            'showHeaderSeparator' => false,
            'showRowSeparators' => true
        ]);
        
        $this->assertEquals('A', $style->topLeft);
        $this->assertEquals('B', $style->topRight);
        $this->assertEquals('C', $style->bottomLeft);
        $this->assertEquals('D', $style->bottomRight);
        $this->assertEquals('E', $style->horizontal);
        $this->assertEquals('F', $style->vertical);
        $this->assertEquals('G', $style->cross);
        $this->assertEquals('H', $style->topTee);
        $this->assertEquals('I', $style->bottomTee);
        $this->assertEquals('J', $style->leftTee);
        $this->assertEquals('K', $style->rightTee);
        $this->assertEquals(2, $style->paddingLeft);
        $this->assertEquals(3, $style->paddingRight);
        $this->assertFalse($style->showBorders);
        $this->assertFalse($style->showHeaderSeparator);
        $this->assertTrue($style->showRowSeparators);
    }
    
    /**
     * @test
     */
    public function testConstructorWithEmptyArray() {
        $style = new TableStyle([]);
        
        // Should use all defaults
        $this->assertEquals('┌', $style->topLeft);
        $this->assertEquals('┐', $style->topRight);
        $this->assertEquals('─', $style->horizontal);
        $this->assertEquals('│', $style->vertical);
        $this->assertEquals(1, $style->paddingLeft);
        $this->assertEquals(1, $style->paddingRight);
        $this->assertTrue($style->showBorders);
        $this->assertTrue($style->showHeaderSeparator);
        $this->assertFalse($style->showRowSeparators);
    }
    
    /**
     * @test
     */
    public function testConstructorWithPartialOverrides() {
        $style = new TableStyle([
            'topLeft' => 'X',
            'paddingLeft' => 5,
            'showBorders' => false
        ]);
        
        // Should use provided values
        $this->assertEquals('X', $style->topLeft);
        $this->assertEquals(5, $style->paddingLeft);
        $this->assertFalse($style->showBorders);
        
        // Should use defaults for non-provided values
        $this->assertEquals('┐', $style->topRight);
        $this->assertEquals('─', $style->horizontal);
        $this->assertEquals(1, $style->paddingRight);
        $this->assertTrue($style->showHeaderSeparator);
    }
    
    /**
     * @test
     */
    public function testReadonlyProperties() {
        $style = TableStyle::default();
        
        // These should not cause errors (readonly properties)
        $this->assertIsString($style->topLeft);
        $this->assertIsString($style->horizontal);
        $this->assertIsInt($style->paddingLeft);
        $this->assertIsBool($style->showBorders);
    }
}
