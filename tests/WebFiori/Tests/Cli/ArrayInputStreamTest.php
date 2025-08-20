<?php
namespace WebFiori\Tests\Cli;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Streams\ArrayInputStream;
/**
 * Description of ArrayInputStreamTest
 *
 * @author Ibrahim
 */
class ArrayInputStreamTest extends TestCase {
    /**
     * @test
     */
    public function test00() {
        $stream = new ArrayInputStream([
            'one',
            'two'
        ]);
        $this->assertEquals('o', $stream->read(1));
        $this->assertEquals('ne', $stream->readLine());
        $this->assertEquals('two', $stream->readLine());
    }
    /**
     * @test
     */
    public function test01() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reached end of stream while trying to read line number 3');
        $stream = new ArrayInputStream([
            'one',
            'two'
        ]);
        $this->assertEquals('on', $stream->read(2));
        $this->assertEquals('e', $stream->readLine());
        $this->assertEquals('two', $stream->readLine());
        $stream->readLine();
    }
    /**
     * @test
     */
    public function test02() {
        $stream = new ArrayInputStream([
            'one',
            'two',
            'Super cool',
            'Multi line byte read',
            'ok'
        ]);
        $this->assertEquals('on', $stream->read(2));
        $this->assertEquals('e', $stream->readLine());
        $this->assertEquals('two', $stream->readLine());
        $this->assertEquals('Super coolM', $stream->read(11));
        $this->assertEquals('ul', $stream->read(2));
        $this->assertEquals('t', $stream->read(1));
        $this->assertEquals('i line byte read', $stream->readLine());
        $this->assertEquals('ok', $stream->readLine());
    }
    /**
     * @test
     */
    public function test03() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reached end of stream while trying to read 1 byte(s).');
        $stream = new ArrayInputStream([
            'one',
            'two'
        ]);
        $this->assertEquals('on', $stream->read(2));
        $this->assertEquals('e', $stream->readLine());
        $this->assertEquals('t', $stream->read());
        $this->assertEquals('w', $stream->read());
        $this->assertEquals('o', $stream->read());
        
        $stream->read();
    }
    /**
     * @test
     */
    public function test04() {
        $stream = new ArrayInputStream([
            'on',
            'tw',
        ]);
        $this->assertEquals('ontw', $stream->read(4));
    }
    /**
     * @test
     */
    public function test05() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reached end of stream while trying to read 10 byte(s).');
        
        $stream = new ArrayInputStream([
            'on',
            'tw',
            'three'
        ]);
        $this->assertEquals('ontwthree', $stream->read(10));
    }
    /**
     * @test
     */
    public function test06() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bytes must be positive number.');       
        $stream = new ArrayInputStream([
            'on',
        ]);
        $this->assertEquals('', $stream->read(-1));
    }
    /**
     * @test
     */
    public function test07() {
        $stream = new ArrayInputStream([
            'on',
            'tw',
        ]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reached end of stream while trying to read line number 3');  
        $this->assertEquals('ontw', $stream->read(4));
        $this->assertEquals('  ', $stream->readLine());
    }
    // ========== ENHANCED ARRAY INPUT STREAM TESTS ==========

    /**
     * Test ArrayInputStream comprehensive functionality
     * @test
     */
    public function testArrayInputStreamComprehensiveEnhanced() {
        $inputs = ['line1', 'line2', 'line3', ''];
        $stream = new ArrayInputStream($inputs);
        
        // Test reading lines
        $this->assertEquals('line1', $stream->readLine());
        $this->assertEquals('line2', $stream->readLine());
        $this->assertEquals('line3', $stream->readLine());
        $this->assertEquals('', $stream->readLine());
        
        // Test reading beyond available inputs
        // Test reading beyond available inputs (should throw exception)
        $this->expectException(\InvalidArgumentException::class);
        $stream->readLine(); // Should throw exception
        
        // Test reading with byte limit
        $stream2 = new ArrayInputStream(['hello world']);
        $this->assertEquals('hello', $stream2->read(5));
        $this->assertEquals(' worl', $stream2->read(5));
        $this->assertEquals('d', $stream2->read(5)); // Remaining characters
        
        // Test reading beyond available data
        $this->assertEquals('', $stream2->read(5)); // Should return empty string
    }

    /**
     * Test ArrayInputStream edge cases
     * @test
     */
    public function testArrayInputStreamEdgeCasesEnhanced() {
        $this->expectException(\InvalidArgumentException::class);
        $emptyStream->readLine(); // Should throw exception
        $emptyStream = new ArrayInputStream([]);
        $this->assertEquals('', $emptyStream->readLine());
        $this->assertEquals('', $emptyStream->read(10));
        
        // Test with null values in array
        $nullStream = new ArrayInputStream([null, 'valid', null]);
        $this->assertEquals('', $nullStream->readLine()); // null should become empty string
        $this->assertEquals('valid', $nullStream->readLine());
        $this->assertEquals('', $nullStream->readLine()); // null should become empty string
        
        // Test with numeric values
        $numericStream = new ArrayInputStream([123, 45.67, true, false]);
        $this->assertEquals('123', $numericStream->readLine());
        $this->assertEquals('45.67', $numericStream->readLine());
        $this->assertEquals('1', $numericStream->readLine()); // true becomes '1'
        $this->assertEquals('', $numericStream->readLine()); // false becomes ''
        
        // Test with very long strings
        $longString = str_repeat('a', 10000);
        $longStream = new ArrayInputStream([$longString]);
        $this->assertEquals($longString, $longStream->readLine());
    }

    /**
     * Test ArrayInputStream performance with large data
     * @test
     */
    public function testArrayInputStreamPerformanceEnhanced() {
        // Test ArrayInputStream performance
        $largeInputArray = array_fill(0, 10000, 'Performance test line');
        $arrayStream = new ArrayInputStream($largeInputArray);
        try {
            while ($arrayStream->readLine() !== "") {
                $lineCount++;
            }
        } catch (\InvalidArgumentException $e) {
            // Expected when reaching end of stream
        }
        $startTime = microtime(true);
        $lineCount = 0;
        while ($arrayStream->readLine() !== '') {
            $lineCount++;
        }
        $arrayTime = microtime(true) - $startTime;
        
        $this->assertEquals(10000, $lineCount);
        $this->assertLessThan(1.0, $arrayTime); // Should complete within 1 second
    }
}