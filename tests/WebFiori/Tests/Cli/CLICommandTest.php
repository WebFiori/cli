<?php
namespace WebFiori\Tests\Cli;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Argument;
use WebFiori\Cli\Exceptions\IOException;
use WebFiori\Cli\InputValidator;
use WebFiori\Cli\Runner;
use WebFiori\Cli\Streams\ArrayInputStream;
use WebFiori\Cli\Streams\ArrayOutputStream;
use WebFiori\Tests\TestStudent;


class CLICommandTest extends TestCase {
    /**
     * @test
     */
    public function moveCursorUpTest() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->moveCursorUp();
        $command->moveCursorUp(-1);
        $command->moveCursorUp(35);
        $this->assertEquals([
            "\e[1A\e[35A"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSussess00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->success('All is ok');
        $this->assertEquals([
            "Success: All is ok\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSussess01() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->success('All is ok');
        $this->assertEquals([
            "\e[1;92mSuccess: \e[0mAll is ok\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testInfo00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->info('Note that all files where uploaded.');
        $this->assertEquals([
            "Info: Note that all files where uploaded.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testInfo01() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->info('Note that all files where uploaded.');
        $this->assertEquals([
            "\e[1;34mInfo: \e[0mNote that all files where uploaded.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testWarning00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->warning('Part of the info was not logged.');
        $this->assertEquals([
            "Warning: Part of the info was not logged.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testWarning01() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->warning('Part of the info was not logged.');
        $this->assertEquals([
            "\e[1;93mWarning: \e[0mPart of the info was not logged.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testError00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->error('An exception was thrown.');
        $this->assertEquals([
            "Error: An exception was thrown.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testError01() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->error('An exception was thrown.');
        $this->assertEquals([
            "\e[1;91mError: \e[0mAn exception was thrown.\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSelect00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '1'
        ]));
        $answer = $command->select('Select a value:', [
            'First',
            'Second',
            'Third'
        ]);
        $this->assertEquals('Second', $answer);
        $this->assertEquals([
            "Select a value:\n",
            "0: First\n",
            "1: Second\n",
            "2: Third\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSelect01() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'Third'
        ]));
        $answer = $command->select('Select a value:', [
            'First',
            'Second',
            'Third'
        ]);
        $this->assertEquals('Third', $answer);
        $this->assertEquals([
            "Select a value:\n",
            "0: First\n",
            "1: Second\n",
            "2: Third\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSelect02() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'ok',
            '3',
            'First'
        ]));
        $answer = $command->select('Select a value:', [
            'First',
            'Second',
            'Third'
        ]);
        $this->assertEquals('First', $answer);
        $this->assertEquals([
            "Select a value:\n",
            "0: First\n",
            "1: Second\n",
            "2: Third\n",
            "Error: Invalid answer.\n",
            "Select a value:\n",
            "0: First\n",
            "1: Second\n",
            "2: Third\n",
            "Error: Invalid answer.\n",
            "Select a value:\n",
            "0: First\n",
            "1: Second\n",
            "2: Third\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSelect03() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'ok',
            'First'
        ]));
        $answer = $command->select('Select a value:', [
            'First',
            'Second',
            'Third'
        ], 3);
        $this->assertEquals('First', $answer);
        $this->assertEquals([
            "Select a value:\n",
            "0: First\n",
            "1: Second\n",
            "2: Third\n",
            "Error: Invalid answer.\n",
            "Select a value:\n",
            "0: First\n",
            "1: Second\n",
            "2: Third\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSelect04() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'ok',
            ''
        ]));
        $answer = $command->select('Select a value:', [
            'First',
            'Second',
            'Third'
        ], 2);
        $this->assertEquals('Third', $answer);
        $this->assertEquals([
            "Select a value:\n",
            "0: First\n",
            "1: Second\n",
            "2: Third <--\n",
            "Error: Invalid answer.\n",
            "Select a value:\n",
            "0: First\n",
            "1: Second\n",
            "2: Third <--\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSelect05() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'ok',
            ''
        ]));
        $answer = $command->select('Select a value:', [
            'First',
            'Second',
            'Third'
        ], 2);
        $this->assertEquals('Third', $answer);
        $this->assertEquals([
            "\e[1;37mSelect a value:\e[0m\n",
            "0: First\n",
            "1: Second\n",
            "\e[1;94m2: Third\e[0m <--\n",
            "\e[1;91mError: \e[0mInvalid answer.\n",
            "\e[1;37mSelect a value:\e[0m\n",
            "0: First\n",
            "1: Second\n",
            "\e[1;94m2: Third\e[0m <--\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSelect06() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '1'
        ]));
        $answer = $command->select('Select a value:', [
            'one' => 'First',
            'Second',
            'th' => 'Third'
        ], 2);
        $this->assertEquals('Second', $answer);
        $this->assertEquals([
            "Select a value:\n",
            "0: First\n",
            "1: Second\n",
            "2: Third <--\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testSelect07() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '1'
        ]));
        $answer = $command->select('Select a value:', [

        ], 2);
        $this->assertNull($answer);
        $this->assertEquals([

        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testMoveCursorTo() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->moveCursorTo();
        $command->moveCursorTo(-1, 3);
        $command->moveCursorTo(3, -1);
        $command->moveCursorTo(44, 3);
        $this->assertEquals([
            "\e[0;0H\e[44;3H"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm00() {
        $command = new TestCommand('cool');
        $this->assertNotNull($command->getInputStream());
        $this->assertNotNull($command->getOutputStream());
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'y'
        ]));
        $this->assertTrue($command->confirm('Are you sure?'));
        $this->assertEquals([
            "Are you sure?(y/n)\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm01() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'n'
        ]));
        $this->assertFalse($command->confirm('Are you sure?'));
        $this->assertEquals([
            "Are you sure?(y/n)\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm02() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'hell',
            'y'
        ]));
        $this->assertTrue($command->confirm('Are you sure?'));
        $this->assertEquals([
            "Are you sure?(y/n)\n",
            "Error: Invalid answer. Choose 'y' or 'n'.\n",
            "Are you sure?(y/n)\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm03() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'hell',
            'y'
        ]));
        $this->assertTrue($command->confirm('Are you sure?'));
        $this->assertEquals([
            "\e[1;37mAre you sure?\e[0m\e[94m(y/n)\e[0m\n",
            "\e[1;91mError: \e[0mInvalid answer. Choose 'y' or 'n'.\n",
            "\e[1;37mAre you sure?\e[0m\e[94m(y/n)\e[0m\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm04() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            " \n"
        ]));
        $this->assertTrue($command->confirm('Are you sure?  ', true));
        $this->assertEquals([
            "\e[1;37mAre you sure?\e[0m\e[94m(Y/n)\e[0m\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testConfirm05() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            "\n"
        ]));
        $this->assertFalse($command->confirm('Are you sure?', false));
        $this->assertEquals([
            "\e[1;37mAre you sure?\e[0m\e[94m(y/N)\e[0m\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testGetInput00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            "My Name Is Ibrahim\r\n",
        ]));
        $input = $command->getInput('   ');
        $this->assertNull($input);
        $input = $command->getInput('Give me Your Name: ');
        $this->assertEquals('My Name Is Ibrahim', $input);
    }
    /**
     * @test
     */
    public function testGetInput01() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'My Name Is Ibrahim',
        ]));
        $input = $command->getInput('   ');
        $this->assertNull($input);
        $input = $command->getInput('Give me Your Name: ');
        $this->assertEquals('My Name Is Ibrahim', $input);
        $this->AssertEquals([
            "\e[1;37mGive me Your Name:\e[0m\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testGetInput02() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            "",
        ]));
        $input = $command->getInput('Give me Your Name: ', "Demon Lord");
        $this->assertEquals('Demon Lord', $input);
        $this->AssertEquals([
            "\e[1;37mGive me Your Name:\e[0m\e[94m Enter = 'Demon Lord'\e[0m\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testGetInput03() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            "",
        ]));
        $input = $command->getInput('Give me Your Name: ');
        $this->assertEquals('', $input);
        $this->AssertEquals([
            "\e[1;37mGive me Your Name:\e[0m\n"
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testGetInput04() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            "",
            "SisPro"
        ]));
        $input = $command->getInput('Give me Your Name: ', null, new InputValidator(function ($val) {
            $trim = trim($val);
            if (strlen($val) == 0) {
                return false;
            }
            return true;
        }));
        $this->assertEquals('SisPro', $input);
        $this->AssertEquals([
            "\e[1;37mGive me Your Name:\e[0m\n",
            "\e[1;91mError: \e[0mInvalid input is given. Try again.\n",
            "\e[1;37mGive me Your Name:\e[0m\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testGetInput05() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            "",
            "SisPro"
        ]));
        $input = $command->getInput('Give me Your Name: ', null, new InputValidator(function ($val) {
            $trim = trim($val);
            if (strlen($val) == 0) {
                return false;
            }
            return true;
        }, 'Wrong Input.'));
        $this->assertEquals('SisPro', $input);
        $this->AssertEquals([
            "\e[1;37mGive me Your Name:\e[0m\n",
            "\e[1;91mError: \e[0mWrong Input.\n",
            "\e[1;37mGive me Your Name:\e[0m\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testGetInput06() {
        $command = new TestCommand('cool');
        $ansiArg = new Argument('--ansi');
        $ansiArg->setValue('');
        $command->addArgument($ansiArg);
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            "SisPro"
        ]));
        $input = $command->getInput('Give me Your Name: ', null, new InputValidator(function ($val, $hello) {
            if ($hello == 'Hello') {
                return true;
            }
            return true;
        }, 'Wrong Input.', [
            'Hello'
        ]));
        $this->assertEquals('SisPro', $input);
        $this->AssertEquals([
            "\e[1;37mGive me Your Name:\e[0m\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testRead00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '445',
            "Hello World!\r\n",
            "Super"
        ]));
        $this->assertEquals('445', $command->readln());
        $this->assertEquals('Hello', $command->read(5));
        $this->assertEquals(" World!\r\n", $command->readln());
        $this->assertEquals('Super', $command->readln());
    }
    /**
     * @test
     */
    public function testReadInteger00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '445',
        ]));
        $input = $command->readInteger('Give me an integer:');
        $this->assertSame(445, $input);
    }
    /**
     * @test
     */
    public function testReadInteger01() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '',
        ]));
        $input = $command->readInteger('Give me an integer:', 88);
        $this->assertSame(88, $input);
    }
    /**
     * @test
     */
    public function testReadInteger02() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'uu8',
            '998&9',
            '100'
        ]));
        $input = $command->readInteger('Give me an integer:', 88);
        $this->assertSame(100, $input);
        $this->assertequals([
            "Give me an integer: Enter = '88'\n",
            "Error: Provided value is not an integer!\n",
            "Give me an integer: Enter = '88'\n",
            "Error: Provided value is not an integer!\n",
            "Give me an integer: Enter = '88'\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadFloat00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '445.1',
        ]));
        $input = $command->readFloat('Give me a float:');
        $this->assertSame(445.1, $input);
    }
    /**
     * @test
     */
    public function testReadFloat01() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '',
        ]));
        $input = $command->readFloat('Give me a float:', 88.98876);
        $this->assertSame(88.98876, $input);
    }
    /**
     * @test
     */
    public function testReadFloat02() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'u.u8',
            '998.9.9',
            '100.998'
        ]));
        $input = $command->readFloat('Give me a float:', 88);
        $this->assertSame(100.998, $input);
        $this->assertequals([
            "Give me a float: Enter = '88'\n",
            "Error: Provided value is not a floating number!\n",
            "Give me a float: Enter = '88'\n",
            "Error: Provided value is not a floating number!\n",
            "Give me a float: Enter = '88'\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadInstance00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '\\webfiori\\tests\\TestStudentXO',
            '\\webfiori\\tests\\TestStudent',
        ]));
        $input = $command->readInstance('Give me class:', 'Not a class!');
        $this->assertTrue($input instanceof TestStudent);
        $this->assertequals([
            "Give me class:\n",
            "Error: Not a class!\n",
            "Give me class:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadInstance01() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '\\webfiori\\tests\\TestStudent2',
            '\\webfiori\\tests\\TestStudent',
        ]));
        $input = $command->readInstance('Give me class:', 'Not a class!');
        $this->assertTrue($input instanceof TestStudent);
        $this->assertequals([
            "Give me class:\n",
            "Error: Not a class!\n",
            "Give me class:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadClassName00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'SuperClass',
        ]));
        $input = $command->readClassName('Give me class name:');
        $this->assertEquals('SuperClass', $input);
        $this->assertequals([
            "Give me class name:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadClassName01() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'Super Class',
            "ValidSuper"
        ]));
        $input = $command->readClassName('Give me class name:', null, 'Not valid Class Name!');
        $this->assertEquals('ValidSuper', $input);
        $this->assertequals([
            "Give me class name:\n",
            "Error: Not valid Class Name!\n",
            "Give me class name:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadClassName02() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'Super Class',
            "ValidSuper"
        ]));
        $input = $command->readClassName('Give me class name:', 'Suffix', 'Not valid Class Name!');
        $this->assertEquals('ValidSuperSuffix', $input);
        $this->assertequals([
            "Give me class name:\n",
            "Error: Not valid Class Name!\n",
            "Give me class name:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadClassName03() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'Super Class',
            "ValidSuperXUYYS"
        ]));
        $input = $command->readClassName('Give me class name:', 'XUYYS', 'Not valid Class Name!');
        $this->assertEquals('ValidSuperXUYYS', $input);
        $this->assertequals([
            "Give me class name:\n",
            "Error: Not valid Class Name!\n",
            "Give me class name:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadClassName04() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'Super Class',
            "ValidSuperXUYYS"
        ]));
        $input = $command->readClassName('Give me class name:', '12X', 'Not valid Class Name!');
        $this->assertEquals('ValidSuperXUYYS12X', $input);
        $this->assertequals([
            "Give me class name:\n",
            "Error: Not valid Class Name!\n",
            "Give me class name:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadNamespace00() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '\\webfiori\\tests\\',
        ]));
        $input = $command->readNamespace('Give me class namespace:');
        $this->assertEquals('\\webfiori\\tests\\', $input);
        $this->assertequals([
            "Give me class namespace:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadNamespace01() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '\\webfiori\\tests',
        ]));
        $input = $command->readNamespace('Give me class namespace:');
        $this->assertEquals('\\webfiori\\tests', $input);
        $this->assertequals([
            "Give me class namespace:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadNamespace02() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            'webfiori\\tests',
        ]));
        $input = $command->readNamespace('Give me class namespace:');
        $this->assertEquals('webfiori\\tests', $input);
        $this->assertequals([
            "Give me class namespace:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadNamespace03() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '/webfiori\\tests',
            "",
            'webfiori\\tests',
        ]));
        $input = $command->readNamespace('Give me class namespace:', null, "Please provide a valid NS!");
        $this->assertEquals('webfiori\\tests', $input);
        $this->assertequals([
            "Give me class namespace:\n",
            "Error: Please provide a valid NS!\n",
            "Give me class namespace:\n",
            "Error: Please provide a valid NS!\n",
            "Give me class namespace:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadNamespace04() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '\\',
        ]));
        $input = $command->readNamespace('Give me class namespace:', null, "Please provide a valid NS!");
        $this->assertEquals('\\', $input);
        $this->assertequals([
            "Give me class namespace:\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadNamespace05() {
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '',
        ]));
        $input = $command->readNamespace('Give me class namespace:', 'wfx\\xyz', "Please provide a valid NS!");
        $this->assertEquals('wfx\\xyz', $input);
        $this->assertequals([
            "Give me class namespace: Enter = 'wfx\xyz'\n",
        ], $command->getOutputStream()->getOutputArray());
    }
    /**
     * @test
     */
    public function testReadNamespace06() {
        $this->expectException(IOException::class);
        $this->expectExceptionMessage('Provided default namespace is not valid.');
        
        $command = new TestCommand('cool');
        $command->setOutputStream(new ArrayOutputStream());
        $command->setInputStream(new ArrayInputStream([
            '',
        ]));
        $input = $command->readNamespace('Give me class namespace:', 'wfx//xyz', "Please provide a valid NS!");
        $this->assertEquals('wfx\\xyz', $input);
        $this->assertequals([
            "Give me class namespace: Enter = 'wfx\xyz'\n",
        ], $command->getOutputStream()->getOutputArray());
    }
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
        $this->assertEquals([
            'valid',
            '--valid-name',
            '0invalid',
            'valid-1'
        ], $command->getArgsNames());
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
    public function testAddArg05() {
        $command = new TestCommand('new');
        $this->assertTrue($command->addArg('default-options', [
            'optional' => true,
            'description' => ' ',
            'default' => 'ok , good '
        ]));
        $arg = $command->getArg('default-options');
        $this->assertEquals('<NO DESCRIPTION>', $arg->getDescription());
        $this->assertEquals('ok , good', $arg->getDefault());
    }
    /**
     * @test
     */
    public function testClear00() {
        $runner = new Runner();
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
        $runner->setInputs([]);
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
            "- one\n",
            "- two\n",
            "- three\n"
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
