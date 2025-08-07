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
