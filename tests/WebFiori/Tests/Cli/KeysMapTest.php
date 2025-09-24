<?php
namespace WebFiori\Tests\Cli;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\KeysMap;
use WebFiori\Cli\Streams\ArrayInputStream;

/**
 * Test cases for KeysMap class arrow key handling.
 */
class KeysMapTest extends TestCase {
    
    /**
     * Test that arrow key escape sequences are properly detected.
     */
    public function testArrowKeyDetection() {
        // Test UP arrow
        $inputStream = new ArrayInputStream(["\033[A"]);
        $result = KeysMap::readAndTranslate($inputStream);
        $this->assertEquals('UP', $result);
        
        // Test DOWN arrow
        $inputStream = new ArrayInputStream(["\033[B"]);
        $result = KeysMap::readAndTranslate($inputStream);
        $this->assertEquals('DOWN', $result);
        
        // Test RIGHT arrow
        $inputStream = new ArrayInputStream(["\033[C"]);
        $result = KeysMap::readAndTranslate($inputStream);
        $this->assertEquals('RIGHT', $result);
        
        // Test LEFT arrow
        $inputStream = new ArrayInputStream(["\033[D"]);
        $result = KeysMap::readAndTranslate($inputStream);
        $this->assertEquals('LEFT', $result);
    }
    
    /**
     * Test that arrow keys don't appear in readline input.
     */
    public function testArrowKeysIgnoredInReadLine() {
        // Input with arrow keys mixed with regular text
        $inputStream = new ArrayInputStream(["\033[A\033[Bhello\033[C\033[D\n"]);
        $result = KeysMap::readLine($inputStream);
        
        // Should only contain "hello", arrow keys should be ignored
        $this->assertEquals('hello', $result);
    }
    
    /**
     * Test that regular characters still work normally.
     */
    public function testRegularCharacters() {
        $inputStream = new ArrayInputStream(["hello world\n"]);
        $result = KeysMap::readLine($inputStream);
        
        $this->assertEquals('hello world', $result);
    }
    
    /**
     * Test backspace functionality still works.
     */
    public function testBackspaceWithArrowKeys() {
        // Type "hello", backspace once, arrow keys (ignored), type "p"
        $inputStream = new ArrayInputStream(["hello\177\033[A\033[Bp\n"]);
        $result = KeysMap::readLine($inputStream);
        
        // Should be "hellp" (hello -> hell -> hell -> hell -> hellp)
        $this->assertEquals('hellp', $result);
    }
    
    /**
     * Test ESC key handling.
     */
    public function testEscapeKey() {
        $inputStream = new ArrayInputStream(["\ehello\n"]);
        $result = KeysMap::readAndTranslate($inputStream);
        $this->assertEquals('ESC', $result);
    }
}
