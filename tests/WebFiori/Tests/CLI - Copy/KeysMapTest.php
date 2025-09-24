<?php
namespace WebFiori\Tests\Cli;

use PHPUnit\Framework\TestCase;
use WebFiori\CLI\KeysMap;
use WebFiori\CLI\Streams\ArrayInputStream;
/**
 * Description of KeysMapTest
 *
 * @author Ibrahim
 */
class KeysMapTest extends TestCase {
    /**
     * @test
     */
    public function test00() {
        $stream = new ArrayInputStream([
            chr(27) // ESC character
        ]);
        $this->assertEquals("ESC", KeysMap::readAndTranslate($stream));
    }
    /**
     * @test
     */
    public function test01() {
        $stream = new ArrayInputStream([
            "\r"
        ]);
        $this->assertEquals("CR", KeysMap::readAndTranslate($stream));
    }
    /**
     * @test
     */
    public function test02() {
        $stream = new ArrayInputStream([
            "\r",
            "\n"
        ]);
        $this->assertEquals("CR", KeysMap::readAndTranslate($stream));
        $this->assertEquals("LF", KeysMap::readAndTranslate($stream));
    }
}
