<?php
namespace WebFiori\Tests\Cli;

use WebFiori\CLI\Argument;
use WebFiori\CLI\Commands\HelpCommand;
use WebFiori\CLI\CommandTestCase;
use WebFiori\CLI\Runner;
use WebFiori\CLI\Streams\ArrayInputStream;
use WebFiori\CLI\Streams\ArrayOutputStream;
use WebFiori\CLI\Streams\StdIn;
use WebFiori\CLI\Streams\StdOut;
use WebFiori\Tests\CLI\TestCommands\Command00;
use WebFiori\Tests\CLI\TestCommands\Command01;
use WebFiori\Tests\CLI\TestCommands\WithExceptionCommand;
use WebFiori\Tests\CLI\TestCommands\Command03;
use const DS;
use const ROOT_DIR;


/**
 * Description of RunnerTest
 *
 * @author Ibrahim
 */
class RunnerTest extends CommandTestCase {
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
        // Help command is automatically registered
        $this->assertEquals(['help'], array_keys($runner->getCommands()));
        $this->assertFalse($runner->addArg(' '));
        $this->assertFalse($runner->addArg(' invalid name '));
        $this->assertInstanceOf(\WebFiori\CLI\Commands\HelpCommand::class, $runner->getDefaultCommand());
        $this->assertNull($runner->getActiveCommand());
        
        $argObj = new Argument('--ansi');
        $this->assertFalse($runner->addArgument($argObj));
        
        $this->assertTrue($runner->addArg('global-arg', [
            'optional' => true
        ]));
        $this->assertEquals(2, count($runner->getArgs()));
        $runner->removeArgument('--ansi');
        $this->assertEquals(1, count($runner->getArgs()));
        $this->assertFalse($runner->hasArg('--ansi'));
        $runner->register(new Command00());
        $this->assertEquals(2, count($runner->getCommands())); // help + super-hero
        $runner->register(new Command00());
        $this->assertEquals(2, count($runner->getCommands())); // Still 2, no duplicates
        $runner->setDefaultCommand('super-hero');
        $runner->setInputs([]);
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
        // Since 'super-hero' is not registered, default remains the help command
        $this->assertInstanceOf(\WebFiori\CLI\Commands\HelpCommand::class, $runner->getDefaultCommand());
        $runner->setInputs([]);
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
        // Since 'super-hero' is not registered, default remains the help command
        $this->assertInstanceOf(\WebFiori\CLI\Commands\HelpCommand::class, $runner->getDefaultCommand());
        $runner->setInputs([]);
        $this->assertEquals(0, $runner->runCommand());
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
        // Since default command is help, it will show help output instead of "No command" message
        $output = $runner->getOutput();
        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Usage:', $output[0]);
    }
    /**
     * @test
     */
    public function testRunner03() {
        $this->assertEquals([
            "Error: The following argument(s) have invalid values: 'name'\n",
            "Info: Allowed values for the argument 'name':\n",
            "Ibrahim\n",
            "Ali\n"
        ], $this->executeSingleCommand(new Command00(), [
            'super-hero',
            'name' => 'Ok'
        ]));
        $this->assertEquals(-1, $this->getExitCode());
    }
    /**
     * @test
     */
    public function testRunner04() {
        $this->assertEquals([
            "\e[1;91mError: \e[0mThe following argument(s) have invalid values: 'name'\n",
            "\e[1;34mInfo: \e[0mAllowed values for the argument 'name':\n",
            "Ibrahim\n",
            "Ali\n"
        ], $this->executeSingleCommand(new Command00(), [
            'name' => 'Ok',
            '--ansi'
        ]));
        $this->assertEquals(-1, $this->getExitCode());
    }
    /**
     * @test
     */
    public function testRunner05() {
        $runner = new Runner();
        $runner->register(new Command00());
        // Don't register HelpCommand again - it's already automatically registered
        $runner->removeArgument('--ansi');
        $runner->setDefaultCommand('help');
        $runner->setInputs([]);
        $this->assertEquals(0, $runner->runCommand(null, []));
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
        $this->assertEquals([
            "Usage:\n",
            "    command [arg1 arg2=\"val\" arg3...]\n\n",
            "Available Commands:\n",
            "    help:           Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            "    super-hero:     A command to display hero's name.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner06() {
        
        $this->assertEquals([
            "Usage:\n",
            "    command [arg1 arg2=\"val\" arg3...]\n\n",
            "Global Arguments:\n",
            "    --ansi:[Optional] Force the use of ANSI output.\n",
            "Available Commands:\n",
            "    help:           Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            "    super-hero:     A command to display hero's name.\n"
        ], $this->executeMultiCommand([], [], [
            new Command00()
            // Don't register HelpCommand - it's automatically registered
        ], 'help'));
        $this->assertEquals(0, $this->getExitCode());
    }
    /**
     * @test
     */
    public function testRunner07() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->setDefaultCommand('help');
        $runner->setInputs([]);
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
            "\e[1;33m    help\e[0m:           Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            "\e[1;33m    super-hero\e[0m:     A command to display hero's name.\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function testRunner08() {
        $runner = new Runner();
        $runner->register(new Command00());
        $runner->setInputs([]);
        $this->assertEquals(0, $runner->runCommand(new HelpCommand(), [
            '--ansi',
            '--command-name' => 'super-hero'
        ]));
        $this->assertEquals([
            "\e[1;33m    super-hero\e[0m:     A command to display hero's name.\n",
            "\e[1;94m    Supported Arguments:\e[0m\n",
            "\e[1;33m                         name:\e[0m The name of the hero\n",
            "\e[1;33m                         help:\e[0m[Optional] Display command help.\n"
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
        // Don't register HelpCommand - it's automatically registered
        $runner->setDefaultCommand('help');
        $runner->setInputs([]);
        $runner->start();
        $this->assertEquals([
            "Usage:\n",
            "    command [arg1 arg2=\"val\" arg3...]\n\n",
            "Available Commands:\n",
            "    help:           Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            "    super-hero:     A command to display hero's name.\n"
        ], $runner->getOutput());
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
    }
    /**
     * @test
     */
    public function testRunner10() {
        $runner = new Runner();
        $runner->register(new Command00());
        // Don't register HelpCommand - it's automatically registered
        $runner->setInputs([]);
        $runner->setArgsVector([
            'entry.php',
            'help',
            '--command-name' => 'super-hero'
        ]);
        $runner->start();
        $this->assertEquals([
            "    super-hero:     A command to display hero's name.\n",
            "    Supported Arguments:\n",
            "                         name: The name of the hero\n",
            "                         help:[Optional] Display command help.\n"
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
            // Don't register HelpCommand - it's automatically registered
            $r->setInputs([]);
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
        // Don't register HelpCommand - it's automatically registered
        
        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInputs([
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
        // Don't register HelpCommand - it's automatically registered

        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInputs([
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
            "    help:           Display CLI Help. To display help for specific command, use the argument \"--command-name\" with this command.\n",
            "    super-hero:     A command to display hero's name.\n",
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
        // Don't register HelpCommand - it's automatically registered

        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInputs([
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
            "                         help:[Optional] Display command help.\n",
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
        // Don't register HelpCommand - it's automatically registered
        $runner->register(new WithExceptionCommand());
        $runner->setAfterExecution(function (Runner $r) {
            $r->getActiveCommand()->println('Command Exit Status: '.$r->getLastCommandExitStatus());
        });
        $runner->setArgsVector([
            'entry.php',
            '--ansi',
            '-i',
        ]);
        $runner->setInputs([
            'help --command-name=super-hero',
            'with-exception',
            'exit'
        ]);
        $runner->start();
        $output = $runner->getOutput();
        $output[12] = null;
        $this->assertEquals([
            "[1;34m>>[0m Running in interactive mode.\n",
            "[1;34m>>[0m Type command name or 'exit' to close.\n",
            "[1;34m>>[0m [1;33m    super-hero[0m:         A command to display hero's name.\n",
            "[1;94m    Supported Arguments:[0m\n",
            "[1;33m                         name:[0m The name of the hero\n",
            "Command Exit Status: 0\n",
            "[1;34m>>[0m [1;31mError:[0m An exception was thrown.\n",
            "[1;33mException Message:[0m Call to undefined method WebFiori\Tests\CLI\TestCommands\WithExceptionCommand::notExist()\n",
            "[1;33mCode:[0m 0\n",
            "[1;33mAt:[0m ".\ROOT_DIR."tests".\DS."WebFiori".\DS."Tests".\DS."Cli".\DS."TestCommands".\DS."WithExceptionCommand.php\n",
            "[1;33mLine:[0m 13\n",
            "[1;33mStack Trace:[0m \n\n",
            null,
            "Command Exit Status: -1\n",
            "[1;34m>>[0m ",
        ], $output);
    }
    /**
     * @test
     */
    public function testRunner16() {
        $runner = new Runner();
        $runner->register(new Command01());
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
        // Don't register HelpCommand - it's automatically registered
        $runner->register(new WithExceptionCommand());
        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInputs([
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
        // Don't register HelpCommand - it's automatically registered
        $runner->register(new WithExceptionCommand());
        $runner->setArgsVector([
            'entry.php',
            '--ansi',
        ]);
        $runner->setInputs([

        ]);
        $runner->start();
        //$this->assertEquals(0, $runner->start());
        // Since help command is now the default, it will show help output instead of "No command" message
        $output = $runner->getOutput();
        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Usage:', $output[0]);
    }
    /**
     * @test
     */
    public function testRunner21() {
        $runner = new Runner();
        $runner->setArgsVector([

        ]);
        $runner->setInputStream(new ArrayInputStream([

        ]));
        $runner->setOutputStream(new ArrayOutputStream());

        $this->assertEquals([

        ], $runner->getOutput());
        $runner->register(new Command00());
        // Don't register HelpCommand - it's automatically registered
        $runner->register(new WithExceptionCommand());
        $runner->setAfterExecution(function (Runner $r) {
            $r->getActiveCommand()->println('Command Exit Status: '.$r->getLastCommandExitStatus());
        });

        $runner->setArgsVector([
            'entry.php',
            'with-exception',
        ]);
        $runner->setInputs([]);
        $runner->start();
        $output = $runner->getOutput();
        //Removing the trace
        $output[6] = null;
        $this->assertEquals([
            "Error: An exception was thrown.\n",
            "Exception Message: Call to undefined method WebFiori\\Tests\Cli\\TestCommands\WithExceptionCommand::notExist()\n",
            "Code: 0\n",
            "At: ".\ROOT_DIR."tests".\DS."WebFiori".\DS."Tests".\DS."Cli".\DS."TestCommands".\DS."WithExceptionCommand.php\n",
            "Line: 13\n",
            "Stack Trace: \n\n",
            null,
            "Command Exit Status: -1\n"
        ], $output);
    }
    public function testRunner22() {
        $runner = new Runner();
        $runner->register(new Command03());
        $runner->setArgsVector([
            'entry.php',
            'run-another',
            'arg-1' => 'Nice',
            'arg-2' => 'Cool'
        ]);
        $runner->setInputStream(new ArrayInputStream([

        ]));
        $runner->setOutputStream(new ArrayOutputStream());
        $exitCode = $runner->start();
        $output = $runner->getOutput();
        $this->assertEquals([
            "Running Sub Command\n",
            "System version: 1.0.0\n",
            "Nice\n",
            "Cool\n",
            "Ur\n",
            "Done\n",
        ], $output);
    }
    /**
     * @test
     */
    public function test00() {
        $runner = new Runner();
        $runner->setInputs([]);
        $runner->setArgsVector([

        ]);
        $this->assertEquals([

        ], $runner->getOutput());
    }
    /**
     * Test Runner initialization and basic properties
     * @test
     */
    public function testRunnerInitializationEnhanced() {
        $runner = new Runner();
        
        // Test initial state
        $this->assertNull($runner->getActiveCommand());
        $this->assertNotNull($runner->getInputStream());
        $this->assertNotNull($runner->getOutputStream());
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
        $this->assertFalse($runner->isInteractive());
    }

    /**
     * Test command registration with aliases
     * @test
     */
    public function testCommandRegistrationWithAliasesEnhanced() {
        $runner = new Runner();
        $command = new TestCommand('test-cmd', [], 'Test command');
        
        // Register command with aliases
        $result = $runner->register($command, ['tc', 'test']);
        $this->assertSame($runner, $result); // Should return self for chaining
        
        // Test command is registered
        $this->assertSame($command, $runner->getCommandByName('test-cmd'));
        
        // Test aliases are registered
        $this->assertTrue($runner->hasAlias('tc'));
        $this->assertTrue($runner->hasAlias('test'));
        $this->assertEquals('test-cmd', $runner->resolveAlias('tc'));
        $this->assertEquals('test-cmd', $runner->resolveAlias('test'));
        
        // Test getting all aliases
        $aliases = $runner->getAliases();
        $this->assertArrayHasKey('tc', $aliases);
        $this->assertArrayHasKey('test', $aliases);
        $this->assertEquals('test-cmd', $aliases['tc']);
        $this->assertEquals('test-cmd', $aliases['test']);
    }

    /**
     * Test duplicate command registration
     * @test
     */
    public function testDuplicateCommandRegistrationEnhanced() {
        $runner = new Runner();
        $command1 = new TestCommand('test-cmd', [], 'First command');
        $command2 = new TestCommand('test-cmd', [], 'Second command');
        
        // Register first command
        $runner->register($command1);
        $this->assertSame($command1, $runner->getCommandByName('test-cmd'));
        
        // Register second command with same name (should replace)
        $runner->register($command2);
        $this->assertSame($command2, $runner->getCommandByName('test-cmd'));
    }

    /**
     * Test global arguments
     * @test
     */
    public function testGlobalArgumentsEnhanced() {
        $runner = new Runner();
        
        // Add global arguments
        $this->assertTrue($runner->addArg('--global-arg', [
            'optional' => true,
            'description' => 'Global argument'
        ]));
        
        // Test duplicate global argument
        $this->assertFalse($runner->addArg('--global-arg', [])); // Should fail
        
        // Test argument exists
        $this->assertTrue($runner->hasArg('--global-arg'));
        $this->assertFalse($runner->hasArg('--non-existent'));
        
        // Test removing argument
        $this->assertTrue($runner->removeArgument('--global-arg'));
        $this->assertFalse($runner->hasArg('--global-arg'));
        
        // Test removing non-existent argument
        $this->assertFalse($runner->removeArgument('--non-existent'));
    }

    /**
     * Test arguments vector handling
     * @test
     */
    public function testArgumentsVectorEnhanced() {
        $runner = new Runner();
        
        $argsVector = ['script.php', 'command', '--arg1=value1', '--arg2', 'value2'];
        $runner->setArgsVector($argsVector);
        
        $this->assertEquals($argsVector, $runner->getArgsVector());
    }

    /**
     * Test stream handling
     * @test
     */
    public function testStreamHandlingEnhanced() {
        $runner = new Runner();
        
        // Test setting custom streams
        $customInput = new ArrayInputStream(['test input']);
        $customOutput = new ArrayOutputStream();
        
        $result1 = $runner->setInputStream($customInput);
        $this->assertSame($runner, $result1); // Should return self
        $this->assertSame($customInput, $runner->getInputStream());
        
        $result2 = $runner->setOutputStream($customOutput);
        $this->assertSame($runner, $result2); // Should return self
        $this->assertSame($customOutput, $runner->getOutputStream());
    }

    /**
     * Test inputs array handling
     * @test
     */
    public function testInputsArrayHandlingEnhanced() {
        $runner = new Runner();
        
        $inputs = ['input1', 'input2', 'input3'];
        $result = $runner->setInputs($inputs);
        $this->assertSame($runner, $result); // Should return self
        
        // The inputs should be set as ArrayInputStream
        $inputStream = $runner->getInputStream();
        $this->assertInstanceOf(ArrayInputStream::class, $inputStream);
    }

    /**
     * Test command execution
     * @test
     */
    public function testCommandExecutionEnhanced() {
        $runner = new Runner();
        $command = new TestCommand('test-cmd');
        $output = new ArrayOutputStream();
        
        $runner->register($command);
        $runner->setOutputStream($output);
        
        // Test running command
        $exitCode = $runner->runCommand($command);
        $this->assertEquals(0, $exitCode); // TestCommand should return 0
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
        
        // Test running with arguments
        $exitCode2 = $runner->runCommand($command, ['--test-arg' => 'value']);
        $this->assertEquals(0, $exitCode2);
        
        // Test running with ANSI
        $exitCode3 = $runner->runCommand($command, [], true);
        $this->assertEquals(0, $exitCode3);
    }

    /**
     * Test sub-command execution
     * @test
     */
    public function testSubCommandExecutionEnhanced() {
        $runner = new Runner();
        $mainCommand = new TestCommand('main-cmd');
        $subCommand = new TestCommand('sub-cmd');
        
        $runner->register($mainCommand);
        $runner->register($subCommand);
        
        // Test running sub-command
        $exitCode = $runner->runCommandAsSub('sub-cmd');
        $this->assertEquals(0, $exitCode);
        
        // Test running non-existent sub-command
        $exitCode2 = $runner->runCommandAsSub('non-existent');
        $this->assertEquals(-1, $exitCode2);
    }

    /**
     * Test active command management
     * @test
     */
    public function testActiveCommandManagementEnhanced() {
        $runner = new Runner();
        $command = new TestCommand('test-cmd');
        
        // Initially no active command
        $this->assertNull($runner->getActiveCommand());
        
        // Set active command
        $result = $runner->setActiveCommand($command);
        $this->assertSame($runner, $result); // Should return self
        $this->assertSame($command, $runner->getActiveCommand());
        
        // Clear active command
        $runner->setActiveCommand(null);
        $this->assertNull($runner->getActiveCommand());
    }

    /**
     * Test callback functionality
     * @test
     */
    public function testCallbacksEnhanced() {
        $runner = new Runner();
        $callbackExecuted = false;
        
        // Test before start callback
        $beforeCallback = function() use (&$callbackExecuted) {
            $callbackExecuted = true;
        };
        
        $result = $runner->setBeforeStart($beforeCallback);
        $this->assertSame($runner, $result); // Should return self
        
        // Test after execution callback
        $afterCallback = function($exitCode, $command) {
            // Callback should receive exit code and command
            $this->assertIsInt($exitCode);
        };
        
        $result2 = $runner->setAfterExecution($afterCallback, ['param1', 'param2']);
        $this->assertSame($runner, $result2); // Should return self
    }

    /**
     * Test output collection
     * @test
     */
    public function testOutputCollectionEnhanced() {
        $runner = new Runner();
        $command = new TestCommand('test-cmd');
        $output = new ArrayOutputStream();
        
        $runner->register($command);
        $runner->setOutputStream($output);
        
        // Run command to generate output
        $runner->runCommand($command);
        
        // Test getting output
        $outputArray = $runner->getOutput();
        $this->assertIsArray($outputArray);
        $this->assertNotEmpty($outputArray);
    }

    /**
     * Test alias resolution edge cases
     * @test
     */
    public function testAliasResolutionEdgeCasesEnhanced() {
        $runner = new Runner();
        
        // Test resolving non-existent alias
        $this->assertNull($runner->resolveAlias('non-existent'));
        
        // Test resolving actual command name (not alias)
        $command = new TestCommand('test-cmd');
        $runner->register($command);
        $this->assertNull($runner->resolveAlias('test-cmd')); // Should return null for actual command names
    }

    /**
     * Test command retrieval edge cases
     * @test
     */
    public function testCommandRetrievalEdgeCasesEnhanced() {
        $runner = new Runner();
        
        // Test getting non-existent command
        $this->assertNull($runner->getCommandByName('non-existent'));
        
        // Test getting command by alias
        $command = new TestCommand('test-cmd');
        $runner->register($command, ['tc']);
        
        // Should not find command by alias using getCommandByName
        $this->assertNull($runner->getCommandByName('tc'));
        $this->assertSame($command, $runner->getCommandByName('test-cmd'));
    }

    /**
     * Test argument object handling
     * @test
     */
    public function testArgumentObjectHandlingEnhanced() {
        $runner = new Runner();
        
        // Test adding Argument object
        $arg = new Argument('--test-arg');
        $arg->setDescription('Test argument');
        
        $result = $runner->addArgument($arg);
        $this->assertTrue($result);
        $this->assertTrue($runner->hasArg('--test-arg'));
        
        // Test adding duplicate Argument object
        $arg2 = new Argument('--test-arg');
        $result2 = $runner->addArgument($arg2);
        $this->assertFalse($result2); // Should fail for duplicate
    }

    /**
     * Test interactive mode detection
     * @test
     */
    public function testInteractiveModeDetectionEnhanced() {
        $runner = new Runner();
        
        // Initially not interactive
        $this->assertFalse($runner->isInteractive());
        
        // Set args vector with -i flag
        $runner->setArgsVector(['script.php', '-i']);
        // Note: The actual interactive detection might depend on the start() method implementation
    }

    /**
     * Test command discovery methods (if available)
     * @test
     */
    public function testCommandDiscoveryMethodsEnhanced() {
        $runner = new Runner();
        
        // Test auto-discovery state
        $this->assertFalse($runner->isAutoDiscoveryEnabled()); // Default should be false
        
        // Test enabling auto-discovery
        $result = $runner->enableAutoDiscovery();
        $this->assertSame($runner, $result);
        $this->assertTrue($runner->isAutoDiscoveryEnabled());
        
        // Test disabling auto-discovery
        $result2 = $runner->disableAutoDiscovery();
        $this->assertSame($runner, $result2);
        $this->assertFalse($runner->isAutoDiscoveryEnabled());
        
        // Test exclude patterns
        $result5 = $runner->excludePattern('*Test*');
        $this->assertSame($runner, $result5);
        
        $result6 = $runner->excludePatterns(['*Test*', '*Mock*']);
        $this->assertSame($runner, $result6);
        
        // Test discovery cache
        $result7 = $runner->enableDiscoveryCache('test-cache.json');
        $this->assertSame($runner, $result7);
        
        $result8 = $runner->disableDiscoveryCache();
        $this->assertSame($runner, $result8);
        
        $result9 = $runner->clearDiscoveryCache();
        $this->assertSame($runner, $result9);
        
        // Test strict mode
        $result10 = $runner->setDiscoveryStrictMode(true);
        $this->assertSame($runner, $result10);
        
        $result11 = $runner->setDiscoveryStrictMode(false);
        $this->assertSame($runner, $result11);
    }
    /**
     * Test command help pattern in interactive mode.
     * @test
     */
    public function testCommandHelpInteractive() {
        $runner = new Runner();
        $runner->register(new Command00());
        // Don't register HelpCommand - it's automatically registered

        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInputs([
            'super-hero help',
            'exit'
        ]);
        $runner->start();
        
        $output = $runner->getOutput();
        
        // Should show help for super-hero command
        $this->assertContains("    super-hero:     A command to display hero's name.\n", $output);
        $this->assertContains("    Supported Arguments:\n", $output);
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
    }

    /**
     * Test command -h pattern in interactive mode.
     * @test
     */
    public function testCommandDashHInteractive() {
        $runner = new Runner();
        $runner->register(new Command00());
        // Don't register HelpCommand - it's automatically registered

        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInputs([
            'super-hero -h',
            'exit'
        ]);
        $runner->start();
        
        $output = $runner->getOutput();
        
        // Should show help for super-hero command
        $this->assertContains("    super-hero:     A command to display hero's name.\n", $output);
        $this->assertContains("    Supported Arguments:\n", $output);
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
    }

    /**
     * Test command help pattern in non-interactive mode.
     * @test
     */
    public function testCommandHelpNonInteractive() {
        $runner = new Runner();
        $runner->register(new Command00());
        // Don't register HelpCommand - it's automatically registered
        $runner->setInputs([]);

        $runner->setArgsVector([
            'entry.php',
            'super-hero',
            'help'
        ]);
        $runner->start();
        
        $output = $runner->getOutput();
        
        // Should show help for super-hero command
        $this->assertContains("    super-hero:     A command to display hero's name.\n", $output);
        $this->assertContains("    Supported Arguments:\n", $output);
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
    }

    /**
     * Test command -h pattern in non-interactive mode.
     * @test
     */
    public function testCommandDashHNonInteractive() {
        $runner = new Runner();
        $runner->register(new Command00());
        // Don't register HelpCommand - it's automatically registered
        $runner->setInputs([]);

        $runner->setArgsVector([
            'entry.php',
            'super-hero',
            '-h'
        ]);
        $runner->start();
        
        $output = $runner->getOutput();
        
        // Should show help for super-hero command
        $this->assertContains("    super-hero:     A command to display hero's name.\n", $output);
        $this->assertContains("    Supported Arguments:\n", $output);
        $this->assertEquals(0, $runner->getLastCommandExitStatus());
    }

    /**
     * Test that invalid command with help doesn't trigger help.
     * @test
     */
    public function testInvalidCommandHelp() {
        $runner = new Runner();
        $runner->register(new Command00());
        // Don't register HelpCommand - it's automatically registered

        $runner->setArgsVector([
            'entry.php',
            '-i',
        ]);
        $runner->setInputs([
            'invalid-command help',
            'exit'
        ]);
        $runner->start();
        
        $output = $runner->getOutput();
        
        // Should show error for invalid command, not help
        $this->assertContains("The command 'invalid-command' is not supported.\n", $output);
        $this->assertEquals(-1, $runner->getLastCommandExitStatus());
    }
}
