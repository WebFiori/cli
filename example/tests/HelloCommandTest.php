<?php


require_once(realpath(dirname(__FILE__).'\\..\\..\\vendor\\autoload.php'));
require_once(realpath(dirname(__FILE__).'\\..\\app\\HelloWorldCommand.php'));


use webfiori\cli\Runner;
use PHPUnit\Framework\TestCase;

class HelloCommandTest  extends TestCase {
    /**
     * @test
     */
    public function test00() {
        $runner = new Runner();
        
        //Register the command that will be tested.
        $runner->register(new HelloWorldCommand());
        
        //Set arguments vector
        $runner->setArgsVector([
            'app.php',//First argument is always name of entry point. 
            //Can be set to anything since its testing env.
            'hello'
        ]);
        
        //Set user inputs.
        //Must be called to use Array as input and output stream even if there are no inputs.
        $runner->setInput();
        
        //Start the process
        $exitStatus = $runner->start();
        
        //Verify test results
        $this->assertEquals(0, $exitStatus);
        $this->assertEquals([
            "Hello World!\n"
        ], $runner->getOutput());
    }
    /**
     * @test
     */
    public function test01() {
        $runner = new Runner();
        $runner->register(new HelloWorldCommand());
        
        $runner->setArgsVector([
            'app.php',
            'hello',
            '--person-name' => 'Ibrahim BinAlshikh'
        ]);
        $runner->setInput();
        $exitStatus = $runner->start();
        $this->assertEquals(0, $exitStatus);
        $this->assertEquals([
            "Hello Ibrahim BinAlshikh!\n"
        ], $runner->getOutput());
    }
}

