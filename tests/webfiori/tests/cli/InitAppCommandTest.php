<?php
namespace webfiori\tests\cli;

use InitAppCommand;
use PHPUnit\Framework\TestCase;
use webfiori\cli\Runner;

/**
 * Description of InitAppCommandTest
 *
 * @author Ibrahim
 */
class InitAppCommandTest extends TestCase {
    /**
     * @test
     */
    public function test00() {
        $r = new Runner();
        $r->register(new InitAppCommand());
        $r->setDefaultCommand('init');
        $r->setInputs([]);
        $r->setArgsVector([
            'app.php',
            'init'
        ]);
        $this->assertEquals(-1, $r->start());
        $this->assertEquals([
            "Error: The following required argument(s) are missing: '--dir'\n"
        ], $r->getOutput());
    }
    /**
     * @test
     */
    public function test01() {
        $r = new Runner();
        $r->register(new InitAppCommand());
        $r->setDefaultCommand('init');
        $r->setInputs([]);
        $r->setArgsVector([
            'app.php',
            'init',
            '--dir' => "test\0a"
        ]);
        $this->assertEquals(-1, $r->start());
    }
    /**
     * @test
     * @depends test01
     */
    public function test02() {
        $r = new Runner();
        $r->register(new InitAppCommand())
        ->setDefaultCommand('init')
        ->setInputs([])
        ->setArgsVector([
            'app.php',
            'init',
            '--dir' => 'test'
        ]);
        $this->assertEquals(0, $r->start());
        $this->assertEquals([
            "Creating \"test/app.php\"...\n",
            "Creating \"test/test\"...\n",
            "Success: App created successfully.\n"
        ], $r->getOutput());
    }
    /**
     * @test
     * @depends test02
     */
    public function test03() {
        $r = new Runner();
        $r->register(new InitAppCommand());
        $r->setDefaultCommand('init');
        $r->setInputs([]);
        $r->setArgsVector([
            'app.php',
            'init',
            '--dir' => 'test'
        ]);
        $this->assertEquals(0, $r->start());
        $this->assertEquals([
            "Creating \"test/app.php\"...\n",
            "Warning: File app.php already exist!\n",
            "Creating \"test/test\"...\n",
            "Warning: File test already exist!\n",
            "Success: App created successfully.\n"
        ], $r->getOutput());
        unlink(ROOT_DIR.DS.'test'.DS.'app.php');
        unlink(ROOT_DIR.DS.'test'.DS.'test');
        rmdir(ROOT_DIR.DS.'test');
    }
    /**
     * @test
     */
    public function test04() {
        $r = new Runner();
        $r->register(new InitAppCommand());
        $r->setDefaultCommand('init');
        $r->setInputs([]);
        $r->setArgsVector([
            'app.php',
            'init',
            '--dir' => 'test2',
            '--entry' => 'bang'
        ]);
        $this->assertEquals(0, $r->start());
        $this->assertEquals([
            "Creating \"test2/app.php\"...\n",
            "Creating \"test2/bang\"...\n",
            "Success: App created successfully.\n"
        ], $r->getOutput());
        unlink(ROOT_DIR.DS.'test2'.DS.'app.php');
        unlink(ROOT_DIR.DS.'test2'.DS.'bang');
        rmdir(ROOT_DIR.DS.'test2');
    }
}





