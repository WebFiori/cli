<?php
namespace WebFiori\Tests\Cli;

use InitAppCommand;
use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Runner;

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
            'main.php',
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
            'main.php',
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
            'main.php',
            'init',
            '--dir' => 'test'
        ]);
        $appPath = ROOT_DIR.DS.'test';
        $this->assertEquals(0, $r->start());
        $this->assertEquals([
            "Creating new app at \"$appPath\" ...\n",
            "Creating \"test/main.php\"...\n",
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
            'main.php',
            'init',
            '--dir' => 'test'
        ]);
        $this->assertEquals(0, $r->start());
        $appPath = ROOT_DIR.DS.'test';
        $this->assertEquals([
            "Creating new app at \"$appPath\" ...\n",
            "Creating \"test/main.php\"...\n",
            "Warning: File main.php already exist!\n",
            "Creating \"test/test\"...\n",
            "Warning: File test already exist!\n",
            "Success: App created successfully.\n"
        ], $r->getOutput());
        unlink(ROOT_DIR.DS.'test'.DS.'main.php');
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
            'main.php',
            'init',
            '--dir' => 'test2',
            '--entry' => 'bang'
        ]);
        $this->assertEquals(0, $r->start());
        $appPath = ROOT_DIR.DS.'test2';
        $this->assertEquals([
            "Creating new app at \"$appPath\" ...\n",
            "Creating \"test2/main.php\"...\n",
            "Creating \"test2/bang\"...\n",
            "Success: App created successfully.\n"
        ], $r->getOutput());
        unlink($appPath.DS.'main.php');
        unlink($appPath.DS.'bang');
        rmdir($appPath);
    }
}



