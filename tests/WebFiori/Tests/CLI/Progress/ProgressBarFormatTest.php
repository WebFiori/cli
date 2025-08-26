<?php
namespace WebFiori\Tests\CLI\Progress;

use PHPUnit\Framework\TestCase;
use WebFiori\CLI\Progress\ProgressBarFormat;

/**
 * Test cases for ProgressBarFormat class.
 */
class ProgressBarFormatTest extends TestCase {
    
    /**
     * @test
     */
    public function testDefaultConstructor() {
        $format = new ProgressBarFormat();
        
        $this->assertEquals(ProgressBarFormat::DEFAULT_FORMAT, $format->getFormat());
    }
    
    /**
     * @test
     */
    public function testCustomConstructor() {
        $customFormat = '[{bar}] {percent}%';
        $format = new ProgressBarFormat($customFormat);
        
        $this->assertEquals($customFormat, $format->getFormat());
    }
    
    /**
     * @test
     */
    public function testSetFormat() {
        $format = new ProgressBarFormat();
        $newFormat = '{current}/{total} [{bar}]';
        $result = $format->setFormat($newFormat);
        
        $this->assertSame($format, $result); // Test fluent interface
        $this->assertEquals($newFormat, $format->getFormat());
    }
    
    /**
     * @test
     */
    public function testRenderBasic() {
        $format = new ProgressBarFormat('[{bar}] {percent}%');
        $values = [
            'bar' => '████░░░░░░',
            'percent' => '40.0'
        ];
        
        $result = $format->render($values);
        $this->assertEquals('[████░░░░░░] 40.0%', $result);
    }
    
    /**
     * @test
     */
    public function testRenderWithMissingValues() {
        $format = new ProgressBarFormat('[{bar}] {percent}% {missing}');
        $values = [
            'bar' => '████░░░░░░',
            'percent' => '40.0'
        ];
        
        $result = $format->render($values);
        $this->assertEquals('[████░░░░░░] 40.0% {missing}', $result);
    }
    
    /**
     * @test
     */
    public function testGetPlaceholders() {
        $format = new ProgressBarFormat('[{bar}] {percent}% ({current}/{total}) ETA: {eta}');
        $placeholders = $format->getPlaceholders();
        
        $expected = ['bar', 'percent', 'current', 'total', 'eta'];
        $this->assertEquals($expected, $placeholders);
    }
    
    /**
     * @test
     */
    public function testGetPlaceholdersEmpty() {
        $format = new ProgressBarFormat('No placeholders here');
        $placeholders = $format->getPlaceholders();
        
        $this->assertEquals([], $placeholders);
    }
    
    /**
     * @test
     */
    public function testHasPlaceholder() {
        $format = new ProgressBarFormat('[{bar}] {percent}%');
        
        $this->assertTrue($format->hasPlaceholder('bar'));
        $this->assertTrue($format->hasPlaceholder('percent'));
        $this->assertFalse($format->hasPlaceholder('eta'));
        $this->assertFalse($format->hasPlaceholder('missing'));
    }
    
    /**
     * @test
     */
    public function testFormatDurationSeconds() {
        $this->assertEquals('00:05', ProgressBarFormat::formatDuration(5));
        $this->assertEquals('00:30', ProgressBarFormat::formatDuration(30));
        $this->assertEquals('01:00', ProgressBarFormat::formatDuration(60));
    }
    
    /**
     * @test
     */
    public function testFormatDurationMinutes() {
        $this->assertEquals('02:30', ProgressBarFormat::formatDuration(150));
        $this->assertEquals('10:00', ProgressBarFormat::formatDuration(600));
        $this->assertEquals('59:59', ProgressBarFormat::formatDuration(3599));
    }
    
    /**
     * @test
     */
    public function testFormatDurationHours() {
        $this->assertEquals('01:00:00', ProgressBarFormat::formatDuration(3600));
        $this->assertEquals('02:30:45', ProgressBarFormat::formatDuration(9045));
        $this->assertEquals('24:00:00', ProgressBarFormat::formatDuration(86400));
    }
    
    /**
     * @test
     */
    public function testFormatDurationNegative() {
        $this->assertEquals('--:--', ProgressBarFormat::formatDuration(-1));
        $this->assertEquals('--:--', ProgressBarFormat::formatDuration(-100));
    }
    
    /**
     * @test
     */
    public function testFormatMemoryBytes() {
        $this->assertEquals('512.0B', ProgressBarFormat::formatMemory(512));
        $this->assertEquals('1023.0B', ProgressBarFormat::formatMemory(1023));
    }
    
    /**
     * @test
     */
    public function testFormatMemoryKilobytes() {
        $this->assertEquals('1.0KB', ProgressBarFormat::formatMemory(1024));
        $this->assertEquals('2.5KB', ProgressBarFormat::formatMemory(2560));
        $this->assertEquals('1023.0KB', ProgressBarFormat::formatMemory(1047552));
    }
    
    /**
     * @test
     */
    public function testFormatMemoryMegabytes() {
        $this->assertEquals('1.0MB', ProgressBarFormat::formatMemory(1048576));
        $this->assertEquals('2.5MB', ProgressBarFormat::formatMemory(2621440));
    }
    
    /**
     * @test
     */
    public function testFormatMemoryGigabytes() {
        $this->assertEquals('1.0GB', ProgressBarFormat::formatMemory(1073741824));
        $this->assertEquals('2.5GB', ProgressBarFormat::formatMemory(2684354560));
    }
    
    /**
     * @test
     */
    public function testFormatRateSmall() {
        $this->assertEquals('0.50', ProgressBarFormat::formatRate(0.5));
        $this->assertEquals('0.75', ProgressBarFormat::formatRate(0.75));
    }
    
    /**
     * @test
     */
    public function testFormatRateMedium() {
        $this->assertEquals('5.5', ProgressBarFormat::formatRate(5.5));
        $this->assertEquals('9.9', ProgressBarFormat::formatRate(9.9));
    }
    
    /**
     * @test
     */
    public function testFormatRateLarge() {
        $this->assertEquals('10', ProgressBarFormat::formatRate(10));
        $this->assertEquals('100', ProgressBarFormat::formatRate(100));
        $this->assertEquals('1000', ProgressBarFormat::formatRate(1000));
    }
}
