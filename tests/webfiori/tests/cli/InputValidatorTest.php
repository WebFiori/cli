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
    /**
     * @test
     */
    public function testIsValidClassName00() {
        $this->assertFalse(InputValidator::isValidClassName('Hello World'));
        $this->assertFalse(InputValidator::isValidClassName('Hello=World'));
        $this->assertTrue(InputValidator::isValidClassName('Hello9World'));
        $this->assertFalse(InputValidator::isValidClassName('7HelloWorld'));
        $this->assertTrue(InputValidator::isValidClassName('HelloWorld0'));
        $this->assertFalse(InputValidator::isValidClassName('!HelloWorld'));
        $this->assertFalse(InputValidator::isValidClassName('Hello\World'));
    }
    /**
     * @test
     */
    public function testIsValidNs00() {
        $this->assertFalse(InputValidator::isValidNamespace('//'));
        $this->assertTrue(InputValidator::isValidNamespace('\\'));
        $this->assertTrue(InputValidator::isValidNamespace('\\HelloWorld'));
        $this->assertTrue(InputValidator::isValidNamespace('\\HelloWorld\\'));
        $this->assertFalse(InputValidator::isValidNamespace('\\7elloWorld'));
        $this->assertFalse(InputValidator::isValidNamespace('\\HelloWorld\HelloIbrahim\\7ood'));
        $this->assertTrue(InputValidator::isValidNamespace('\\HelloWorld\HelloIbrahim\\Uood7'));
    }
}
