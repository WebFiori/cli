<?php
namespace webfiori\tests\cli;

use webfiori\cli\streams\ArrayInputStream;
use webfiori\cli\streams\ArrayOutputStream;
use webfiori\cli\streams\StdIn;
use webfiori\cli\streams\StdOut;
use webfiori\cli\Runner;
use PHPUnit\Framework\TestCase;
use webfiori\tests\cli\testCommands\Command00;
/**
 * Description of RunnerTest
 *
 * @author Ibrahim
 */
class RunnerTest extends TestCase {
    /**
     * @test
     */
    public function testSetStreams00() {
        $runner = new Runner();
        $runner->reset();
        $this->assertTrue($runner->getOutputStream() instanceof StdOut);
        $this->assertTrue($runner->getInputStream() instanceof StdIn);
        $runner->setInputStream(new ArrayInputStream());
        $runner->setOutputStream(new ArrayOutputStream());
        $this->assertFalse($runner->getOutputStream() instanceof StdOut);
        $this->assertFalse($runner->getInputStream() instanceof StdIn);
        $this->assertTrue($runner->getInputStream() instanceof ArrayInputStream);
        $this->assertTrue($runner->getOutputStream() instanceof ArrayOutputStream);
    }
    /**
     * @test
     */
    public function runnerTest00() {
        $runner = new Runner();
        $this->assertEquals([], $runner->getCommands());
        $this->assertNull($runner->getDefaultCommand());
        $this->assertNull($runner->getActiveCommand());
        $runner->register(new Command00());
        $this->assertEquals(1, count($runner->getCommands()));
        $runner->register(new Command00());
        $this->assertEquals(1, count($runner->getCommands()));
        $runner->setDefaultCommand('super-hero');
        $runner->setInput([]);
        $this->assertEquals(0, $runner->runCommand(null, [
            'name' => 'Ibrahim'
        ]));
        $this->assertEquals([
            "Hello hero Ibrahim\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest01() {
        $runner = new Runner();
        $runner->setDefaultCommand('super-hero');
        $this->assertNull($runner->getDefaultCommand());
        $runner->setInput([]);
        $this->assertEquals(-1, $runner->runCommand(null, [
            'do-it',
            '--ansi'
        ]));
        $this->assertEquals([
            "Error: The command 'do-it' is not supported.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest02() {
        $runner = new Runner();
        $runner->setDefaultCommand('super-hero');
        $this->assertNull($runner->getDefaultCommand());
        $runner->setInput([]);
        $this->assertEquals(-1, $runner->runCommand());
        $this->assertEquals([
            "Info: No command was specified to run.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest03() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->setInput([]);
        $this->assertEquals(-1, $runner->runCommand(null, [
            'super-hero',
            'name' => 'Ok'
        ]));
        $this->assertEquals([
            "Error: The following argument(s) have invalid values: 'name'\n",
            "Info: Allowed values for the argument 'name':\n",
            "Ibrahim\n",
            "Ali\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest04() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->setInput([]);
        $this->assertEquals(-1, $runner->runCommand(null, [
            'super-hero',
            'name' => 'Ok',
            '--ansi'
        ]));
        $this->assertEquals([
            "\e[1;91mError: \e[0mThe following argument(s) have invalid values: 'name'\n",
            "\e[1;34mInfo: \e[0mAllowed values for the argument 'name':\n",
            "Ibrahim\n",
            "Ali\n"
        ], $runner->getOutput());
    }
}
