<?php
namespace WebFiori\Tests\Cli;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Streams\ArrayOutputStream;
/**
 * Description of ArrayInputStreamTest
 *
 * @author Ibrahim
 */
class ArrayOutputStreamTest extends TestCase {
    /**
     * @test
     */
    public function test00() {
        $stream = new ArrayOutputStream();
        $this->assertEquals([], $stream->getOutputArray());
        $stream->println('Hello');
        $this->assertEquals([
            "Hello\n"
        ], $stream->getOutputArray());
        $stream->prints(' World!');
        $this->assertEquals([
            "Hello\n",
            " World!",
        ], $stream->getOutputArray());
        $stream->println('Good');
        $this->assertEquals([
            "Hello\n",
            " World!Good\n",
        ], $stream->getOutputArray());
        $stream->reset();
        $this->assertEquals([], $stream->getOutputArray());
    }
    // ========== ENHANCED ARRAY OUTPUT STREAM TESTS ==========

    /**
     * Test ArrayOutputStream comprehensive functionality
     * @test
     */
    public function testArrayOutputStreamComprehensiveEnhanced() {
        $stream = new ArrayOutputStream();
        
        // Test initial state
        $this->assertEmpty($stream->getOutputArray());
        
        // Test writing strings
        $stream->prints('Hello');
        $stream->prints(' ');
        $stream->prints('World');
        
        $output = $stream->getOutputArray();
        $this->assertNotEmpty($output);
        $this->assertEquals(['Hello World'], $output);
        
        // Test writing with println to create separate entries
        $stream->println('');  // This creates a new line and separates entries
        $stream->prints('New line');
        
        $output2 = $stream->getOutputArray();
        $this->assertCount(2, $output2);
        $this->assertEquals(["Hello World\n", 'New line'], $output2);
        
        // Test clearing output
        $stream->reset();
        $this->assertEmpty($stream->getOutputArray());
    }

    /**
     * Test ArrayOutputStream edge cases
     * @test
     */
    public function testArrayOutputStreamEdgeCasesEnhanced() {
        $stream = new ArrayOutputStream();
        
        // Test writing null
        $stream->prints("");
        $output = $stream->getOutputArray();
        $this->assertEquals([''], $output); // null should become empty string
        
        // Test writing numbers
        $stream->reset();
        $stream->prints(123);
        $stream->prints(45.67);
        $stream->prints(true);
        $stream->prints(false);
        
        $output2 = $stream->getOutputArray();
        $this->assertEquals(['12345.671'], $output2);
        
        // Test writing empty strings - consecutive prints calls are concatenated
        $stream->reset();
        $stream->prints('');
        $stream->prints('');
        $stream->prints('content');
        
        $output3 = $stream->getOutputArray();
        $this->assertEquals(['content'], $output3);
        
        // Test writing very long strings
        $longString = str_repeat('x', 10000);
        $stream->reset();
        $stream->prints($longString);
        
        $output4 = $stream->getOutputArray();
        $this->assertEquals([$longString], $output4);
    }

    /**
     * Test ArrayOutputStream performance
     * @test
     */
    public function testArrayOutputStreamPerformanceEnhanced() {
        // Test ArrayOutputStream performance
        $arrayOutputStream = new ArrayOutputStream();
        
        $startTime = microtime(true);
        for ($i = 0; $i < 10000; $i++) {
            $arrayOutputStream->prints("Performance test line $i\n");
        }
        $outputTime = microtime(true) - $startTime;
        
        $this->assertNotEmpty($arrayOutputStream->getOutputArray());
        $this->assertLessThan(1.0, $outputTime); // Should complete within 1 second
    }
}