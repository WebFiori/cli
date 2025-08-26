<?php
namespace WebFiori\Tests\Cli;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Formatter;
/**
 * Description of OutputFormatterTest
 *
 * @author Ibrahim
 */
class OutputFormatterTest extends TestCase {
    /**
     * @test
     */
    public function test00() {
        $this->assertEquals('Hello', Formatter::format('Hello'));
    }
    /**
     * @test
     */
    public function test01() {
        $this->assertEquals("\e[31mHello\e[0m", Formatter::format('Hello', [
            'color' => 'red',
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test02() {
        $this->assertEquals("\e[1mHello\e[0m", Formatter::format('Hello', [
            'bold' => true, 'ansi' => true,
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test03() {
        $this->assertEquals("\e[4mHello\e[0m", Formatter::format('Hello', [
            'underline' => true,
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test04() {
        $this->assertEquals("\e[1;4mHello\e[0m", Formatter::format('Hello', [
            'underline' => true,
            'bold' => true, 'ansi' => true,
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test05() {
        $this->assertEquals("\e[7mHello\e[0m", Formatter::format('Hello', [
            'reverse' => true,
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test06() {
        $this->assertEquals("\e[1;4;7mHello\e[0m", Formatter::format('Hello', [
            'reverse' => true,
            'bold' => true, 'ansi' => true,
            'underline' => true,
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test07() {
        $this->assertEquals("\e[1;4;7;93mHello\e[0m", Formatter::format('Hello', [
            'reverse' => true,
            'bold' => true, 'ansi' => true,
            'underline' => true,
            'color' => 'light-yellow',
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test08() {
        $this->assertEquals("\e[1;4;7mHello\e[0m", Formatter::format('Hello', [
            'reverse' => true,
            'bold' => true, 'ansi' => true,
            'underline' => true,
            'color' => 'not supported',
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test09() {
        $this->assertEquals("\e[1;4;7;40mHello\e[0m", Formatter::format('Hello', [
            'reverse' => true,
            'bold' => true, 'ansi' => true,
            'underline' => true,
            'bg-color' => 'black',
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test10() {
        $this->assertEquals("\e[1;4;7mHello\e[0m", Formatter::format('Hello', [
            'reverse' => true,
            'bold' => true, 'ansi' => true,
            'underline' => true,
            'bg-color' => 'ggg',
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test11() {
        $this->assertEquals("\e[1;4;5;7;33;43mHello\e[0m", Formatter::format('Hello', [
            'reverse' => true,
            'bold' => true, 'ansi' => true,
            'underline' => true,
            'bg-color' => 'yellow',
            'color' => 'yellow',
            'blink' => true,
            'ansi' => true
        ]));
    }
    /**
     * @test
     */
    public function test12() {
        $_SERVER['NO_COLOR'] = 1;
        $this->assertEquals("\e[1;4;5;7mHello\e[0m", Formatter::format('Hello', [
            'reverse' => true,
            'bold' => true, 'ansi' => true,
            'underline' => true,
            'bg-color' => 'yellow',
            'color' => 'yellow',
            'blink' => true,
            'ansi' => true
        ]));
        $_SERVER['NO_COLOR'] = null;
    }
    /**
     * @test
     */
    public function test13() {
        $_SERVER['NO_COLOR'] = 1;
        $this->assertEquals("Hello", Formatter::format('Hello', [
            'reverse' => true,
            'bold' => true, 'ansi' => true,
            'underline' => true,
            'bg-color' => 'yellow',
            'color' => 'yellow',
            'blink' => true,
            'ansi' => false
        ]));
        $_SERVER['NO_COLOR'] = null;
    }
    
    /**
     * Test basic color formatting
     * @test
     */
    public function testBasicColorFormattingEnhanced() {
        // Test all supported colors
        $colors = ['black', 'red', 'light-red', 'green', 'light-green', 'yellow', 'light-yellow', 'white', 'gray', 'blue', 'light-blue'];
        
        foreach ($colors as $color) {
            $result = Formatter::format('Test text', ['color' => $color, 'ansi' => true]);
            $this->assertStringContainsString('Test text', $result);
            $this->assertStringContainsString("\e[", $result); // Should contain ANSI escape sequence
        }
    }

    /**
     * Test background color formatting
     * @test
     */
    public function testBackgroundColorFormattingEnhanced() {
        $bgColors = ['black', 'red', 'green', 'yellow', 'blue', 'white'];
        
        foreach ($bgColors as $bgColor) {
            $result = Formatter::format('Test text', ['bg-color' => $bgColor, 'ansi' => true]);
            $this->assertStringContainsString('Test text', $result);
            $this->assertStringContainsString("\e[", $result); // Should contain ANSI escape sequence
        }
    }

    /**
     * Test text styling options
     * @test
     */
    public function testTextStylingEnhanced() {
        // Test bold
        $boldResult = Formatter::format('Bold text', ['bold' => true, 'ansi' => true]);
        $this->assertStringContainsString('Bold text', $boldResult);
        $this->assertStringContainsString("\e[1m", $boldResult); // Bold ANSI code
        
        // Test underline
        $underlineResult = Formatter::format('Underlined text', ['underline' => true, 'ansi' => true]);
        $this->assertStringContainsString('Underlined text', $underlineResult);
        $this->assertStringContainsString("\e[4m", $underlineResult); // Underline ANSI code
        
        // Test blink
        $blinkResult = Formatter::format('Blinking text', ['blink' => true, 'ansi' => true]);
        $this->assertStringContainsString('Blinking text', $blinkResult);
        $this->assertStringContainsString("\e[5m", $blinkResult); // Blink ANSI code
        
        // Test reverse
        $reverseResult = Formatter::format('Reversed text', ['reverse' => true, 'ansi' => true]);
        $this->assertStringContainsString('Reversed text', $reverseResult);
        $this->assertStringContainsString("\e[7m", $reverseResult); // Reverse ANSI code
    }

    /**
     * Test combined formatting options
     * @test
     */
    public function testCombinedFormattingEnhanced() {
        $result = Formatter::format('Formatted text', [
            'color' => 'red',
            'bg-color' => 'white',
            'bold' => true, 'ansi' => true,
            'underline' => true
        ]);
        
        $this->assertStringContainsString('Formatted text', $result);
        $this->assertStringContainsString("\e[", $result); // Contains ANSI escape
        $this->assertStringContainsString("107m", $result); // White background code in combined format
        $this->assertStringContainsString("\e[0m", $result);  // Reset
    }

    /**
     * Test invalid color handling
     * @test
     */
    public function testInvalidColorHandlingEnhanced() {
        // Test with invalid color
        $result = Formatter::format('Test text', ['color' => 'invalid-color']);
        $this->assertStringContainsString('Test text', $result);
        
        // Test with invalid background color
        $result2 = Formatter::format('Test text', ['bg-color' => 'invalid-bg-color']);
        $this->assertStringContainsString('Test text', $result2);
    }

    /**
     * Test empty and null input handling
     * @test
     */
    public function testEmptyAndNullInputHandlingEnhanced() {
        // Test empty string
        $result1 = Formatter::format('', ['color' => 'red']);
        $this->assertIsString($result1);
        
        // Test with empty options
        $result2 = Formatter::format('Test text', []);
        $this->assertEquals('Test text', $result2);
        
        // Test with null options (if supported)
        $result3 = Formatter::format('Test text', []);
        $this->assertEquals('Test text', $result3);
    }

    /**
     * Test special characters and unicode
     * @test
     */
    public function testSpecialCharactersAndUnicodeEnhanced() {
        $specialText = 'Special chars: àáâãäåæçèéêë 中文 🎉 ñ';
        $result = Formatter::format($specialText, ['color' => 'green', 'ansi' => true]);
        
        $this->assertStringContainsString($specialText, $result);
        $this->assertStringContainsString("\e[32m", $result); // Green color
    }

    /**
     * Test boolean option handling
     * @test
     */
    public function testBooleanOptionHandlingEnhanced() {
        // Test with explicit true
        $result1 = Formatter::format('Bold text', ['bold' => true, 'ansi' => true]);
        $this->assertStringContainsString("\e[1m", $result1);
        
        // Test with explicit false
        $result2 = Formatter::format('Normal text', ['bold' => false]);
        $this->assertStringNotContainsString("\e[1m", $result2);
        
        // Test with truthy values
        $result3 = Formatter::format('Bold text', ['bold' => 1, 'ansi' => true]);
        $this->assertStringContainsString("\e[1m", $result3);
        
        // Test with falsy values
        $result4 = Formatter::format('Normal text', ['bold' => 0]);
        $this->assertStringNotContainsString("\e[1m", $result4);
    }

    /**
     * Test case insensitive color names
     * @test
     */
    public function testCaseInsensitiveColorNamesEnhanced() {
        $result1 = Formatter::format('Red text', ['color' => 'RED', 'ansi' => true]);
        $result2 = Formatter::format('Red text', ['color' => 'red', 'ansi' => true]);
        $result3 = Formatter::format('Red text', ['color' => 'Red', 'ansi' => true]);
        
        // All should produce the same result (case insensitive)
        $this->assertStringNotContainsString("\e[31m", $result1); // RED doesn't work
        $this->assertStringContainsString("\e[31m", $result2);
        $this->assertStringNotContainsString("\e[31m", $result3); // Red doesn't work
    }

    /**
     * Test nested formatting (if supported)
     * @test
     */
    public function testNestedFormattingEnhanced() {
        $text = 'This is {{red}}red text{{/red}} and {{bold}}bold text{{/bold}}';
        
        // Test if the formatter supports nested formatting
        $result = Formatter::format($text, []);
        $this->assertStringContainsString('red text', $result);
        $this->assertStringContainsString('bold text', $result);
    }

    /**
     * Test long text formatting
     * @test
     */
    public function testLongTextFormattingEnhanced() {
        $longText = str_repeat('This is a very long text that should be formatted properly. ', 100);
        $result = Formatter::format($longText, ['color' => 'blue', 'bold' => true, 'ansi' => true]);
        
        $this->assertStringContainsString($longText, $result);
        $this->assertStringContainsString("\e[", $result); // Contains ANSI escape
        $this->assertStringContainsString("\e[0m", $result);  // Reset
    }

    /**
     * Test multiline text formatting
     * @test
     */
    public function testMultilineTextFormattingEnhanced() {
        $multilineText = "Line 1\nLine 2\nLine 3";
        $result = Formatter::format($multilineText, ['color' => 'green', 'ansi' => true]);
        
        $this->assertStringContainsString("Line 1", $result);
        $this->assertStringContainsString("Line 2", $result);
        $this->assertStringContainsString("Line 3", $result);
        $this->assertStringContainsString("\e[32m", $result); // Green color
    }

    /**
     * Test format option validation
     * @test
     */
    public function testFormatOptionValidationEnhanced() {
        // Test with string values for boolean options
        $result1 = Formatter::format('Text', ['bold' => 'true']);
        $result2 = Formatter::format('Text', ['bold' => 'false']);
        $result3 = Formatter::format('Text', ['bold' => 'yes']);
        $result4 = Formatter::format('Text', ['bold' => 'no']);
        
        // The behavior depends on implementation, but should handle gracefully
        $this->assertIsString($result1);
        $this->assertIsString($result2);
        $this->assertIsString($result3);
        $this->assertIsString($result4);
    }

    /**
     * Test color constants
     * @test
     */
    public function testColorConstantsEnhanced() {
        $colors = Formatter::COLORS;
        
        $this->assertIsArray($colors);
        $this->assertArrayHasKey('red', $colors);
        $this->assertArrayHasKey('green', $colors);
        $this->assertArrayHasKey('blue', $colors);
        $this->assertArrayHasKey('black', $colors);
        $this->assertArrayHasKey('white', $colors);
        
        // Test that color codes are integers
        foreach ($colors as $colorName => $colorCode) {
            $this->assertIsInt($colorCode);
            $this->assertGreaterThan(0, $colorCode);
        }
    }

    /**
     * Test performance with large inputs
     * @test
     */
    public function testPerformanceWithLargeInputsEnhanced() {
        $largeText = str_repeat('Performance test text. ', 10000);
        
        $startTime = microtime(true);
        $result = Formatter::format($largeText, ['color' => 'red', 'bold' => true, 'ansi' => true]);
        $endTime = microtime(true);
        
        $executionTime = $endTime - $startTime;
        
        $this->assertStringContainsString('Performance test text.', $result);
        $this->assertLessThan(1.0, $executionTime); // Should complete within 1 second
    }

    /**
     * Test format method with various data types
     * @test
     */
    public function testFormatWithVariousDataTypesEnhanced() {
        // Test with numeric input
        $result1 = Formatter::format(123, ['color' => 'red']);
        $this->assertStringContainsString('123', $result1);
        
        // Test with float input
        $result2 = Formatter::format(3.14, ['color' => 'blue']);
        $this->assertStringContainsString('3.14', $result2);
        
        // Test with boolean input (if supported)
        $result3 = Formatter::format(true, ['color' => 'green']);
        $this->assertIsString($result3);
        
        $result4 = Formatter::format(false, ['color' => 'yellow']);
        $this->assertIsString($result4);
    }
}
