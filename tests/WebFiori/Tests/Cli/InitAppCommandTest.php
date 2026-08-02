<?php
namespace WebFiori\Tests\Cli;

use WebFiori\Cli\Commands\InitAppCommand;
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
        $output = $r->getOutput();
        // Check key elements instead of exact match due to binary string representation differences
        $this->assertCount(4, $output);
        $this->assertStringContainsString('Creating new app at', $output[0]);
        $this->assertStringContainsString('Creating "test', $output[1]);
        $this->assertStringContainsString('Error: Unable to initialize', $output[2]);
        $this->assertStringContainsString('null bytes', $output[3]);
    }
    /**
     * @test
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
        // Cleanup existing files
        $appPath = ROOT_DIR.'test';
        if (file_exists($appPath)) {
            unlink(ROOT_DIR.DS.'test'.DS.'main.php');
            unlink(ROOT_DIR.DS.'test'.DS.'HelloCommand.php');
            unlink(ROOT_DIR.DS.'test'.DS.'main');
            rmdir(ROOT_DIR.DS.'test');
        }
        $this->assertEquals(0, $r->start());
        $this->assertEquals([
            "Creating new app at \"$appPath\" ...\n",
            "Creating \"test/main.php\"...\n",
            "Creating \"test/main\"...\n",
            "Creating \"test/HelloCommand.php\"...\n",
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
        // Don't cleanup - this test expects files to exist
        $this->assertEquals(0, $r->start());
        $appPath = ROOT_DIR.'test';
        $this->assertEquals([
            "Creating new app at \"$appPath\" ...\n",
            "Creating \"test/main.php\"...\n",
            "Warning: File main.php already exist!\n",
            "Creating \"test/main\"...\n",
            "Warning: File main already exist!\n",
            "Creating \"test/HelloCommand.php\"...\n",
            "Warning: File HelloCommand.php already exist!\n",
            "Success: App created successfully.\n"
        ], $r->getOutput());
        unlink(ROOT_DIR.DS.'test'.DS.'main.php');
        unlink(ROOT_DIR.DS.'test'.DS.'HelloCommand.php');
        unlink(ROOT_DIR.DS.'test'.DS.'main');
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
        // Cleanup existing files
        $appPath = ROOT_DIR.'test2';
        if (file_exists($appPath)) {
            unlink($appPath.DS.'main.php');
            unlink($appPath.DS.'bang');
            unlink($appPath.DS.'HelloCommand.php');
            rmdir($appPath);
        }
        $this->assertEquals(0, $r->start());
        $this->assertEquals([
            "Creating new app at \"$appPath\" ...\n",
            "Creating \"test2/main.php\"...\n",
            "Creating \"test2/bang\"...\n",
            "Creating \"test2/HelloCommand.php\"...\n",
            "Success: App created successfully.\n"
        ], $r->getOutput());
        unlink($appPath.DS.'main.php');
        unlink($appPath.DS.'bang');
        unlink($appPath.DS.'HelloCommand.php');
        rmdir($appPath);
    }
}





