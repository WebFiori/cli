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
        $r->start();
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
        $r->start();
        $this->assertEquals([
            "Creating \"test\0a/app.php\"...\n",
            "Error: Unable to initialize due to an exception:\n",
            "2 - file_exists() expects parameter 1 to be a valid path, string given At class File line 502\n"
        ], $r->getOutput());
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
        ])->start();
        $this->assertEquals([
            "Creating \"test/app.php\"...\n",
            "Creating \"test/test.sh\"...\n",
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
        $r->start();
        $this->assertEquals([
            "Creating \"test/app.php\"...\n",
            "Warning: File app.php already exist!\n",
            "Creating \"test/test.sh\"...\n",
            "Warning: File test.sh already exist!\n",
            "Success: App created successfully.\n"
        ], $r->getOutput());
    }
}
