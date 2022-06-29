<?php
namespace webfiori\tests\cli;

use webfiori\cli\streams\ArrayInputStream;
use webfiori\cli\streams\ArrayOutputStream;
use webfiori\cli\streams\StdIn;
use webfiori\cli\streams\StdOut;
use webfiori\cli\Runner;
use PHPUnit\Framework\TestCase;
use webfiori\tests\cli\testCommands\Command00;
use webfiori\cli\commands\HelpCommand;
use webfiori\tests\cli\testCommands\WithExceptionCommand;
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
    /**
     * @test
     */
    public function runnerTest05() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());
        $runner->removeArgument('--ansi');
        $runner->setDefaultCommand('help');
        $runner->setInput([]);
        $this->assertEquals(0, $runner->runCommand(null, []));
        $this->assertEquals([
            "Usage:\n",
            "    command [arg1 arg2=\"val\" arg3...]\n\n",
            "Available Commands:\n",
            "    super-hero:     A command to display hero's name.\n",
            "    help:           Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest06() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->setDefaultCommand('help');
        $runner->setInput([]);
        $this->assertEquals(0, $runner->runCommand(new HelpCommand(), []));
        $this->assertEquals([
            "Usage:\n",
            "    command [arg1 arg2=\"val\" arg3...]\n\n",
            "Global Arguments:\n",
            "    --ansi:[Optional] Force the use of ANSI output.\n",
            "Available Commands:\n",
            "    super-hero:     A command to display hero's name.\n",
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest07() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->setDefaultCommand('help');
        $runner->setInput([]);
        $this->assertEquals(0, $runner->runCommand(new HelpCommand(), [
            '--ansi'
        ]));
        $this->assertEquals([
            "\e[1;93mUsage:\e[0m\n",
            "    command [arg1 arg2=\"val\" arg3...]\n\n",
            "\e[1;93mGlobal Arguments:\e[0m\n",
            "\e[1;33m    --ansi:\e[0m[Optional] Force the use of ANSI output.\n",
            "\e[1;93mAvailable Commands:\e[0m\n",
            "\e[1;33m    super-hero\e[0m:     A command to display hero's name.\n",
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest08() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->setInput([]);
        $this->assertEquals(0, $runner->runCommand(new HelpCommand(), [
            '--ansi',
            '--command-name' => 'super-hero'
        ]));
        $this->assertEquals([
            "\e[1;33m    super-hero\e[0m:     A command to display hero's name.\n",
            "\e[1;94m    Supported Arguments:\e[0m\n",
            "\e[1;33m                         name:\e[0m The name of the hero\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest09() {
        $_SERVER['argv'] = [];
        $runner = new Runner();
        $runner->removeArgument('--ansi');
        $runner->register(new Command00());
        $runner->register(new HelpCommand());
        $runner->setDefaultCommand('help');
        $runner->setInput([]);
        $runner->start();
        $this->assertEquals([
            "Usage:\n",
            "    command [arg1 arg2=\"val\" arg3...]\n\n",
            "Available Commands:\n",
            "    super-hero:     A command to display hero's name.\n",
            "    help:           Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest10() {
        $_SERVER['argv'] = [
            'entry.php',
            'help',
            '--command-name' => 'super-hero'
        ];
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());

        $runner->setInput([]);
        $runner->start();
        $this->assertEquals([
            "    super-hero:     A command to display hero's name.\n",
            "    Supported Arguments:\n",
            "                         name: The name of the hero\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest11() {
        $_SERVER['argv'] = [
            'entry.php',
            'help',
            '--command-name' => 'super hero',
            '--ansi'
        ];
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());

        $runner->setInput([]);
        $runner->start();
        $this->assertEquals([
            "\e[1;91mError: \e[0mCommand 'super hero' is not supported.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest12() {
        $_SERVER['argv'] = [
            'entry.php',
            '-i',
        ];
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());

        $runner->setInput([
            'exit'
        ]);
        $runner->start();
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type commant name or 'exit' to close.\n",
            ">>"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest13() {
        $_SERVER['argv'] = [
            'entry.php',
            '-i',
        ];
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());

        $runner->setInput([
            'help',
            'exit'
        ]);
        $runner->start();
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type commant name or 'exit' to close.\n",
            ">>Usage:\n",
            "    command [arg1 arg2=\"val\" arg3...]\n\n",
            "Global Arguments:\n",
            "    --ansi:[Optional] Force the use of ANSI output.\n",
            "Available Commands:\n",
            "    super-hero:     A command to display hero's name.\n",
            "    help:           Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            ">>",
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function runnerTest14() {
        $_SERVER['argv'] = [
            'entry.php',
            '-i',
        ];
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());

        $runner->setInput([
            'help --command-name=super-hero',
            'super-hero name=Ibrahim',
            'exit'
        ]);
        $runner->start();
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type commant name or 'exit' to close.\n",
            ">>    super-hero:     A command to display hero's name.\n",
            "    Supported Arguments:\n",
            "                         name: The name of the hero\n",
            ">>Hello hero Ibrahim\n",
            ">>"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
//    public function runnerTest15() {
//        $_SERVER['argv'] = [
//            'entry.php',
//            '-i',
//        ];
//        $runner = new Runner();
//        $runner->register(new Command00());
//        $runner->register(new HelpCommand());
//        $runner->register(new WithExceptionCommand());
//        $runner->setInput([
//            'help --command-name=super-hero',
//            'with-exception',
//            'exit'
//        ]);
//        $runner->start();
//        $this->assertEquals([
//            ">> Running in interactive mode.\n",
//            ">> Type commant name or 'exit' to close.\n",
//            ">>    super-hero\n",
//            "        A command to display hero's name.\n\n",
//            "    Supported Arguments:\n",
//            "                         name: The name of the hero\n",
//            ">>Hello hero Ibrahim\n",
//            ">>"
//        ], $runner->getOutput());
//    }
}
