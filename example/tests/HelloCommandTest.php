<?php

use WebFiori\Cli\CommandTestCase;


require_once(realpath(dirname(__FILE__).'\\..\\..\\vendor\\autoload.php'));
require_once(realpath(dirname(__FILE__).'\\..\\app\\HelloWorldCommand.php'));


class HelloCommandTest  extends CommandTestCase {
    /**
     * @test
     */
    public function test00() {
        //A basic test case without using arg vector or user inputs
        $this->assertEquals([
            "Hello World!".self::NL
        ], $this->executeSingleCommand([new HelloWorldCommand()]));
    }
    /**
     * @test
     */
    public function test01() {
        //A test case that uses arg vector
        $this->assertEquals([
            "Hello Ibrahim BinAlshikh!\n".self::NL
        ], $this->executeSingleCommand(new HelloWorldCommand(), [
            '--person-name' => 'Ibrahim BinAlshikh'
        ]));
    }
}

