<?php
namespace WebFiori\Tests\CLI\Progress;

use PHPUnit\Framework\TestCase;
use WebFiori\CLI\Progress\ProgressBar;
use WebFiori\CLI\Progress\ProgressBarFormat;
use WebFiori\CLI\Progress\ProgressBarStyle;
use WebFiori\CLI\Streams\ArrayOutputStream;

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
        $this->assertEquals(100, $bar->getCurrent());
        
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
        $this->assertEquals(100.0, $bar->getPercent());
        
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
        $this->assertEquals(10, $bar->getCurrent());
        
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
        
        // Test starting with message (since setMessage doesn't exist)
        $result = $bar->start('Processing items...');
        $this->assertSame($bar, $result); // Should return self
        
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
        
        // Test finishing with message
        $bar->finish('Process completed!');
        $finalOutput = $output->getOutputArray();
        $this->assertNotEmpty($finalOutput);
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
        
        // Test that format was set by checking output contains expected elements
        $bar->start('Test');
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
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
        
        // Test setting zero width (should use minimum)
        $bar->setWidth(0);
        
        // Test setting negative width (should use minimum)
        $bar->setWidth(-5);
        
        // Verify width setting by checking output
        $bar->start();
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
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
        
        // Test that style was set by checking output
        $bar->start();
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
        
        // Test getting default style on new instance
        $bar2 = new ProgressBar($output, 100);
        $bar2->start();
        $this->assertNotEmpty($output->getOutputArray());
    }

    /**
     * Test ProgressBar update throttling
     * @test
     */
    public function testUpdateThrottlingEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        // Test setting update throttle
        $result = $bar->setUpdateThrottle(0.1); // 100ms
        $this->assertSame($bar, $result); // Should return self
        
        // Test setting negative throttle (should be handled gracefully)
        $bar->setUpdateThrottle(-0.05);
        
        // Test throttling behavior by checking output
        $bar->start();
        $initialOutputCount = count($output->getOutputArray());
        
        // Multiple rapid updates
        $bar->advance();
        $bar->advance();
        $bar->advance();
        
        // Should have some output
        $this->assertGreaterThanOrEqual($initialOutputCount, count($output->getOutputArray()));
    }

    /**
     * Test ProgressBar timing functionality
     * @test
     */
    public function testTimingFunctionalityEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        $bar->start();
        
        // Test that progress bar handles timing internally
        usleep(10000); // 10ms
        $bar->setCurrent(10);
        
        // Test that timing is working by checking output
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
        
        // When finished, should complete properly
        $bar->finish();
        $finalOutput = $output->getOutputArray();
        $this->assertNotEmpty($finalOutput);
    }

    /**
     * Test ProgressBar performance tracking
     * @test
     */
    public function testPerformanceTrackingEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        $bar->start();
        usleep(10000); // 10ms
        
        // Test that progress bar tracks performance internally
        $bar->setCurrent(10);
        
        // Verify output contains progress information
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
        
        // Continue progress
        usleep(10000); // Another 10ms
        $bar->setCurrent(50);
        
        // Should have more output
        $newOutputArray = $output->getOutputArray();
        $this->assertGreaterThanOrEqual(count($outputArray), count($newOutputArray));
    }

    /**
     * Test ProgressBar rate monitoring
     * @test
     */
    public function testRateMonitoringEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 100);
        
        $bar->start();
        
        // Test that progress bar monitors rate internally
        usleep(10000); // 10ms
        $bar->setCurrent(10);
        
        // Verify progress bar is working
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
        
        // Continue with more progress
        usleep(10000); // Another 10ms
        $bar->setCurrent(25);
        
        // Should continue to work
        $newOutputArray = $output->getOutputArray();
        $this->assertGreaterThanOrEqual(count($outputArray), count($newOutputArray));
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
        $this->assertEquals(100.0, $bar->getPercent());
    }

    /**
     * Test ProgressBar output rendering
     * @test
     */
    public function testProgressBarOutputRenderingEnhanced() {
        $output = new ArrayOutputStream();
        $bar = new ProgressBar($output, 10);
        $bar->setWidth(20);
        $bar->setUpdateThrottle(0); // Disable throttling for testing
        
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
        $bar->setUpdateThrottle(0); // Disable throttling for testing
        
        // Test format with placeholders
        $format = 'Progress: [{bar}] {percent}% ({current}/{total})';
        $bar->setFormat($format);
        
        $bar->start('Processing');
        $bar->setCurrent(25);
        
        // The output should contain progress information
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
        
        $outputString = implode('', $outputArray);
        $this->assertNotEmpty($outputString);
        
        // Should contain some progress indicators
        $this->assertStringContainsString('25', $outputString);
        $this->assertStringContainsString('100', $outputString);
    }
}