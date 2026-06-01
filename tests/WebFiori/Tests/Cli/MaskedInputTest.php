<?php
declare(strict_types=1);

namespace WebFiori\Tests\Cli;

use WebFiori\Cli\CommandTestCase;
use WebFiori\Cli\InputValidator;
use WebFiori\Cli\Streams\ArrayInputStream;
use WebFiori\Cli\Streams\ArrayOutputStream;

/**
 * Test cases for masked input functionality.
 */
class MaskedInputTest extends CommandTestCase {
    
    /**
     * Test basic masked input functionality.
     * 
     * @test
     */
    public function testBasicMaskedInput() {
        $command = new class extends \WebFiori\Cli\Command {
            public function __construct() {
                parent::__construct('test-masked');
            }
            
            public function exec(): int {
                $input = $this->getMaskedInput('Enter password: ');
                $this->println("Password received: $input");
                return 0;
            }
        };
        
        $output = $this->executeSingleCommand($command, [], ['secret123']);
        
        $this->assertContains("Enter password:\n", $output);
        $this->assertContains("Password received: secret123\n", $output);
        $this->assertEquals(0, $this->getExitCode());
    }
    
    /**
     * Test masked input with default value.
     * 
     * @test
     */
    public function testMaskedInputWithDefault() {
        $command = new class extends \WebFiori\Cli\Command {
            public function __construct() {
                parent::__construct('test-masked-default');
            }
            
            public function exec(): int {
                $input = $this->getMaskedInput('Enter token: ', '*', 'default-token');
                $this->println("Token: $input");
                return 0;
            }
        };
        
        // Test with empty input (should use default)
        $output = $this->executeSingleCommand($command, [], ['']);
        
        $this->assertContains("Enter token: Enter = 'default-token'\n", $output);
        $this->assertContains("Token: default-token\n", $output);
    }
    
    /**
     * Test masked input with custom mask character.
     * 
     * @test
     */
    public function testMaskedInputWithCustomMask() {
        $command = new class extends \WebFiori\Cli\Command {
            public function __construct() {
                parent::__construct('test-custom-mask');
            }
            
            public function exec(): int {
                $input = $this->getMaskedInput('Enter PIN: ', '#');
                $this->println("PIN: $input");
                return 0;
            }
        };
        
        $output = $this->executeSingleCommand($command, [], ['1234']);
        
        $this->assertContains("Enter PIN:\n", $output);
        $this->assertContains("PIN: 1234\n", $output);
    }
    
    /**
     * Test masked input with validation.
     * 
     * @test
     */
    public function testMaskedInputWithValidation() {
        $command = new class extends \WebFiori\Cli\Command {
            public function __construct() {
                parent::__construct('test-masked-validation');
            }
            
            public function exec(): int {
                $validator = new InputValidator(function($input) {
                    return strlen($input) >= 8;
                }, 'Password must be at least 8 characters long!');
                
                $input = $this->getMaskedInput('Enter password: ', '*', null, $validator);
                $this->println("Valid password received");
                return 0;
            }
        };
        
        // Test with invalid input first, then valid
        $output = $this->executeSingleCommand($command, [], ['short', 'validpassword']);
        
        $this->assertContains("Error: Password must be at least 8 characters long!\n", $output);
        $this->assertContains("Valid password received\n", $output);
    }
    
    /**
     * Test masked input with empty prompt.
     * 
     * @test
     */
    public function testMaskedInputWithEmptyPrompt() {
        $command = new class extends \WebFiori\Cli\Command {
            public function __construct() {
                parent::__construct('test-empty-prompt');
            }
            
            public function exec(): int {
                $input = $this->getMaskedInput('');
                $result = $input === null ? 'null' : $input;
                $this->println("Result: $result");
                return 0;
            }
        };
        
        $output = $this->executeSingleCommand($command);
        
        $this->assertContains("Result: null\n", $output);
    }
    
    /**
     * Test masked input with whitespace handling.
     * 
     * @test
     */
    public function testMaskedInputWhitespaceHandling() {
        $command = new class extends \WebFiori\Cli\Command {
            public function __construct() {
                parent::__construct('test-whitespace');
            }
            
            public function exec(): int {
                $input = $this->getMaskedInput('Enter value: ');
                $this->println("Value: '$input'");
                return 0;
            }
        };
        
        // Test with leading/trailing spaces
        $output = $this->executeSingleCommand($command, [], ['  spaced  ']);
        
        $this->assertContains("Value: 'spaced'\n", $output); // Should be trimmed
    }
    
    /**
     * Test masked input with special characters.
     * 
     * @test
     */
    public function testMaskedInputWithSpecialCharacters() {
        $command = new class extends \WebFiori\Cli\Command {
            public function __construct() {
                parent::__construct('test-special-chars');
            }
            
            public function exec(): int {
                $input = $this->getMaskedInput('Enter complex password: ');
                $this->println("Password length: " . strlen($input));
                return 0;
            }
        };
        
        $complexPassword = 'P@ssw0rd!#$%';
        $output = $this->executeSingleCommand($command, [], [$complexPassword]);
        
        $this->assertContains("Password length: " . strlen($complexPassword) . "\n", $output);
    }
}
