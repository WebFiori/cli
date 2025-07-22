<?php
namespace WebFiori\Tests\Cli;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Argument;
use WebFiori\Cli\Exceptions\IOException;
use WebFiori\Cli\InputValidator;
use WebFiori\Cli\Runner;
use WebFiori\Cli\Streams\ArrayInputStream;
use WebFiori\Cli\Streams\ArrayOutputStream;
use WebFiori\Tests\Cli\TestCommand;
use WebFiori\Tests\TestStudent;

class CliCommandTest extends TestCase {
    /**
     * @test
     */
    public function moveCursorUpTest() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->moveCursorUp();
        $this->assertEquals([
            "\e[1A"
        ], $command->getOutputStream()->getOutputArray());
        $command->moveCursorUp(5);
        $this->assertEquals([
            "\e[1A",
            "\e[5A"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function moveCursorDownTest() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->moveCursorDown();
        $this->assertEquals([
            "\e[1B"
        ], $command->getOutputStream()->getOutputArray());
        $command->moveCursorDown(5);
        $this->assertEquals([
            "\e[1B",
            "\e[5B"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function moveCursorForwardTest() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->moveCursorForward();
        $this->assertEquals([
            "\e[1C"
        ], $command->getOutputStream()->getOutputArray());
        $command->moveCursorForward(5);
        $this->assertEquals([
            "\e[1C",
            "\e[5C"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function moveCursorBackwardTest() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->moveCursorBackward();
        $this->assertEquals([
            "\e[1D"
        ], $command->getOutputStream()->getOutputArray());
        $command->moveCursorBackward(5);
        $this->assertEquals([
            "\e[1D",
            "\e[5D"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testAddArg00() {
        $command = new TestCommand();
        $this->assertEquals('', $command->getName());
        $this->assertEquals([], $command->getArgs());
        $this->assertTrue($command->addArg(new Argument('--arg-1')));
        $this->assertEquals([
            '--arg-1' => new Argument('--arg-1')
        ], $command->getArgs());
        $this->assertTrue($command->addArg(new Argument('--arg-2')));
        $this->assertEquals([
            '--arg-1' => new Argument('--arg-1'),
            '--arg-2' => new Argument('--arg-2')
        ], $command->getArgs());
        $this->assertFalse($command->addArg(new Argument('--arg-2')));
        $this->assertEquals([
            '--arg-1' => new Argument('--arg-1'),
            '--arg-2' => new Argument('--arg-2')
        ], $command->getArgs());
    }
    /**
     * @test
     */
    public function testAddArgs00() {
        $command = new TestCommand();
        $this->assertEquals([], $command->getArgs());
        $this->assertEquals(0, $command->addArgs([
            
        ]));
        $this->assertEquals([], $command->getArgs());
        $this->assertEquals(1, $command->addArgs([
            '--arg-1' => [
                
            ]
        ]));
        $this->assertEquals([
            '--arg-1' => new Argument('--arg-1')
        ], $command->getArgs());
        $this->assertEquals(1, $command->addArgs([
            '--arg-2' => [
                
            ],
            '--arg-1' => [
                
            ]
        ]));
        $this->assertEquals([
            '--arg-1' => new Argument('--arg-1'),
            '--arg-2' => new Argument('--arg-2')
        ], $command->getArgs());
        $this->assertEquals(1, $command->addArgs([
            '--arg-3' => [
                
            ],
            '--arg-1' => [
                
            ]
        ]));
        $this->assertEquals([
            '--arg-1' => new Argument('--arg-1'),
            '--arg-2' => new Argument('--arg-2'),
            '--arg-3' => new Argument('--arg-3')
        ], $command->getArgs());
        $this->assertEquals(1, $command->addArgs([
            new Argument('--arg-4')
        ]));
        $this->assertEquals([
            '--arg-1' => new Argument('--arg-1'),
            '--arg-2' => new Argument('--arg-2'),
            '--arg-3' => new Argument('--arg-3'),
            '--arg-4' => new Argument('--arg-4')
        ], $command->getArgs());
    }
    /**
     * @test
     */
    public function testAddOption00() {
        $command = new TestCommand();
        $this->assertEquals([], $command->getOptions());
        $command->addOption('option-1', 'Hello');
        $this->assertEquals([
            'option-1' => 'Hello'
        ], $command->getOptions());
        $command->addOption('option-2', 'World');
        $this->assertEquals([
            'option-1' => 'Hello',
            'option-2' => 'World'
        ], $command->getOptions());
        $command->addOption('option-1', 'New Val');
        $this->assertEquals([
            'option-1' => 'New Val',
            'option-2' => 'World'
        ], $command->getOptions());
    }
    /**
     * @test
     */
    public function testClear00() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->clear();
        $this->assertEquals([
            "\e[2J\e[;H"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm00() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'y'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertTrue($command->confirm());
        $this->assertEquals([
            "Are you sure you want to continue? (yes/y, no/n): "
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm01() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'n'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertFalse($command->confirm());
        $this->assertEquals([
            "Are you sure you want to continue? (yes/y, no/n): "
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm02() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'YES'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertTrue($command->confirm());
        $this->assertEquals([
            "Are you sure you want to continue? (yes/y, no/n): "
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm03() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'NO'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertFalse($command->confirm());
        $this->assertEquals([
            "Are you sure you want to continue? (yes/y, no/n): "
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm04() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'ok'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertFalse($command->confirm('Are you ok?', [
            'y' => ['ok'],
            'n' => ['not ok']
        ]));
        $this->assertEquals([
            "Are you ok? (ok, not ok): "
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm05() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'not ok'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertFalse($command->confirm('Are you ok?', [
            'y' => ['ok'],
            'n' => ['not ok']
        ]));
        $this->assertEquals([
            "Are you ok? (ok, not ok): "
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testError00() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->error('This is error.');
        $this->assertEquals([
            "\e[1;31mError:\e[0m This is error.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testError01() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->error('This is error with %s.', 'OK');
        $this->assertEquals([
            "\e[1;31mError:\e[0m This is error with OK.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testExecSubCommand00() {
        $runner = new Runner();
        $command = new TestCommand();
        $command->setRunner($runner);
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals(-1, $command->execSubCommand('not-exist'));
        $this->assertEquals([
            "\e[1;31mError:\e[0m Command 'not-exist' is not registered.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testExecSubCommand01() {
        $runner = new Runner();
        $command = new TestCommand();
        $command->setRunner($runner);
        $command->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello'));
        $this->assertEquals(0, $command->execSubCommand('hello'));
        $this->assertEquals([
            "Hello World!\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testExecSubCommand02() {
        $runner = new Runner();
        $command = new TestCommand();
        $command->setRunner($runner);
        $command->setOutputStream(new ArrayOutputStream());
        $runner->register(new TestCommand('hello', [
            '--name' => [
                'optional' => true
            ]
        ]));
        $this->assertEquals(0, $command->execSubCommand('hello', [
            '--name' => 'Ibrahim'
        ]));
        $this->assertEquals([
            "Hello Ibrahim!\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testGetArg00() {
        $command = new TestCommand();
        $this->assertNull($command->getArg('--not-exist'));
        $command->addArg(new Argument('--arg-1'));
        $this->assertNull($command->getArg('--not-exist'));
        $this->assertTrue($command->getArg('--arg-1') instanceof Argument);
    }
    /**
     * @test
     */
    public function testGetArgValue00() {
        $command = new TestCommand();
        $this->assertNull($command->getArgValue('--not-exist'));
        $command->addArg(new Argument('--arg-1'));
        $this->assertNull($command->getArgValue('--arg-1'));
        $command->getArg('--arg-1')->setValue('Hello');
        $this->assertEquals('Hello', $command->getArgValue('--arg-1'));
    }
    /**
     * @test
     */
    public function testGetArgValue01() {
        $command = new TestCommand();
        $this->assertNull($command->getArgValue('--not-exist'));
        $arg = new Argument('--arg-1');
        $arg->setIsOptional(true);
        $arg->setDefault('Hello');
        $command->addArg($arg);
        $this->assertEquals('Hello', $command->getArgValue('--arg-1'));
    }
    /**
     * @test
     */
    public function testGetOption00() {
        $command = new TestCommand();
        $this->assertNull($command->getOption('not-exist'));
        $command->addOption('option-1', 'Hello');
        $this->assertEquals('Hello', $command->getOption('option-1'));
        $this->assertNull($command->getOption('not-exist'));
    }
    /**
     * @test
     */
    public function testHasArg00() {
        $command = new TestCommand();
        $this->assertFalse($command->hasArg('--not-exist'));
        $command->addArg(new Argument('--arg-1'));
        $this->assertTrue($command->hasArg('--arg-1'));
        $this->assertFalse($command->hasArg('--not-exist'));
    }
    /**
     * @test
     */
    public function testHasOption00() {
        $command = new TestCommand();
        $this->assertFalse($command->hasOption('not-exist'));
        $command->addOption('option-1', 'Hello');
        $this->assertTrue($command->hasOption('option-1'));
        $this->assertFalse($command->hasOption('not-exist'));
    }
    /**
     * @test
     */
    public function testInfo00() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->info('This is info.');
        $this->assertEquals([
            "\e[1;34mInfo:\e[0m This is info.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testInfo01() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->info('This is info with %s.', 'OK');
        $this->assertEquals([
            "\e[1;34mInfo:\e[0m This is info with OK.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testIsInteractive00() {
        $command = new TestCommand();
        $this->assertFalse($command->isInteractive());
        $runner = new Runner();
        $command->setRunner($runner);
        $this->assertFalse($command->isInteractive());
        $runner->setArgsVector([
            'app.php',
            '-i'
        ]);
        $runner->_checkIsInteractive();
        $this->assertTrue($command->isInteractive());
    }
    /**
     * @test
     */
    public function testIsVerbose00() {
        $command = new TestCommand();
        $this->assertFalse($command->isVerbose());
        $runner = new Runner();
        $command->setRunner($runner);
        $this->assertFalse($command->isVerbose());
        $runner->setArgsVector([
            'app.php',
            '-v'
        ]);
        $runner->_checkIsVerbose();
        $this->assertTrue($command->isVerbose());
    }
    /**
     * @test
     */
    public function testPrints00() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->prints('Hello World!');
        $this->assertEquals([
            "Hello World!"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testPrints01() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->prints('Hello %s!', 'Ibrahim');
        $this->assertEquals([
            "Hello Ibrahim!"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testPrintln00() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->println('Hello World!');
        $this->assertEquals([
            "Hello World!\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testPrintln01() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->println('Hello %s!', 'Ibrahim');
        $this->assertEquals([
            "Hello Ibrahim!\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testPrintln02() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->println();
        $this->assertEquals([
            "\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testRead00() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'Hello World!'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals('H', $command->read());
        $this->assertEquals([], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testRead01() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'Hello World!'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals('Hello', $command->read('', 5));
        $this->assertEquals([], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testRead02() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'Hello World!'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals('H', $command->read('Enter Something:'));
        $this->assertEquals([
            "Enter Something:"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadln00() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'Hello World!'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals('Hello World!', $command->readln());
        $this->assertEquals([], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadln01() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'Hello World!'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals('Hello World!', $command->readln('Enter Something:'));
        $this->assertEquals([
            "Enter Something:"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadMultiple00() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'Hello World!',
            'Second Line.',
            'exit'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals("Hello World!Second Line.", $command->readMultiple());
        $this->assertEquals([
            "Enter 'exit' in a new line to finish.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadMultiple01() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'Hello World!',
            'Second Line.',
            'exit'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals("Hello World!Second Line.", $command->readMultiple('Enter multiple lines:'));
        $this->assertEquals([
            "Enter multiple lines:\n",
            "Enter 'exit' in a new line to finish.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadMultiple02() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            'Hello World!',
            'Second Line.',
            'ok'
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals("Hello World!Second Line.", $command->readMultiple('Enter multiple lines:', 'ok'));
        $this->assertEquals([
            "Enter multiple lines:\n",
            "Enter 'ok' in a new line to finish.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadPassword00() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            "Hello\n"
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals('Hello', $command->readPassword());
        $this->assertEquals([], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadPassword01() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            "Hello\n"
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $this->assertEquals('Hello', $command->readPassword('Enter Password:'));
        $this->assertEquals([
            "Enter Password:"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadValue00() {
        $command = new TestCommand();
        $command->setInputStream(new ArrayInputStream([
            "Hello",
            "8"
        ]));
        $command->setOutputStream(new ArrayOutputStream());
        $validator = new InputValidator(function($val)
        {
            return InputValidator::isInt($val);
        }, 'Enter a number.');
        $this->assertEquals('8', $command->readValue('Enter a number:', $validator));
        $this->assertEquals([
            "Enter a number:",
            "\e[1;31mError:\e[0m Enter a number.\n",
            "Enter a number:"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testRemoveArg00() {
        $command = new TestCommand();
        $this->assertFalse($command->removeArg('--not-exist'));
        $command->addArg(new Argument('--arg-1'));
        $this->assertTrue($command->removeArg('--arg-1'));
        $this->assertFalse($command->removeArg('--arg-1'));
    }
    /**
     * @test
     */
    public function testRemoveOption00() {
        $command = new TestCommand();
        $this->assertFalse($command->removeOption('not-exist'));
        $command->addOption('option-1', 'Hello');
        $this->assertTrue($command->removeOption('option-1'));
        $this->assertFalse($command->removeOption('option-1'));
    }
    /**
     * @test
     */
    public function testSetDescription00() {
        $command = new TestCommand();
        $this->assertEquals('', $command->getDescription());
        $command->setDescription('Hello World!');
        $this->assertEquals('Hello World!', $command->getDescription());
    }
    /**
     * @test
     */
    public function testSetName00() {
        $command = new TestCommand();
        $this->assertEquals('', $command->getName());
        $this->assertTrue($command->setName('hello'));
        $this->assertEquals('hello', $command->getName());
        $this->assertFalse($command->setName(''));
        $this->assertEquals('hello', $command->getName());
        $this->assertFalse($command->setName('hello world'));
        $this->assertEquals('hello', $command->getName());
    }
    /**
     * @test
     */
    public function testSuccess00() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->success('This is success.');
        $this->assertEquals([
            "\e[1;32mSuccess:\e[0m This is success.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSuccess01() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->success('This is success with %s.', 'OK');
        $this->assertEquals([
            "\e[1;32mSuccess:\e[0m This is success with OK.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testValidateArgs00() {
        $command = new TestCommand();
        $this->assertTrue($command->validateArgs());
        $command->addArg(new Argument('--arg-1'));
        $this->assertFalse($command->validateArgs());
        $command->getArg('--arg-1')->setIsOptional(true);
        $this->assertTrue($command->validateArgs());
    }
    /**
     * @test
     */
    public function testValidateArgs01() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->addArg(new Argument('--arg-1'));
        $this->assertFalse($command->validateArgs());
        $this->assertEquals([
            "\e[1;31mError:\e[0m The argument '--arg-1' is required.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testValidateArgs02() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $arg = new Argument('--arg-1');
        $arg->addAllowedValue('Hello');
        $command->addArg($arg);
        $arg->setValue('Hello');
        $this->assertTrue($command->validateArgs());
        $arg->setValue('Not Allowed');
        $this->assertFalse($command->validateArgs());
        $this->assertEquals([
            "\e[1;31mError:\e[0m The value 'Not Allowed' is not allowed for the argument '--arg-1'.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testWarning00() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->warning('This is warning.');
        $this->assertEquals([
            "\e[1;33mWarning:\e[0m This is warning.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testWarning01() {
        $command = new TestCommand();
        $command->setOutputStream(new ArrayOutputStream());
        $command->warning('This is warning with %s.', 'OK');
        $this->assertEquals([
            "\e[1;33mWarning:\e[0m This is warning with OK.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
}
