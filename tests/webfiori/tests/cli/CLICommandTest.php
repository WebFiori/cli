<?php
namespace webfiori\tests\cli;

use PHPUnit\Framework\TestCase;
use webfiori\cli\Runner;
use webfiori\tests\cli\TestCommand;

class CLICommandTest extends TestCase {
    /**
     * @test
     */
    public function test00() {
        $command = new TestCommand('new-command');
        $this->assertEquals($command->getName(), 'new-command');
        $this->assertEquals('<NO DESCRIPTION>', $command->getDescription());
        $this->assertEquals(0, count($command->getArgs()));
    }
    /**
     * @test
     */
    public function test01() {
        $command = new TestCommand('new-command');
        $command->println('%30s', 'ok');
        $this->assertTrue(true);
    }
    /**
     * @test
     */
    public function test03() {
        $command = new TestCommand('with space');
        $this->assertEquals('new-command', $command->getName());
        $this->assertEquals('<NO DESCRIPTION>', $command->getDescription());
    }
    /**
     * @test
     */
    public function testAddArg00() {
        $command = new TestCommand('new-command');
        $this->assertFalse($command->addArg(''));
        $this->assertFalse($command->addArg('with space'));
        $this->assertFalse($command->addArg('       '));
        $this->assertFalse($command->addArg('invalid name'));
        $this->assertTrue($command->addArg('valid'));
        $this->assertTrue($command->addArg('--valid-name'));
        $this->assertTrue($command->addArg('0invalid'));
        $this->assertTrue($command->addArg('valid-1'));
    }
    /**
     * @test
     */
    public function testAddArg01() {
        $command = new TestCommand('new-command');
        $this->assertTrue($command->addArg('default-options'));
        $argDetails = $command->getArg('default-options');
        $this->assertEquals('<NO DESCRIPTION>', $argDetails->getDescription());
        $this->assertFalse($argDetails->isOptional());
        $this->assertEquals([], $argDetails->getAllowedValues());
    }
    /**
     * @test
     */
    public function testAddArg02() {
        $command = new TestCommand('new-command');
        $this->assertTrue($command->addArg('default-options', [
            'optional' => true
        ]));
        $argDetails = $command->getArg('default-options');
        $this->assertEquals('<NO DESCRIPTION>', $argDetails->getDescription());
        $this->assertTrue($argDetails->isOptional());
        $this->assertEquals([], $argDetails->getAllowedValues());
    }
    /**
     * @test
     */
    public function testAddArg03() {
        $command = new TestCommand('new');
        $this->assertTrue($command->addArg('default-options', [
            'optional' => true
        ]));
        $argDetails = $command->getArg('default-options');
        $this->assertEquals('<NO DESCRIPTION>', $argDetails->getDescription());
        $this->assertTrue($argDetails->isOptional());
        $this->assertEquals([], $argDetails->getAllowedValues());
    }
    /**
     * @test
     */
    public function testAddArg04() {
        $command = new TestCommand('new');
        $this->assertTrue($command->addArg('default-options', [
            'optional' => true
        ]));
        $this->assertFalse($command->addArg('default-options'));
    }
    /**
     * @test
     */
    public function testClear00() {
        $runner = new Runner();
        $runner->setInput([]);
        $command = new TestCommand('hello', [
            'name' => [
                
            ]
        ]);
        $runner->runCommand($command, [
            'name' => 'Ibrahim'
        ]);
        $command->clearConsole();
        $this->assertEquals([
            "Hello Ibrahim!\n",
            "Ok\n",
            "\ec"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testClear01() {
        $runner = new Runner();
        $runner->setInput([]);
        $command = new TestCommand('hello', [
            'name' => [
                
            ]
        ]);
        $runner->runCommand($command, [
            'name' => 'Ibrahim'
        ]);
        $command->clearLine();
        $this->assertEquals([
            "Hello Ibrahim!\n",
            "Ok\n",
            "\e[2K\r"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testClear02() {
        $runner = new Runner();
        $runner->setInput([]);
        $command = new TestCommand('hello', [
            'name' => [
                
            ]
        ]);
        $runner->runCommand($command, [
            'name' => 'Ibrahim'
        ]);
        $command->clear(1);
        $this->assertEquals([
            "Hello Ibrahim!\n",
            "Ok\n",
            "\e[1D \e[1D\e[1C"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testClear03() {
        $runner = new Runner();
        $runner->setInput([]);
        $command = new TestCommand('hello', [
            'name' => [
                
            ]
        ]);
        $runner->runCommand($command, [
            'name' => 'Ibrahim'
        ]);
        $command->clear(2);
        $this->assertEquals([
            "Hello Ibrahim!\n",
            "Ok\n",
            "\e[1D \e[1D\e[1D \e[1D\e[2C"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testClear05() {
        $runner = new Runner();
        $runner->setInput([]);
        $command = new TestCommand('hello', [
            'name' => [
                
            ]
        ]);
        $runner->runCommand($command, [
            'name' => 'Ibrahim'
        ]);
        $command->clear(1, false);
        $this->assertEquals([
            "Hello Ibrahim!\n",
            "Ok\n",
            "\e[1C \e[2D"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testClear06() {
        $runner = new Runner();
        $runner->setInput([]);
        $command = new TestCommand('hello', [
            'name' => [
                
            ]
        ]);
        $runner->runCommand($command, [
            'name' => 'Ibrahim'
        ]);
        $command->clear(2, false);
        $this->assertEquals([
            "Hello Ibrahim!\n",
            "Ok\n",
            "\e[1C  \e[3D"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testMove00() {
        $runner = new Runner();
        $runner->setInput([]);
        $command = new TestCommand('hello', [
            'name' => [
                
            ]
        ]);
        $runner->runCommand($command, [
            'name' => 'Ibrahim'
        ]);
        $command->moveCursorDown(3);
        $command->moveCursorDown(6);
        $command->moveCursorLeft(88);
        $command->moveCursorRight(4);
        $this->assertEquals([
            "Hello Ibrahim!\n",
            "Ok\n",
            "\e[3B\e[6B\e[88D\e[4C"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testMove01() {
        $runner = new Runner();
        $runner->setInput([]);
        $command = new TestCommand('hello', [
            'name' => [
                
            ]
        ]);
        $runner->runCommand($command, [
            'name' => 'Ibrahim'
        ]);
        $command->moveCursorDown(3);
        $command->moveCursorDown(6);
        $command->moveCursorLeft(88);
        $command->moveCursorRight(4);
        $this->assertEquals([
            "Hello Ibrahim!\n",
            "Ok\n",
            "\e[3B\e[6B\e[88D\e[4C"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testPrintList00() {
        $runner = new Runner();
        $runner->setInput([]);
        $command = new TestCommand('hello');
        $runner->runCommand($command);
        $command->printList([
            'one',
            'two',
            'three'
        ]);
        $this->assertEquals([
            "Hello !\n",
            "Ok\n",
            "- one\n",
            "- two\n",
            "- three\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testPrintList01() {
        $runner = new Runner();
        $runner->setInput([]);
        $command = new TestCommand('hello');
        $runner->runCommand($command, [
            '--ansi'
        ]);
        $command->printList([
            'one',
            'two',
            'three'
        ]);
        $this->assertEquals([
            "\e[31mHello !\e[0m\n",
            "Ok\n",
            "\e[32m- \e[0mone\n",
            "\e[32m- \e[0mtwo\n",
            "\e[32m- \e[0mthree\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testSetArgVal00() {
        $command = new TestCommand('ok', [
            'one' => [
                
            ],
            'two' => [
                
            ]
        ]);
        $this->assertFalse($command->isArgProvided('one'));
        $this->assertTrue($command->setArgValue('one', 1));
        $this->assertTrue($command->isArgProvided('one'));
        $arg = $command->getArg('one');
        $this->assertEquals(1, $arg->getValue());
        $this->assertFalse($command->isArgProvided('two'));
        $this->assertTrue($command->setArgValue('two'));
        $this->assertTrue($command->isArgProvided('two'));
        $this->assertEquals('', $command->getArgValue('two'));
        $this->assertFalse($command->setArgValue('not-exist'));
    }
    
}
