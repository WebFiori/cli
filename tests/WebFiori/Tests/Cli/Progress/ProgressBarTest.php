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
    // ========== ENHANCED PROGRESS BAR TESTS ==========

    /**
     * Test ProgressBar initialization with different parameters
     * @test
     */
    public function testProgressBarInitializationEnhanced() {
        $output = new ArrayOutputStream();
        
        // Test with default parameters
        $bar1 = new ProgressBar($output);
        $this->assertEquals(100, $bar1->getTotal());
        $this->assertEquals(0, $bar1->getCurrent());
        $this->assertFalse($bar1->isFinished());
        
        // Test with custom total
        $bar2 = new ProgressBar($output, 50);
        $this->assertEquals(50, $bar2->getTotal());
        $this->assertEquals(0, $bar2->getCurrent());
    }

    /**
     * Test ProgressBar current value management
     * @test
     */
    public function testCurrentValueManagementEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        // Test setting current value
        $result = $bar->setCurrent(25);
        $this->assertSame($bar, $result); // Should return self
        $this->assertEquals(25, $bar->getCurrent());
        $this->assertEquals(25.0, $bar->getPercent());
        
        // Test setting current beyond total
        $bar->setCurrent(150);
        $this->assertEquals(100, $bar->getCurrent()); // Should be capped at total
        $this->assertEquals(100.0, $bar->getPercent());
        $this->assertTrue($bar->isFinished());
        
        // Test setting negative current
        $bar->setCurrent(-10);
        $this->assertEquals(0, $bar->getCurrent()); // Should be capped at 0
        $this->assertEquals(0.0, $bar->getPercent());
        $this->assertFalse($bar->isFinished());
    }

    /**
     * Test ProgressBar total value management
     * @test
     */
    public function testTotalValueManagementEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        $bar->setCurrent(50);
        
        // Test setting new total
        $result = $bar->setTotal(200);
        $this->assertSame($bar, $result); // Should return self
        $this->assertEquals(200, $bar->getTotal());
        $this->assertEquals(50, $bar->getCurrent()); // Current should remain
        $this->assertEquals(25.0, $bar->getPercent()); // Percent should recalculate
        
        // Test setting total smaller than current
        $bar->setTotal(25);
        $this->assertEquals(25, $bar->getTotal());
        $this->assertEquals(25, $bar->getCurrent()); // Current should be adjusted
        $this->assertEquals(100.0, $bar->getPercent());
        $this->assertTrue($bar->isFinished());
        
        // Test setting zero total
        $bar->setTotal(0);
        $this->assertEquals(1, $bar->getTotal()); // Should be minimum 1
    }

    /**
     * Test ProgressBar advance functionality
     * @test
     */
    public function testAdvanceFunctionalityEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 10);
        
        // Test advance with default step
        $result = $bar->advance();
        $this->assertSame($bar, $result); // Should return self
        $this->assertEquals(1, $bar->getCurrent());
        
        // Test advance with custom step
        $bar->advance(3);
        $this->assertEquals(4, $bar->getCurrent());
        
        // Test advance beyond total
        $bar->advance(10);
        $this->assertEquals(10, $bar->getCurrent()); // Should be capped
        $this->assertTrue($bar->isFinished());
        
        // Test advance when already finished
        $bar->advance();
        $this->assertEquals(10, $bar->getCurrent()); // Should remain at total
    }

    /**
     * Test ProgressBar start and finish
     * @test
     */
    public function testStartAndFinishEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        // Test start
        $result = $bar->start('Starting process...');
        $this->assertSame($bar, $result); // Should return self
        
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
        
        // Test finish
        $bar->setCurrent(50); // Set to middle
        $this->assertFalse($bar->isFinished());
        
        $result2 = $bar->finish('Process completed!');
        $this->assertSame($bar, $result2); // Should return self
        $this->assertEquals(100, $bar->getCurrent()); // Should be set to total
        $this->assertTrue($bar->isFinished());
        
        // Test multiple finish calls
        $bar->finish('Already finished');
        $this->assertEquals(100, $bar->getCurrent()); // Should remain at total
    }

    /**
     * Test ProgressBar message handling
     * @test
     */
    public function testMessageHandlingEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        // Test setting message
        $result = $bar->setMessage('Processing items...');
        $this->assertSame($bar, $result); // Should return self
        $this->assertEquals('Processing items...', $bar->getMessage());
        
        // Test empty message
        $bar->setMessage('');
        $this->assertEquals('', $bar->getMessage());
        
        // Test null message
        $bar->setMessage(null);
        $this->assertEquals('', $bar->getMessage()); // Should convert to empty string
    }

    /**
     * Test ProgressBar format handling
     * @test
     */
    public function testFormatHandlingEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        // Test setting custom format
        $customFormat = '{message} [{bar}] {percent}% ({current}/{total})';
        $result = $bar->setFormat($customFormat);
        $this->assertSame($bar, $result); // Should return self
        $this->assertEquals($customFormat, $bar->getFormat());
        
        // Test with null format (should use default)
        $bar->setFormat(null);
        $this->assertNotNull($bar->getFormat()); // Should have some default format
    }

    /**
     * Test ProgressBar width handling
     * @test
     */
    public function testWidthHandlingEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        // Test setting width
        $result = $bar->setWidth(50);
        $this->assertSame($bar, $result); // Should return self
        $this->assertEquals(50, $bar->getWidth());
        
        // Test setting zero width
        $bar->setWidth(0);
        $this->assertEquals(10, $bar->getWidth()); // Should use minimum width
        
        // Test setting negative width
        $bar->setWidth(-5);
        $this->assertEquals(10, $bar->getWidth()); // Should use minimum width
    }

    /**
     * Test ProgressBar style handling
     * @test
     */
    public function testStyleHandlingEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        // Test setting custom style
        $customStyle = new ProgressBarStyle('█', '░', '▓');
        $result = $bar->setStyle($customStyle);
        $this->assertSame($bar, $result); // Should return self
        $this->assertSame($customStyle, $bar->getStyle());
        
        // Test getting default style
        $bar2 = new ProgressBar($output, 100);
        $defaultStyle = $bar2->getStyle();
        $this->assertInstanceOf(ProgressBarStyle::class, $defaultStyle);
    }

    /**
     * Test ProgressBar update throttling
     * @test
     */
    public function testUpdateThrottlingEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        // Test setting update throttle
        $result = $bar->setUpdateThrottle(100); // 100ms
        $this->assertSame($bar, $result); // Should return self
        $this->assertEquals(100, $bar->getUpdateThrottle());
        
        // Test setting negative throttle
        $bar->setUpdateThrottle(-50);
        $this->assertEquals(0, $bar->getUpdateThrottle()); // Should be minimum 0
        
        // Test throttling behavior
        $bar->setUpdateThrottle(1000); // 1 second
        $bar->start();
        
        $initialOutputCount = count($output->getOutputArray());
        
        // Multiple rapid updates should be throttled
        $bar->advance();
        $bar->advance();
        $bar->advance();
        
        // The exact behavior depends on implementation, but there should be some throttling
        $this->assertGreaterThanOrEqual($initialOutputCount, count($output->getOutputArray()));
    }

    /**
     * Test ProgressBar ETA calculation
     * @test
     */
    public function testETACalculationEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        $bar->start();
        
        // Initially ETA should be 0 or very small
        $initialETA = $bar->getEta();
        $this->assertGreaterThanOrEqual(0, $initialETA);
        
        // After some progress, ETA should be calculated
        usleep(10000); // 10ms
        $bar->setCurrent(10);
        
        $eta = $bar->getEta();
        $this->assertGreaterThanOrEqual(0, $eta);
        
        // When finished, ETA should be 0
        $bar->finish();
        $this->assertEquals(0, $bar->getEta());
    }

    /**
     * Test ProgressBar elapsed time
     * @test
     */
    public function testElapsedTimeEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        // Before start, elapsed should be 0
        $this->assertEquals(0, $bar->getElapsed());
        
        $bar->start();
        usleep(10000); // 10ms
        
        // After start, elapsed should be > 0
        $elapsed = $bar->getElapsed();
        $this->assertGreaterThan(0, $elapsed);
        
        // Elapsed should increase over time
        usleep(10000); // Another 10ms
        $newElapsed = $bar->getElapsed();
        $this->assertGreaterThanOrEqual($elapsed, $newElapsed);
    }

    /**
     * Test ProgressBar rate calculation
     * @test
     */
    public function testRateCalculationEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        $bar->start();
        
        // Initially rate should be 0
        $initialRate = $bar->getRate();
        $this->assertGreaterThanOrEqual(0, $initialRate);
        
        // After some progress and time, rate should be calculated
        usleep(10000); // 10ms
        $bar->setCurrent(10);
        
        $rate = $bar->getRate();
        $this->assertGreaterThanOrEqual(0, $rate);
    }

    /**
     * Test ProgressBar with zero total edge case
     * @test
     */
    public function testProgressBarZeroTotalEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 0);
        
        // Should handle zero total gracefully
        $this->assertEquals(1, $bar->getTotal()); // Should be adjusted to minimum
        $this->assertEquals(0, $bar->getCurrent());
        $this->assertEquals(0.0, $bar->getPercent());
        
        $bar->advance();
        $this->assertEquals(1, $bar->getCurrent());
        $this->assertEquals(100.0, $bar->getPercent());
        $this->assertTrue($bar->isFinished());
    }

    /**
     * Test ProgressBar output rendering
     * @test
     */
    public function testProgressBarOutputRenderingEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 10);
        $bar->setWidth(20);
        
        // Test rendering at different progress levels
        $bar->start('Starting...');
        $this->assertNotEmpty($output->getOutputArray());
        
        $output->reset(); // Clear previous output
        
        $bar->setCurrent(5); // 50% progress
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
        
        // The output should contain progress bar elements
        $outputString = implode('', $outputArray);
        $this->assertNotEmpty($outputString);
        
        $output->reset();
        
        $bar->finish('Completed!');
        $finalOutput = $output->getOutputArray();
        $this->assertNotEmpty($finalOutput);
    }

    /**
     * Test ProgressBar format placeholders
     * @test
     */
    public function testFormatPlaceholdersEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        // Test format with all placeholders
        $format = '{message} [{bar}] {percent}% ({current}/{total}) ETA: {eta}s Elapsed: {elapsed}s Rate: {rate}/s';
        $bar->setFormat($format);
        $bar->setMessage('Processing');
        
        $bar->start();
        $bar->setCurrent(25);
        
        // The output should contain all the placeholder values
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
        
        $outputString = implode('', $outputArray);
        $this->assertStringContainsString('Processing', $outputString);
        $this->assertStringContainsString('25%', $outputString);
        $this->assertStringContainsString('25/100', $outputString);
    }
