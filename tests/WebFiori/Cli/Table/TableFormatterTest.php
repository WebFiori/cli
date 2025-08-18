<?php

namespace tests\WebFiori\Cli\Table;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Table\TableFormatter;
use WebFiori\Cli\Table\Column;

/**
 * Unit tests for TableFormatter class.
 * 
 * Tests content-specific formatting logic, data type formatting,
 * and custom formatter registration.
 */
class TableFormatterTest extends TestCase {
    
    private TableFormatter $formatter;
    private Column $column;
    
    protected function setUp(): void {
        require_once __DIR__ . '/../../../../WebFiori/Cli/Table/Column.php';
        require_once __DIR__ . '/../../../../WebFiori/Cli/Table/TableFormatter.php';
        
        $this->formatter = new TableFormatter();
        $this->column = new Column('Test');
    }
    
    /**
     * @test
     */
    public function testFormatHeader() {
        $result = $this->formatter->formatHeader('test_header');
        
        $this->assertEquals('Test Header', $result);
    }
    
    /**
     * @test
     */
    public function testFormatHeaderWithDashes() {
        $result = $this->formatter->formatHeader('test-header-name');
        
        $this->assertEquals('Test Header Name', $result);
    }
    
    /**
     * @test
     */
    public function testFormatCell() {
        $result = $this->formatter->formatCell('test value', $this->column);
        
        $this->assertEquals('test value', $result);
    }
    
    /**
     * @test
     */
    public function testFormatCellWithNull() {
        $this->column->setDefaultValue('N/A');
        $result = $this->formatter->formatCell(null, $this->column);
        
        $this->assertEquals('N/A', $result);
    }
    
    /**
     * @test
     */
    public function testFormatCellWithEmpty() {
        $this->column->setDefaultValue('Empty');
        $result = $this->formatter->formatCell('', $this->column);
        
        $this->assertEquals('Empty', $result);
    }
    
    /**
     * @test
     */
    public function testFormatCellWithColumnFormatter() {
        $this->column->setFormatter(fn($value) => strtoupper($value));
        $result = $this->formatter->formatCell('test', $this->column);
        
        $this->assertEquals('TEST', $result);
    }
    
    /**
     * @test
     */
    public function testRegisterFormatter() {
        $customFormatter = fn($value) => "Custom: $value";
        $result = $this->formatter->registerFormatter('custom', $customFormatter);
        
        $this->assertSame($this->formatter, $result); // Fluent interface
    }
    
    /**
     * @test
     */
    public function testRegisterGlobalFormatter() {
        $globalFormatter = fn($value, $type) => "Global: $value";
        $result = $this->formatter->registerGlobalFormatter($globalFormatter);
        
        $this->assertSame($this->formatter, $result);
    }
    
    /**
     * @test
     */
    public function testFormatNumber() {
        $result = $this->formatter->formatNumber(1234.567, 2);
        
        $this->assertEquals('1,234.57', $result);
    }
    
    /**
     * @test
     */
    public function testFormatNumberWithCustomSeparators() {
        $result = $this->formatter->formatNumber(1234.567, 2, ',', '.');
        
        $this->assertEquals('1.234,57', $result);
    }
    
    /**
     * @test
     */
    public function testFormatCurrency() {
        $result = $this->formatter->formatCurrency(1234.56);
        
        $this->assertEquals('$1,234.56', $result);
    }
    
    /**
     * @test
     */
    public function testFormatCurrencyCustomSymbol() {
        $result = $this->formatter->formatCurrency(1234.56, '€', 2, false);
        
        $this->assertEquals('1,234.56 €', $result);
    }
    
    /**
     * @test
     */
    public function testFormatPercentage() {
        $result = $this->formatter->formatPercentage(85.5);
        
        $this->assertEquals('85.5%', $result);
    }
    
    /**
     * @test
     */
    public function testFormatPercentageWithDecimals() {
        $result = $this->formatter->formatPercentage(85.567, 2);
        
        $this->assertEquals('85.57%', $result);
    }
    
    /**
     * @test
     */
    public function testFormatDate() {
        $result = $this->formatter->formatDate('2024-01-15');
        
        $this->assertEquals('2024-01-15', $result);
    }
    
    /**
     * @test
     */
    public function testFormatDateWithCustomFormat() {
        $result = $this->formatter->formatDate('2024-01-15', 'M j, Y');
        
        $this->assertEquals('Jan 15, 2024', $result);
    }
    
    /**
     * @test
     */
    public function testFormatDateWithDateTime() {
        $date = new \DateTime('2024-01-15');
        $result = $this->formatter->formatDate($date, 'Y-m-d');
        
        $this->assertEquals('2024-01-15', $result);
    }
    
    /**
     * @test
     */
    public function testFormatDateWithTimestamp() {
        $timestamp = strtotime('2024-01-15');
        $result = $this->formatter->formatDate($timestamp, 'Y-m-d');
        
        $this->assertEquals('2024-01-15', $result);
    }
    
    /**
     * @test
     */
    public function testFormatDateInvalid() {
        $result = $this->formatter->formatDate('invalid-date');
        
        $this->assertEquals('invalid-date', $result);
    }
    
    /**
     * @test
     */
    public function testFormatBoolean() {
        $this->assertEquals('Yes', $this->formatter->formatBoolean(true));
        $this->assertEquals('No', $this->formatter->formatBoolean(false));
    }
    
    /**
     * @test
     */
    public function testFormatBooleanCustomText() {
        $result = $this->formatter->formatBoolean(true, 'Active', 'Inactive');
        
        $this->assertEquals('Active', $result);
    }
    
    /**
     * @test
     */
    public function testFormatBooleanString() {
        $this->assertEquals('Yes', $this->formatter->formatBoolean('true'));
        $this->assertEquals('Yes', $this->formatter->formatBoolean('1'));
        $this->assertEquals('Yes', $this->formatter->formatBoolean('yes'));
        $this->assertEquals('No', $this->formatter->formatBoolean('false'));
        $this->assertEquals('No', $this->formatter->formatBoolean('0'));
        $this->assertEquals('No', $this->formatter->formatBoolean('no'));
    }
    
    /**
     * @test
     */
    public function testFormatFileSize() {
        $this->assertEquals('1.00 KB', $this->formatter->formatFileSize(1024));
        $this->assertEquals('1.00 MB', $this->formatter->formatFileSize(1048576));
        $this->assertEquals('1.00 GB', $this->formatter->formatFileSize(1073741824));
    }
    
    /**
     * @test
     */
    public function testFormatFileSizeBytes() {
        $this->assertEquals('512 B', $this->formatter->formatFileSize(512));
    }
    
    /**
     * @test
     */
    public function testFormatFileSizeWithPrecision() {
        $result = $this->formatter->formatFileSize(1536, 1); // 1.5 KB
        
        $this->assertEquals('1.5 KB', $result);
    }
    
    /**
     * @test
     */
    public function testFormatDuration() {
        $this->assertEquals('30s', $this->formatter->formatDuration(30));
        $this->assertEquals('2m 30s', $this->formatter->formatDuration(150));
        $this->assertEquals('1h 5m', $this->formatter->formatDuration(3900));
        $this->assertEquals('1d 2h', $this->formatter->formatDuration(93600));
    }
    
    /**
     * @test
     */
    public function testFormatDurationExact() {
        $this->assertEquals('1m', $this->formatter->formatDuration(60));
        $this->assertEquals('1h', $this->formatter->formatDuration(3600));
        $this->assertEquals('1d', $this->formatter->formatDuration(86400));
    }
    
    /**
     * @test
     */
    public function testSmartTruncate() {
        $text = 'This is a very long text that needs truncation';
        $result = $this->formatter->smartTruncate($text, 20);
        
        $this->assertLessThanOrEqual(20, strlen($result));
        $this->assertStringContainsString('...', $result);
    }
    
    /**
     * @test
     */
    public function testSmartTruncateShortText() {
        $text = 'Short text';
        $result = $this->formatter->smartTruncate($text, 20);
        
        $this->assertEquals($text, $result);
    }
    
    /**
     * @test
     */
    public function testSmartTruncateWordBoundary() {
        $text = 'This is a test';
        $result = $this->formatter->smartTruncate($text, 10);
        
        // Should break at word boundary if possible
        $this->assertStringContainsString('...', $result);
        $this->assertLessThanOrEqual(10, strlen($result));
    }
    
    /**
     * @test
     */
    public function testCreateColumnFormatter() {
        $formatter = TableFormatter::createColumnFormatter('currency', [
            'symbol' => '€',
            'decimals' => 2
        ]);
        
        $this->assertIsCallable($formatter);
        $result = $formatter(1234.56);
        $this->assertEquals('€1,234.56', $result);
    }
    
    /**
     * @test
     */
    public function testCreateColumnFormatterPercentage() {
        $formatter = TableFormatter::createColumnFormatter('percentage', [
            'decimals' => 2
        ]);
        
        $result = $formatter(85.567);
        $this->assertEquals('85.57%', $result);
    }
    
    /**
     * @test
     */
    public function testCreateColumnFormatterDate() {
        $formatter = TableFormatter::createColumnFormatter('date', [
            'format' => 'M j, Y'
        ]);
        
        $result = $formatter('2024-01-15');
        $this->assertEquals('Jan 15, 2024', $result);
    }
    
    /**
     * @test
     */
    public function testCreateColumnFormatterFilesize() {
        $formatter = TableFormatter::createColumnFormatter('filesize', [
            'precision' => 1
        ]);
        
        $result = $formatter(1536);
        $this->assertEquals('1.5 KB', $result);
    }
    
    /**
     * @test
     */
    public function testCreateColumnFormatterBoolean() {
        $formatter = TableFormatter::createColumnFormatter('boolean', [
            'true_text' => 'Active',
            'false_text' => 'Inactive'
        ]);
        
        $this->assertEquals('Active', $formatter(true));
        $this->assertEquals('Inactive', $formatter(false));
    }
    
    /**
     * @test
     */
    public function testCreateColumnFormatterNumber() {
        $formatter = TableFormatter::createColumnFormatter('number', [
            'decimals' => 3,
            'thousands_separator' => '.'
        ]);
        
        $result = $formatter(1234.5678);
        $this->assertEquals('1.234.568', $result);
    }
    
    /**
     * @test
     */
    public function testGetAvailableTypes() {
        $types = $this->formatter->getAvailableTypes();
        
        $this->assertIsArray($types);
        $this->assertContains('string', $types);
        $this->assertContains('integer', $types);
        $this->assertContains('float', $types);
        $this->assertContains('date', $types);
        $this->assertContains('boolean', $types);
    }
    
    /**
     * @test
     */
    public function testClearFormatters() {
        $this->formatter->registerFormatter('custom', fn($v) => $v);
        $this->formatter->registerGlobalFormatter(fn($v, $t) => $v);
        
        $result = $this->formatter->clearFormatters();
        
        $this->assertSame($this->formatter, $result);
        // Default formatters should be restored
        $types = $this->formatter->getAvailableTypes();
        $this->assertContains('email', $types);
    }
    
    /**
     * @test
     */
    public function testBuiltInEmailFormatter() {
        $this->formatter->registerFormatter('email', function($value) {
            return filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : (string)$value;
        });
        
        $result = $this->formatter->formatCell('test@example.com', $this->column, 'email');
        $this->assertEquals('test@example.com', $result);
    }
    
    /**
     * @test
     */
    public function testBuiltInStatusFormatter() {
        // Test the status formatter that should be initialized by default
        $result = $this->formatter->formatCell('active', $this->column, 'status');
        $this->assertStringContainsString('Active', $result);
        $this->assertStringContainsString('✅', $result);
    }
}
