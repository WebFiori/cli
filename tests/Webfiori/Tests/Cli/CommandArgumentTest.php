<?php

namespace webfiori\tests\cli;
use PHPUnit\Framework\TestCase;
use webfiori\cli\Argument;
use webfiori\cli\Runner;
/**
 * Description of CommandArgumentTest
 *
 * @author Ibrahim
 */
class CommandArgumentTest extends TestCase {
    /**
     * @test
     */
    public function extractValueTest00() {
        $_SERVER['argv'] = [
            'name=ibrahim'
        ];
        $this->assertEquals('ibrahim', Argument::extractValue('name'));
    }
    /**
     * @test
     */
    public function extractValueTest01() {
        $_SERVER['argv'] = [
            'name=ibrahim'
        ];
        $r = new Runner();
        $r->setArgsVector([
            'name=ali' 
        ]);
        $this->assertEquals('ali', Argument::extractValue('name', $r));
    }
    /**
     * @test
     */
    public function extractValueTest03() {
        $_SERVER['argv'] = [
            'name="ibrahim Ali"',
            "last-name='bin'"
        ];
        $this->assertEquals('ibrahim Ali', Argument::extractValue('name'));
        $this->assertEquals('bin', Argument::extractValue('last-name'));
    }
    /**
     * @test
     */
    public function test00() {
        $arg = new Argument();
        $this->assertNull($arg->getValue());
        $this->assertEquals('', $arg->getDefault());
        $this->assertEquals('', $arg->getDescription());
        $arg->setDescription(' Cool Arg ');
        $this->assertEquals('Cool Arg', $arg->getDescription());
        $arg->setDescription(' ');
        $this->assertEquals('', $arg->getDescription());
        $this->assertEquals('arg', $arg->getName());
        $this->assertFalse($arg->isOptional());
        $arg->setIsOptional(true);
        $this->assertTrue($arg->isOptional());
        $arg->setIsOptional(false);
        $this->assertFalse($arg->isOptional());
        $this->assertEquals([], $arg->getAllowedValues());
        $this->assertNull($arg->getValue());
    }
    /**
     * @test
     */
    public function test01() {
        $arg = new Argument('');
        $this->assertEquals('arg', $arg->getName());
    }
    /**
     * @test
     */
    public function test02() {
        $arg = new Argument('--config');
        $this->assertNull($arg->getValue());
        $this->assertEquals('', $arg->getDefault());
        $this->assertEquals('', $arg->getDescription());
        $this->assertEquals('--config', $arg->getName());
        $this->assertFalse($arg->isOptional());
        $this->assertEquals([], $arg->getAllowedValues());
        $this->assertNull($arg->getValue());
    }
    /**
     * @test
     */
    public function testSetName() {
        $arg = new Argument('    ');
        $this->assertEquals('arg', $arg->getName());
        $this->assertTrue($arg->setName('my-val'));
        $this->assertEquals('my-val', $arg->getName());
        $this->assertFalse($arg->setName('with space'));
        $this->assertEquals('my-val', $arg->getName());
        $this->assertTrue($arg->setName('   --arg1    '));
        $this->assertEquals('--arg1', $arg->getName());
    }
    /**
     * @test
     */
    public function testSetValue00() {
        $arg = new Argument();
        $this->assertNull($arg->getValue());
        $arg->setValue('');
        $this->assertEquals('', $arg->getValue());
        $arg->setValue('    Super Lengthy String      ');
        $this->assertEquals('Super Lengthy String', $arg->getValue());
    }
    /**
     * @test
     */
    public function testSetValue01() {
        $arg = new Argument();
        $this->assertNull($arg->getValue());
        $arg->addAllowedValue('Super');
        $this->assertFalse($arg->setValue(''));
        $this->assertNull($arg->getValue());
        $this->assertTrue($arg->setValue('Super'));
        $this->assertEquals('Super', $arg->getValue());
    }
}
