<?php
namespace WebFiori\Tests\Cli;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Streams\FileOutputStream;
use WebFiori\Cli\Streams\FileInputStream;
use WebFiori\Cli\Exceptions\IOException;

/**
 * Description of FileInputOutputStreamsTest
 *
 * @author i.binalshikh
 */
class FileInputOutputStreamsTest extends TestCase {
    const STREAMS_PATH = ROOT_DIR.'tests'.DS.'Files'.DS;
    
    /**
     * Normalize line endings to make tests work on all platforms
     */
    private function normalizeLineEndings($str) {
        return str_replace(["\r\n", "\r"], "\n", $str);
    }
    
    /**
     * @test
     */
    public function testInputStream00() {
        $stream = new FileInputStream(self::STREAMS_PATH.'stream1.txt');
        $line = $stream->readLine();
        $this->assertEquals("Hello World!\n", $this->normalizeLineEndings($line));
    }
    /**
     * @test
     */
    public function testInputStream01() {
        $this->expectException(IOException::class);
        $this->expectExceptionMessage('Unable to read 1 byte(s) due to an error: "Reached end of file while trying to read 1 byte(s)."');
        $stream = new FileInputStream(self::STREAMS_PATH.'stream1.txt');
        $line = $stream->readLine();
        $this->assertEquals("Hello World!\n", $this->normalizeLineEndings($line));
        $stream->readLine();
    }
    /**
     * @test
     */
    public function testInputStream02() {
        $stream = new FileInputStream(self::STREAMS_PATH.'stream1.txt');
        $this->assertEquals('H', $stream->read());
        $this->assertEquals('el', $stream->read(2));
        $this->assertEquals('l', $stream->read());
        $this->assertEquals('o W', $stream->read(3));
        $this->assertEquals('orld!', $stream->read(5));
        $this->assertEquals("\n", $this->normalizeLineEndings($stream->read()));
    }
    /**
     * @test
     */
    public function testInputStream03() {
        $stream = new FileInputStream(self::STREAMS_PATH.'stream1.txt');
        $this->assertEquals("Hello World!\n", $this->normalizeLineEndings($stream->read(13)));
    }
    /**
     * @test
     */
    public function testInputStream04() {
        $this->expectException(IOException::class);
        $this->expectExceptionMessage('Unable to read 14 byte(s) due to an error: "Reached end of file while trying to read 14 byte(s)."');
        $stream = new FileInputStream(self::STREAMS_PATH.'stream1.txt');
        $this->assertEquals("Hello World!\n", $this->normalizeLineEndings($stream->read(14)));
    }
    /**
     * @test
     */
    public function testInputStream05() {
        $stream = new FileInputStream(self::STREAMS_PATH.'stream2.txt');
        $this->assertEquals("My\n", $this->normalizeLineEndings($stream->readLine()));
        $this->assertEquals("Name Is \n", $this->normalizeLineEndings($stream->readLine()));
        $this->assertEquals("Super", $stream->read(5));
        $this->assertEquals(" Hero Ibrahim\n", $this->normalizeLineEndings($stream->readLine()));
        $this->assertEquals("Even Though I'm Not A Hero\nBut ", $this->normalizeLineEndings($stream->read(31)));
        $this->assertEquals("I'm A\n", $this->normalizeLineEndings($stream->readLine()));
        $this->assertEquals("Hero in Programming\n", $this->normalizeLineEndings($stream->readLine()));
    }
    /**
     * @test
     */
    public function testOutputStream00() {
        $stream = new FileOutputStream(self::STREAMS_PATH.'output-stream1.txt');
        $stream2 = new FileInputStream(self::STREAMS_PATH.'output-stream1.txt');
        $stream->println('Hello World!');
        $this->assertEquals("Hello World!\n", $this->normalizeLineEndings($stream2->readLine()));
    }
    /**
     * @test
     */
    public function testOutputStream01() {
        $stream = new FileOutputStream(self::STREAMS_PATH.'output-stream1.txt');
        $stream2 = new FileInputStream(self::STREAMS_PATH.'output-stream1.txt');
        $stream->prints('Hello Mr %s!', 'Ibrahim');
        $stream->println('');
        $this->assertEquals("Hello Mr Ibrahim!\n", $this->normalizeLineEndings($stream2->readLine()));
    }
    /**
     * @test
     */
    public function testOutputStream02() {
        $stream = new FileOutputStream(self::STREAMS_PATH.'output-stream1.txt');
        $stream2 = new FileInputStream(self::STREAMS_PATH.'output-stream1.txt');
        $stream->prints('Im Cool');
        $stream->println('. You are cool.');
        $this->assertEquals("Im Cool. You are cool.\n", $this->normalizeLineEndings($stream2->readLine()));
    }
}
