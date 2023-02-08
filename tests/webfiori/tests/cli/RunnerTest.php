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
use webfiori\tests\cli\testCommands\Command01;
use webfiori\cli\CommandArgument;
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
    public function testIsCLI() {
        $this->assertTrue(Runner::isCLI());
    }
    /**
     * @test
     */
    public function testRunner00() {
        $runner = new Runner();
        $this->assertEquals([], $runner->getOutput());
        $this->assertEquals([], $runner->getCommands());
        $this->assertFalse($runner->addArg(' '));
        $this->assertFalse($runner->addArg(' invalid name '));
        $this->assertNull($runner->getDefaultCommand());
        $this->assertNull($runner->getActiveCommand());
        
        $argObj = new CommandArgument('--ansi');
        $this->assertFalse($runner->addArgument($argObj));
        
        $this->assertTrue($runner->addArg('global-arg', [
            'optional' => true
        ]));
        $this->assertEquals(2, count($runner->getArgs()));
        $runner->removeArgument('--ansi');
        $this->assertEquals(1, count($runner->getArgs()));
        $this->assertFalse($runner->hasArg('--ansi'));
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
    public function testRunner01() {
        $runner = new Runner();
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
        $runner->setDefaultCommand('super-hero');
        $this->assertNull($runner->getDefaultCommand());
        $runner->setInput([]);
        $this->assertEquals(-1, $runner->runCommand(null, [
            'do-it',
            '--ansi'
        ]));
        $this->assertEquals(-1, $runner->getLastCommandExitStatus());
        $this->assertEquals([
            "Error: The command 'do-it' is not supported.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner02() {
        $runner = new Runner();
        $runner->setDefaultCommand('super-hero');
        $this->assertNull($runner->getDefaultCommand());
        $runner->setInput([]);
        $this->assertEquals(0, $runner->runCommand());
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
        $this->assertEquals([
            "Info: No command was specified to run.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner03() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->setInput([]);
        $this->assertEquals(-1, $runner->runCommand(null, [
            'super-hero',
            'name' => 'Ok'
        ]));
        $this->assertEquals(-1, $runner->getLastCommandExitStatus());
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
    public function testRunner04() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->setInput([]);
        $this->assertEquals(-1, $runner->runCommand(null, [
            'super-hero',
            'name' => 'Ok',
            '--ansi'
        ]));
        $this->assertEquals(-1, $runner->getLastCommandExitStatus());
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
    public function testRunner05() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());
        $runner->removeArgument('--ansi');
        $runner->setDefaultCommand('help');
        $runner->setInput([]);
        $this->assertEquals(0, $runner->runCommand(null, []));
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
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
    public function testRunner06() {
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
    public function testRunner07() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->setDefaultCommand('help');
        $runner->setInput([]);
        $this->assertEquals(0, $runner->runCommand(new HelpCommand(), [
            '--ansi'
        ]));
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
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
    public function testRunner08() {
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
    public function testRunner09() {
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
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
    }
    /**
     * @test
     */
    public function testRunner10() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());
        $runner->setInput([]);
        $runner->setArgsVector([
            'entry.php',
            'help',
            '--command-name' => 'super-hero'
        ]);
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
    public function testRunner11() {
        $runner = new Runner();
        $runner->setBeforeStart(function (Runner $r) {
            $r->setArgsVector([
                'entry.php',
                'help',
                '--command-name' => 'super hero',
                '--ansi'
            ]);
            $r->register(new Command00());
            $r->register(new HelpCommand());
            $r->setInput([]);
        });
        $runner->start();
        $this->assertEquals([
            "\e[1;91mError: \e[0mCommand 'super hero' is not supported.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner12() {
        
        $runner = new Runner();
        
        $runner->register(new Command00());
        $runner->register(new HelpCommand());
        
        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInput([
            'exit'
        ]);
        $runner->start();
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type command name or 'exit' to close.\n",
            ">> "
        ], $runner->getOutput());
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
    }
    /**
     * @test
     */
    public function testRunner13() {
        
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());

        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInput([
            'help --ansi',
            'exit'
        ]);
        $runner->start();
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type command name or 'exit' to close.\n",
            ">> Usage:\n",
            "    command [arg1 arg2=\"val\" arg3...]\n\n",
            "Global Arguments:\n",
            "    --ansi:[Optional] Force the use of ANSI output.\n",
            "Available Commands:\n",
            "    super-hero:     A command to display hero's name.\n",
            "    help:           Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            ">> ",
        ], $runner->getOutput());
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
    }
    /**
     * @test
     */
    public function testRunner14() {
        $runner = new Runner();
        
        $runner->register(new Command00());
        $runner->register(new HelpCommand());

        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInput([
            'help --ansi --command-name=super-hero',
            'super-hero name=Ibrahim',
            'exit'
        ]);
        $runner->start();
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type command name or 'exit' to close.\n",
            ">>     super-hero:     A command to display hero's name.\n",
            "    Supported Arguments:\n",
            "                         name: The name of the hero\n",
            ">> Hello hero Ibrahim\n",
            ">> "
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner15() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());
        $runner->register(new WithExceptionCommand());
        $runner->setAfterExecution(function (Runner $r) {
            $r->getActiveCommand()->println('Command Exit Status: '.$r->getLastCommandExitStatus());
        });
        $runner->setArgsVector([
            'entry.php',
            '--ansi',
            '-i',
        ]);
        $runner->setInput([
            'help --command-name=super-hero',
            'with-exception',
            'exit'
        ]);
        $runner->start();
        $this->assertEquals([
            "[1;34m>>[0m Running in interactive mode.\n",
            "[1;34m>>[0m Type command name or 'exit' to close.\n",
            "[1;34m>>[0m [1;33m    super-hero[0m:         A command to display hero's name.\n",
            "[1;94m    Supported Arguments:[0m\n",
            "[1;33m                         name:[0m The name of the hero\n",
            "Command Exit Status: 0\n",
            "[1;34m>>[0m [1;31mError:[0m An exception was thrown.\n",
            "[1;33mException Message:[0m Call to undefined method webfiori\\tests\\cli\\testCommands\\WithExceptionCommand::notExist()\n",
            "[1;33mCode:[0m 0\n",
            "[1;33mAt:[0m ".ROOT_DIR."tests".DS."webfiori".DS."tests".DS."cli".DS."testCommands".DS."WithExceptionCommand.php\n",
            "[1;33mLine:[0m 12\n",
            "Command Exit Status: -1\n",
            "[1;34m>>[0m ",
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner16() {
        $runner = new Runner();
        $runner->register(new Command01());
        $runner->setInput([]);
        $this->assertEquals(-1, $runner->runCommand(null, [
            'show-v'
        ]));
        $this->assertEquals([
            "Error: The following required argument(s) are missing: 'arg-1', 'arg-2'\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner17() {
        $runner = new Runner();
        $runner->register(new Command01());
        $runner->setInput([]);
        $this->assertEquals(-1, $runner->runCommand(null, [
            'show-v',
            '--ansi'
        ]));
        $this->assertEquals([
            "\e[1;91mError: \e[0mThe following required argument(s) are missing: 'arg-1', 'arg-2'\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner18() {
        $runner = new Runner();
        $runner->register(new Command01());
        $runner->setInput([]);
        $runner->setAfterExecution(function (Runner $r) {
            $r->getActiveCommand()->println('Command Exit Status: '.$r->getLastCommandExitStatus());
        });
        $this->assertEquals(0, $runner->runCommand(null, [
            'show-v',
            'arg-1' => 'Super Cool Arg',
            'arg-2' => "First One is Coller",
        ]));
        $this->assertEquals([
            "System version: 1.0.0\n",
            "Super Cool Arg\n",
            "First One is Coller\n",
            "Hello\n",
            "Command Exit Status: 0\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner19() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());
        $runner->register(new WithExceptionCommand());
        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInput([
            '',
            '',
            'exit'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type command name or 'exit' to close.\n",
            ">> No input.\n",
            ">> No input.\n",
            ">> "
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner20() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->register(new HelpCommand());
        $runner->register(new WithExceptionCommand());
        $runner->setArgsVector([
            'entry.php',
            '--ansi',
        ]);
        $runner->setInput([

        ]);
        $runner->start();
        //$this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "[1;34mInfo:[0m No command was specified to run.\n",
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner21() {
        $runner = new Runner();
        
        $runner->register(new Command00());
        $runner->register(new HelpCommand());
        $runner->register(new WithExceptionCommand());
        $runner->setAfterExecution(function (Runner $r) {
            $r->getActiveCommand()->println('Command Exit Status: '.$r->getLastCommandExitStatus());
        });

        $runner->setArgsVector([
            'entry.php',
            'with-exception',
        ]);
        $runner->setInput([]);
        $runner->start();
        $this->assertEquals([
            "Error: An exception was thrown.\n",
            "Exception Message: Call to undefined method webfiori\\tests\cli\\testCommands\WithExceptionCommand::notExist()\n",
            "Code: 0\n",
            "At: ".ROOT_DIR."tests\webfiori\\tests\cli\\testCommands\WithExceptionCommand.php\n",
            "Line: 12\n",
            "Command Exit Status: -1\n"
        ], $runner->getOutput());
    }
}
