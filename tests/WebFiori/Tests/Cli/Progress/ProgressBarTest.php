<?php
namespace WebFiori\Tests\Cli\Progress;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Progress\ProgressBar;
use WebFiori\Cli\Progress\ProgressBarFormat;
use WebFiori\Cli\Progress\ProgressBarStyle;
use WebFiori\Cli\Streams\ArrayOutputStream;

/**
 * Test cases for ProgressBar class.
 */
class ProgressBarTest extends TestCase {
    private ArrayOutputStream $output;
    
    protected function setUp(): void {
        $this->output = new ArrayOutputStream();
    }
    
    /**
     * @test
     */
    public function testConstructorDefaults() {
        $progressBar = new ProgressBar($this->output);
        
        $this->assertEquals(0, $progressBar->getCurrent());
        $this->assertEquals(100, $progressBar->getTotal());
        $this->assertEquals(0.0, $progressBar->getPercent());
        $this->assertFalse($progressBar->isFinished());
    }
    
    /**
     * @test
     */
    public function testConstructorWithTotal() {
        $progressBar = new ProgressBar($this->output, 50);
        
        $this->assertEquals(0, $progressBar->getCurrent());
        $this->assertEquals(50, $progressBar->getTotal());
        $this->assertEquals(0.0, $progressBar->getPercent());
    }
    
    /**
     * @test
     */
    public function testConstructorWithZeroTotal() {
        $progressBar = new ProgressBar($this->output, 0);
        
        // Should default to 1 to avoid division by zero
        $this->assertEquals(1, $progressBar->getTotal());
    }
    
    /**
     * @test
     */
    public function testSetCurrent() {
        $progressBar = new ProgressBar($this->output, 100);
        $result = $progressBar->setCurrent(25);
        
        $this->assertSame($progressBar, $result); // Test fluent interface
        $this->assertEquals(25, $progressBar->getCurrent());
        $this->assertEquals(25.0, $progressBar->getPercent());
    }
    
    /**
     * @test
     */
    public function testSetCurrentBeyondTotal() {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->setCurrent(150);
        
        // Should be clamped to total
        $this->assertEquals(100, $progressBar->getCurrent());
        $this->assertEquals(100.0, $progressBar->getPercent());
    }
    
    /**
     * @test
     */
    public function testSetCurrentNegative() {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->setCurrent(-10);
        
        // Should be clamped to 0
        $this->assertEquals(0, $progressBar->getCurrent());
        $this->assertEquals(0.0, $progressBar->getPercent());
    }
    
    /**
     * @test
     */
    public function testAdvance() {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->setCurrent(10);
        
        $result = $progressBar->advance();
        $this->assertSame($progressBar, $result); // Test fluent interface
        $this->assertEquals(11, $progressBar->getCurrent());
        
        $progressBar->advance(5);
        $this->assertEquals(16, $progressBar->getCurrent());
    }
    
    /**
     * @test
     */
    public function testStart() {
        $progressBar = new ProgressBar($this->output, 100);
        $result = $progressBar->start('Processing...');
        
        $this->assertSame($progressBar, $result); // Test fluent interface
        $this->assertEquals(0, $progressBar->getCurrent());
        $this->assertFalse($progressBar->isFinished());
        
        // Should have output
        $this->assertNotEmpty($this->output->getOutputArray());
    }
    
    /**
     * @test
     */
    public function testFinish() {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->start();
        $progressBar->setCurrent(50);
        
        $result = $progressBar->finish('Complete!');
        
        $this->assertSame($progressBar, $result); // Test fluent interface
        $this->assertEquals(100, $progressBar->getCurrent());
        $this->assertEquals(100.0, $progressBar->getPercent());
        $this->assertTrue($progressBar->isFinished());
    }
    
    /**
     * @test
     */
    public function testFinishMultipleTimes() {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->start();
        
        $progressBar->finish();
        $this->assertTrue($progressBar->isFinished());
        
        // Should not change state on second finish
        $progressBar->finish();
        $this->assertTrue($progressBar->isFinished());
        $this->assertEquals(100, $progressBar->getCurrent());
    }
    
    /**
     * @test
     */
    public function testSetTotal() {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->setCurrent(50);
        
        $result = $progressBar->setTotal(200);
        
        $this->assertSame($progressBar, $result); // Test fluent interface
        $this->assertEquals(200, $progressBar->getTotal());
        $this->assertEquals(50, $progressBar->getCurrent());
        $this->assertEquals(25.0, $progressBar->getPercent());
    }
    
    /**
     * @test
     */
    public function testSetTotalSmallerThanCurrent() {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->setCurrent(50);
        $progressBar->setTotal(25);
        
        // Current should be clamped to new total
        $this->assertEquals(25, $progressBar->getTotal());
        $this->assertEquals(25, $progressBar->getCurrent());
        $this->assertEquals(100.0, $progressBar->getPercent());
    }
    
    /**
     * @test
     */
    public function testSetTotalZero() {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->setTotal(0);
        
        // Should default to 1
        $this->assertEquals(1, $progressBar->getTotal());
    }
    
    /**
     * @test
     */
    public function testSetWidth() {
        $progressBar = new ProgressBar($this->output, 100);
        $result = $progressBar->setWidth(30);
        
        $this->assertSame($progressBar, $result); // Test fluent interface
    }
    
    /**
     * @test
     */
    public function testSetWidthZero() {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->setWidth(0);
        
        // Should default to 1
        // We can't directly test width, but we can test that it doesn't crash
        $progressBar->start();
        $this->assertNotEmpty($this->output->getOutputArray());
    }
    
    /**
     * @test
     */
    public function testSetStyle() {
        $progressBar = new ProgressBar($this->output, 100);
        $style = new ProgressBarStyle('=', '-', '>');
        
        $result = $progressBar->setStyle($style);
        $this->assertSame($progressBar, $result); // Test fluent interface
    }
    
    /**
     * @test
     */
    public function testSetStyleByName() {
        $progressBar = new ProgressBar($this->output, 100);
        $result = $progressBar->setStyle(ProgressBarStyle::ASCII);
        
        $this->assertSame($progressBar, $result); // Test fluent interface
    }
    
    /**
     * @test
     */
    public function testSetFormat() {
        $progressBar = new ProgressBar($this->output, 100);
        $result = $progressBar->setFormat('[{bar}] {percent}%');
        
        $this->assertSame($progressBar, $result); // Test fluent interface
    }
    
    /**
     * @test
     */
    public function testSetUpdateThrottle() {
        $progressBar = new ProgressBar($this->output, 100);
        $result = $progressBar->setUpdateThrottle(0.5);
        
        $this->assertSame($progressBar, $result); // Test fluent interface
    }
    
    /**
     * @test
     */
    public function testSetUpdateThrottleNegative() {
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->setUpdateThrottle(-1);
        
        // Should not crash - negative values should be handled
        $progressBar->start();
        $this->assertNotEmpty($this->output->getOutputArray());
    }
    
    /**
     * @test
     */
    public function testSetOverwrite() {
        $progressBar = new ProgressBar($this->output, 100);
        $result = $progressBar->setOverwrite(false);
        
        $this->assertSame($progressBar, $result); // Test fluent interface
    }
    
    /**
     * @test
     */
    public function testProgressBarOutput() {
        $progressBar = new ProgressBar($this->output, 10);
        $progressBar->setWidth(10);
        $progressBar->setFormat('[{bar}] {percent}%');
        $progressBar->setStyle(ProgressBarStyle::ASCII);
        $progressBar->setUpdateThrottle(0); // No throttling for tests
        
        $progressBar->start();
        $progressBar->setCurrent(5);
        $progressBar->finish();
        
        $output = $this->output->getOutputArray();
        $this->assertNotEmpty($output);
        
        // Should contain progress bar elements
        $lastOutput = end($output);
        $this->assertStringContainsString('[', $lastOutput);
        $this->assertStringContainsString(']', $lastOutput);
        $this->assertStringContainsString('%', $lastOutput);
    }
    
    /**
     * @test
     */
    public function testProgressBarWithMessage() {
        $progressBar = new ProgressBar($this->output, 10);
        $progressBar->setUpdateThrottle(0); // No throttling for tests
        
        $progressBar->start('Loading...');
        
        $output = $this->output->getOutputArray();
        $this->assertNotEmpty($output);
        
        $firstOutput = $output[0];
        $this->assertStringContainsString('Loading...', $firstOutput);
    }
}
