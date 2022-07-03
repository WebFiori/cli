<?php
namespace webfiori\tests\cli;

use webfiori\cli\KeysMap;
use PHPUnit\Framework\TestCase;
use webfiori\cli\streams\ArrayInputStream;
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
            "\e"
        ]);
        $this->assertEquals(" ", KeysMap::read($stream));
    }
    /**
     * @test
     */
    public function test01() {
        $stream = new ArrayInputStream([
            "\r"
        ]);
        $this->assertEquals(" ", KeysMap::read($stream));
    }
    /**
     * @test
     */
    public function test02() {
        $stream = new ArrayInputStream([
            "\r\n"
        ]);
        $this->assertEquals(" ", KeysMap::read($stream));
    }
}
