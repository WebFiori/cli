<?php
namespace WebFiori\Tests\CLI\Discovery;

use PHPUnit\Framework\TestCase;
use WebFiori\CLI\Exceptions\CommandDiscoveryException;

/**
 * Test cases for CommandDiscoveryException class.
 */
class CommandDiscoveryExceptionTest extends TestCase {
    
    /**
     * @test
     */
    public function testBasicException() {
        $message = 'Test exception message';
        $code = 123;
        
        $exception = new CommandDiscoveryException($message, $code);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }
    
    /**
     * @test
     */
    public function testFromErrors() {
        $errors = [
            'Error 1: Something went wrong',
            'Error 2: Another issue',
            'Error 3: Yet another problem'
        ];
        $code = 456;
        
        $exception = CommandDiscoveryException::fromErrors($errors, $code);
        
        $this->assertInstanceOf(CommandDiscoveryException::class, $exception);
        $this->assertEquals($code, $exception->getCode());
        
        $message = $exception->getMessage();
        $this->assertStringContainsString('Command discovery failed with the following errors:', $message);
        
        foreach ($errors as $error) {
            $this->assertStringContainsString($error, $message);
        }
    }
    
    /**
     * @test
     */
    public function testFromErrorsWithDefaultCode() {
        $errors = ['Single error'];
        
        $exception = CommandDiscoveryException::fromErrors($errors);
        
        $this->assertEquals(0, $exception->getCode());
        $this->assertStringContainsString('Single error', $exception->getMessage());
    }
    
    /**
     * @test
     */
    public function testFromErrorsWithEmptyArray() {
        $errors = [];
        
        $exception = CommandDiscoveryException::fromErrors($errors);
        
        $this->assertStringContainsString('Command discovery failed with the following errors:', $exception->getMessage());
    }
}
