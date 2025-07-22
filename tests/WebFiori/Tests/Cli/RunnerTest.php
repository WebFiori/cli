<?php
namespace WebFiori\Tests\Cli;

use WebFiori\Cli\Argument;
use WebFiori\Cli\Commands\HelpCommand;
use WebFiori\Cli\CommandTestCase;
use WebFiori\Cli\Runner;
use WebFiori\Cli\Streams\ArrayInputStream;
use WebFiori\Cli\Streams\ArrayOutputStream;
use WebFiori\Cli\Streams\StdIn;
use WebFiori\Cli\Streams\StdOut;
use WebFiori\Tests\Cli\TestCommands\Command00;
use WebFiori\Tests\Cli\TestCommands\Command01;
use WebFiori\Tests\Cli\TestCommands\WithExceptionCommand;
/**
 * Description of RunnerTest
 *
 * @author Ibrahim
 */
class RunnerTest extends CommandTestCase {
    /**
     * @test
     */
    public function test00() {
        $runner = new Runner();
        $this->assertEquals([], $runner->getCommands());
        $this->assertEquals([], $runner->getArgsVector());
        $this->assertEquals('', $runner->getDefaultCommand());
        $this->assertFalse($runner->isInteractive());
        $this->assertFalse($runner->isVerbose());
        $this->assertTrue($runner->getInputStream() instanceof StdIn);
        $this->assertTrue($runner->getOutputStream() instanceof StdOut);
    }
    /**
     * @test
     */
    public function test01() {
        $runner = new Runner();
        $this->assertFalse($runner->hasCommand('hello'));
        $this->assertTrue($runner->register(new TestCommand('hello')));
        $this->assertTrue($runner->hasCommand('hello'));
        $this->assertFalse($runner->register(new TestCommand('hello')));
        $this->assertFalse($runner->register(new TestCommand('')));
    }
    /**
     * @test
     */
    public function test02() {
        $runner = new Runner();
        $this->assertNull($runner->getCommand('hello'));
        $this->assertTrue($runner->register(new TestCommand('hello')));
        $this->assertTrue($runner->getCommand('hello') instanceof TestCommand);
    }
    /**
     * @test
     */
    public function test03() {
        $runner = new Runner();
        $this->assertEquals('', $runner->getDefaultCommand());
        $this->assertFalse($runner->setDefaultCommand('hello'));
        $this->assertEquals('', $runner->getDefaultCommand());
        $this->assertTrue($runner->register(new TestCommand('hello')));
        $this->assertTrue($runner->setDefaultCommand('hello'));
        $this->assertEquals('hello', $runner->getDefaultCommand());
    }
    /**
     * @test
     */
    public function test04() {
        $runner = new Runner();
        $runner->setArgsVector([
            'app.php',
            '-i'
        ]);
        $this->assertEquals([
            'app.php',
            '-i'
        ], $runner->getArgsVector());
        $runner->_checkIsInteractive();
        $this->assertTrue($runner->isInteractive());
    }
    /**
     * @test
     */
    public function test05() {
        $runner = new Runner();
        $runner->setArgsVector([
            'app.php',
            '-v'
        ]);
        $this->assertEquals([
            'app.php',
            '-v'
        ], $runner->getArgsVector());
        $runner->_checkIsVerbose();
        $this->assertTrue($runner->isVerbose());
    }
    /**
     * @test
     */
    public function test06() {
        $runner = new Runner();
        $runner->setArgsVector([
            'app.php',
            '-v',
            '-i'
        ]);
        $this->assertEquals([
            'app.php',
            '-v',
            '-i'
        ], $runner->getArgsVector());
        $runner->_checkIsVerbose();
        $runner->_checkIsInteractive();
        $this->assertTrue($runner->isVerbose());
        $this->assertTrue($runner->isInteractive());
    }
    /**
     * @test
     */
    public function test07() {
        $runner = new Runner();
        $runner->setArgsVector([
            'app.php',
            '-v',
            '-i'
        ]);
        $this->assertEquals([
            'app.php',
            '-v',
            '-i'
        ], $runner->getArgsVector());
        $runner->_checkIsVerbose();
        $runner->_checkIsInteractive();
        $this->assertTrue($runner->isVerbose());
        $this->assertTrue($runner->isInteractive());
        $runner->reset();
        $this->assertEquals([], $runner->getArgsVector());
        $this->assertEquals('', $runner->getDefaultCommand());
        $this->assertFalse($runner->isInteractive());
        $this->assertFalse($runner->isVerbose());
        $this->assertTrue($runner->getInputStream() instanceof StdIn);
        $this->assertTrue($runner->getOutputStream() instanceof StdOut);
    }
    /**
     * @test
     */
    public function test08() {
        $runner = new Runner();
        $runner->setInputStream(new ArrayInputStream([
            'Hello'
        ]));
        $this->assertTrue($runner->getInputStream() instanceof ArrayInputStream);
        $runner->setOutputStream(new ArrayOutputStream());
        $this->assertTrue($runner->getOutputStream() instanceof ArrayOutputStream);
    }
    /**
     * @test
     */
    public function test09() {
        $runner = new Runner();
        $runner->setInputs([
            'Hello'
        ]);
        $this->assertTrue($runner->getInputStream() instanceof ArrayInputStream);
        $this->assertTrue($runner->getOutputStream() instanceof ArrayOutputStream);
    }
    /**
     * @test
     */
    public function test10() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->print('Hello');
        $this->assertEquals([
            'Hello'
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test11() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->print('Hello %s', 'Ibrahim');
        $this->assertEquals([
            'Hello Ibrahim'
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test12() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->println('Hello');
        $this->assertEquals([
            "Hello\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test13() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->println('Hello %s', 'Ibrahim');
        $this->assertEquals([
            "Hello Ibrahim\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test14() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->println();
        $this->assertEquals([
            "\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test15() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->setArgsVector([
            'app.php'
        ]);
        $this->assertEquals(-1, $runner->start());
        $this->assertEquals([
            "Error: No command was specified to run.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test16() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->setArgsVector([
            'app.php',
            'hello'
        ]);
        $this->assertEquals(-1, $runner->start());
        $this->assertEquals([
            "Error: No command was specified to run.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test17() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello'));
        $runner->setArgsVector([
            'app.php',
            'hello'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "Hello World!\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test18() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'optional' => true
            ]
        ]));
        $runner->setArgsVector([
            'app.php',
            'hello',
            '--name' => 'Ibrahim'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "Hello Ibrahim!\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test19() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                
            ]
        ]));
        $runner->setArgsVector([
            'app.php',
            'hello'
        ]);
        $this->assertEquals(-1, $runner->start());
        $this->assertEquals([
            "Error: The argument '--name' is required.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test20() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'values' => [
                    'Ibrahim', 'Ali'
                ]
            ]
        ]));
        $runner->setArgsVector([
            'app.php',
            'hello',
            '--name' => 'Ibrahim'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "Hello Ibrahim!\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test21() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'values' => [
                    'Ibrahim', 'Ali'
                ]
            ]
        ]));
        $runner->setArgsVector([
            'app.php',
            'hello',
            '--name' => 'Khalid'
        ]);
        $this->assertEquals(-1, $runner->start());
        $this->assertEquals([
            "Error: The value 'Khalid' is not allowed for the argument '--name'.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test22() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new WithExceptionCommand());
        $runner->setArgsVector([
            'app.php',
            'with-exception'
        ]);
        $this->assertEquals(-1, $runner->start());
        $this->assertEquals([
            "Error: Call to undefined method WebFiori\Tests\Cli\TestCommands\WithExceptionCommand::notExist()\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test23() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->setInputs([
            'exit'
        ]);
        $runner->setArgsVector([
            'app.php',
            '-i'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type command name or 'exit' to close.\n",
            ">>\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test24() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->setInputs([
            'hello',
            'exit'
        ]);
        $runner->setArgsVector([
            'app.php',
            '-i'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type command name or 'exit' to close.\n",
            ">>\n",
            "Error: Command 'hello' is not supported.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test25() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello'));
        $runner->setInputs([
            'hello',
            'exit'
        ]);
        $runner->setArgsVector([
            'app.php',
            '-i'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type command name or 'exit' to close.\n",
            ">>\n",
            "Hello World!\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test26() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                
            ]
        ]));
        $runner->setInputs([
            'hello',
            'exit'
        ]);
        $runner->setArgsVector([
            'app.php',
            '-i'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type command name or 'exit' to close.\n",
            ">>\n",
            "Error: The argument '--name' is required.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test27() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new WithExceptionCommand());
        $runner->setInputs([
            'with-exception',
            'exit'
        ]);
        $runner->setArgsVector([
            'app.php',
            '-i'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            ">> Running in interactive mode.\n",
            ">> Type command name or 'exit' to close.\n",
            ">>\n",
            "Error: Call to undefined method WebFiori\Tests\Cli\TestCommands\WithExceptionCommand::notExist()\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test28() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello'));
        $runner->setDefaultCommand('hello');
        $runner->setArgsVector([
            'app.php'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "Hello World!\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test29() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello'));
        $runner->setDefaultCommand('hello');
        $runner->setArgsVector([
            'app.php',
            'not-exist'
        ]);
        $this->assertEquals(-1, $runner->start());
        $this->assertEquals([
            "Error: No command was specified to run.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test30() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new Command00());
        $runner->setArgsVector([
            'app.php',
            'super-hero',
            'name' => 'Ibrahim'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "Hello hero Ibrahim\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test31() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new Command00());
        $runner->setArgsVector([
            'app.php',
            'super-hero',
            'name' => 'Khalid'
        ]);
        $this->assertEquals(-1, $runner->start());
        $this->assertEquals([
            "Error: The value 'Khalid' is not allowed for the argument 'name'.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test32() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new Command01());
        $runner->setArgsVector([
            'app.php',
            'show-v',
            'arg-1' => 'Hello',
            'arg-2' => 'World'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "System version: 1.0.0\n",
            "Hello\n",
            "World\n",
            "Hello\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test33() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->setArgsVector([
            'app.php',
            'help'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "\e[1;93mUsage:\e[0m\n",
            "    command [arg1 arg2=\"val\" arg3...]\n",
            "\n",
            "\e[1;93mAvailable Commands:\e[0m\n",
            "    \e[1;33mhelp\e[0m:          Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test34() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->register(new TestCommand('hello'));
        $runner->setArgsVector([
            'app.php',
            'help',
            '--command-name' => 'hello'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "    \e[1;33mhello\e[0m:         \n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test35() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->register(new TestCommand('hello'));
        $runner->setArgsVector([
            'app.php',
            'help',
            '--command-name' => 'not-exist'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "\e[1;31mError:\e[0m Command 'not-exist' is not supported.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test36() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'description' => 'The name of the person to say hi to.'
            ]
        ]));
        $runner->setArgsVector([
            'app.php',
            'help',
            '--command-name' => 'hello'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "    \e[1;33mhello\e[0m:         \n",
            "    \e[1;94mSupported Arguments:\e[0m\n",
            "    \e[1;33m                         --name\e[0m: The name of the person to say hi to.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test37() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'description' => 'The name of the person to say hi to.',
                'optional' => true
            ]
        ]));
        $runner->setArgsVector([
            'app.php',
            'help',
            '--command-name' => 'hello'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "    \e[1;33mhello\e[0m:         \n",
            "    \e[1;94mSupported Arguments:\e[0m\n",
            "    \e[1;33m                         --name\e[0m:[Optional] The name of the person to say hi to.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test38() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'description' => 'The name of the person to say hi to.',
                'optional' => true,
                'default' => 'Ibrahim'
            ]
        ]));
        $runner->setArgsVector([
            'app.php',
            'help',
            '--command-name' => 'hello'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "    \e[1;33mhello\e[0m:         \n",
            "    \e[1;94mSupported Arguments:\e[0m\n",
            "    \e[1;33m                         --name\e[0m:[Optional][Default = 'Ibrahim'] The name of the person to say hi to.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test39() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'description' => 'The name of the person to say hi to.',
                'optional' => true,
                'default' => 'Ibrahim'
            ]
        ]));
        $runner->setArgsVector([
            'app.php',
            'help'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "\e[1;93mUsage:\e[0m\n",
            "    command [arg1 arg2=\"val\" arg3...]\n",
            "\n",
            "\e[1;93mAvailable Commands:\e[0m\n",
            "    \e[1;33mhelp\e[0m:          Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            "    \e[1;33mhello\e[0m:         \n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test40() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'description' => 'The name of the person to say hi to.',
                'optional' => true,
                'default' => 'Ibrahim'
            ]
        ]));
        $arg = new Argument('--ansi');
        $arg->setDescription('Force the use of ANSI output.');
        $arg->setIsOptional(true);
        $runner->addArg($arg);
        $runner->setArgsVector([
            'app.php',
            'help'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "\e[1;93mUsage:\e[0m\n",
            "    command [arg1 arg2=\"val\" arg3...]\n",
            "\n",
            "\e[1;93mGlobal Arguments:\e[0m\n",
            "    \e[1;33m    --ansi\e[0m:[Optional] Force the use of ANSI output.\n",
            "\e[1;93mAvailable Commands:\e[0m\n",
            "    \e[1;33mhelp\e[0m:          Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            "    \e[1;33mhello\e[0m:         \n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test41() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'description' => 'The name of the person to say hi to.',
                'optional' => true,
                'default' => 'Ibrahim'
            ]
        ]));
        $arg = new Argument('--ansi');
        $arg->setDescription('Force the use of ANSI output.');
        $arg->setIsOptional(true);
        $runner->addArg($arg);
        $runner->setArgsVector([
            'app.php',
            'help',
            '--command-name' => 'hello'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "    \e[1;33mhello\e[0m:         \n",
            "    \e[1;94mSupported Arguments:\e[0m\n",
            "    \e[1;33m                         --name\e[0m:[Optional][Default = 'Ibrahim'] The name of the person to say hi to.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test42() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'description' => 'The name of the person to say hi to.',
                'optional' => true,
                'default' => 'Ibrahim'
            ]
        ]));
        $runner->register(new TestCommand('hello-world', [
            '--name' => [
                'description' => 'The name of the person to say hi to.',
                'optional' => true,
                'default' => 'Ibrahim'
            ]
        ]));
        $runner->setArgsVector([
            'app.php',
            'help'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "\e[1;93mUsage:\e[0m\n",
            "    command [arg1 arg2=\"val\" arg3...]\n",
            "\n",
            "\e[1;93mAvailable Commands:\e[0m\n",
            "    \e[1;33mhelp\e[0m:          Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            "    \e[1;33mhello\e[0m:         \n",
            "    \e[1;33mhello-world\e[0m:   \n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test43() {
        $runner = new Runner();
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new HelpCommand());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'description' => 'The name of the person to say hi to.',
                'optional' => true,
                'default' => 'Ibrahim'
            ]
        ], 'A command to say hi.'));
        $runner->register(new TestCommand('hello-world', [
            '--name' => [
                'description' => 'The name of the person to say hi to.',
                'optional' => true,
                'default' => 'Ibrahim'
            ]
        ], 'A command to say hi to the world.'));
        $runner->setArgsVector([
            'app.php',
            'help'
        ]);
        $this->assertEquals(0, $runner->start());
        $this->assertEquals([
            "\e[1;93mUsage:\e[0m\n",
            "    command [arg1 arg2=\"val\" arg3...]\n",
            "\n",
            "\e[1;93mAvailable Commands:\e[0m\n",
            "    \e[1;33mhelp\e[0m:          Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            "    \e[1;33mhello\e[0m:         A command to say hi.\n",
            "    \e[1;33mhello-world\e[0m:   A command to say hi to the world.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test44() {
        $runner = new Runner();
        $this->assertNull($runner->getActiveCommand());
        $runner->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello'));
        $runner->setArgsVector([
            'app.php',
            'hello'
        ]);
        $runner->start();
        $this->assertTrue($runner->getActiveCommand() instanceof TestCommand);
    }
}
