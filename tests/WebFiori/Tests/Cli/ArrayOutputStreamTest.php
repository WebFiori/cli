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
        $stream->write('Hello');
        $stream->write(' ');
        $stream->write('World');
        
        $output = $stream->getOutputArray();
        $this->assertCount(3, $output);
        $this->assertEquals(['Hello', ' ', 'World'], $output);
        
        // Test writing with newlines
        $stream->write("\n");
        $stream->write("New line");
        
        $output2 = $stream->getOutputArray();
        $this->assertCount(5, $output2);
        $this->assertEquals(['Hello', ' ', 'World', "\n", 'New line'], $output2);
        
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
        $stream->write(null);
        $output = $stream->getOutputArray();
        $this->assertEquals([''], $output); // null should become empty string
        
        // Test writing numbers
        $stream->reset();
        $stream->write(123);
        $stream->write(45.67);
        $stream->write(true);
        $stream->write(false);
        
        $output2 = $stream->getOutputArray();
        $this->assertEquals(['123', '45.67', '1', ''], $output2);
        
        // Test writing empty strings
        $stream->reset();
        $stream->write('');
        $stream->write('');
        $stream->write('content');
        
        $output3 = $stream->getOutputArray();
        $this->assertEquals(['', '', 'content'], $output3);
        
        // Test writing very long strings
        $longString = str_repeat('x', 10000);
        $stream->reset();
        $stream->write($longString);
        
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
            $arrayOutputStream->write("Performance test line $i\n");
        }
        $outputTime = microtime(true) - $startTime;
        
        $this->assertCount(10000, $arrayOutputStream->getOutputArray());
        $this->assertLessThan(1.0, $outputTime); // Should complete within 1 second
    }
