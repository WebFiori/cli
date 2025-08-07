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
}
