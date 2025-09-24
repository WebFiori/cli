<?php
namespace WebFiori\Tests\Cli\Progress;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Command;
use WebFiori\Cli\Progress\ProgressBar;
use WebFiori\Cli\Streams\ArrayOutputStream;

/**
 * Test cases for Command progress bar integration.
 */
class CommandProgressTest extends TestCase {
    
    /**
     * @test
     */
    public function testCreateProgressBar() {
        $command = new TestProgressCommand();
        $output = new ArrayOutputStream();
        $command->setOutputStream($output);
        
        $progressBar = $command->createProgressBar(50);
        
        $this->assertInstanceOf(ProgressBar::class, $progressBar);
        $this->assertEquals(0, $progressBar->getCurrent());
        $this->assertEquals(50, $progressBar->getTotal());
    }
    
    /**
     * @test
     */
    public function testCreateProgressBarDefault() {
        $command = new TestProgressCommand();
        $output = new ArrayOutputStream();
        $command->setOutputStream($output);
        
        $progressBar = $command->createProgressBar();
        
        $this->assertInstanceOf(ProgressBar::class, $progressBar);
        $this->assertEquals(100, $progressBar->getTotal());
    }
    
    /**
     * @test
     */
    public function testWithProgressBarArray() {
        $command = new TestProgressCommand();
        $output = new ArrayOutputStream();
        $command->setOutputStream($output);
        
        $items = [1, 2, 3, 4, 5];
        $processed = [];
        
        $command->withProgressBar($items, function($item, $key) use (&$processed) {
            $processed[] = $item;
        });
        
        $this->assertEquals($items, $processed);
        
        // Should have output from progress bar
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
    }
    
    /**
     * @test
     */
    public function testWithProgressBarIterator() {
        $command = new TestProgressCommand();
        $output = new ArrayOutputStream();
        $command->setOutputStream($output);
        
        $items = new \ArrayIterator([10, 20, 30]);
        $processed = [];
        
        $command->withProgressBar($items, function($item, $key) use (&$processed) {
            $processed[] = $item;
        });
        
        $this->assertEquals([10, 20, 30], $processed);
        
        // Should have output from progress bar
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
    }
    
    /**
     * @test
     */
    public function testWithProgressBarWithMessage() {
        $command = new TestProgressCommand();
        $output = new ArrayOutputStream();
        $command->setOutputStream($output);
        
        $items = ['a', 'b', 'c'];
        $processed = [];
        
        $command->withProgressBar($items, function($item, $key) use (&$processed) {
            $processed[] = $item;
        }, 'Processing items...');
        
        $this->assertEquals($items, $processed);
        
        // Should have output with message
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
        
        $firstOutput = $outputArray[0];
        $this->assertStringContainsString('Processing items...', $firstOutput);
    }
    
    /**
     * @test
     */
    public function testWithProgressBarEmptyArray() {
        $command = new TestProgressCommand();
        $output = new ArrayOutputStream();
        $command->setOutputStream($output);
        
        $items = [];
        $processed = [];
        
        $command->withProgressBar($items, function($item, $key) use (&$processed) {
            $processed[] = $item;
        });
        
        $this->assertEquals([], $processed);
        
        // Should still have some output (start and finish)
        $outputArray = $output->getOutputArray();
        $this->assertNotEmpty($outputArray);
    }
    
    /**
     * @test
     */
    public function testWithProgressBarCallbackReceivesKeyAndValue() {
        $command = new TestProgressCommand();
        $output = new ArrayOutputStream();
        $command->setOutputStream($output);
        
        $items = ['first' => 'a', 'second' => 'b', 'third' => 'c'];
        $processedKeys = [];
        $processedValues = [];
        
        $command->withProgressBar($items, function($item, $key) use (&$processedKeys, &$processedValues) {
            $processedKeys[] = $key;
            $processedValues[] = $item;
        });
        
        $this->assertEquals(['first', 'second', 'third'], $processedKeys);
        $this->assertEquals(['a', 'b', 'c'], $processedValues);
    }
}

/**
 * Test command for progress bar testing.
 */
class TestProgressCommand extends Command {
    public function __construct() {
        parent::__construct('test-progress');
    }
    
    public function exec(): int {
        return 0;
    }
}
