<?php
namespace webfiori\tests\cli;

use PHPUnit\Framework\TestCase;
use webfiori\cli\InputValidator;

class InputValidatorTest extends TestCase {
    /**
     * @test
     */
    public function testIsValidInt() {
        $this->assertFalse(InputValidator::isInt(''));
        $this->assertFalse(InputValidator::isInt(' 1 2 3'));
        $this->assertFalse(InputValidator::isInt(' 1 '));
        $this->assertTrue(InputValidator::isInt('66'));
        $this->assertFalse(InputValidator::isInt('6&r7'));
    }
    /**
     * @test
     */
    public function testIsValidFloat() {
        $this->assertFalse(InputValidator::isFloat(''));
        $this->assertFalse(InputValidator::isFloat(' 1 2 3'));
        $this->assertFalse(InputValidator::isFloat(' 1.1.4 '));
        $this->assertTrue(InputValidator::isFloat('66'));
        $this->assertTrue(InputValidator::isFloat('77.9'));
        $this->assertFalse(InputValidator::isFloat('6&r7.90'));
    }
}
