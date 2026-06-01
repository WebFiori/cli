<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\InputValidator;
use WebFiori\Cli\ArgumentOption;

/**
 * Demonstrates masked input functionality for secure data entry.
 * 
 * This command shows various use cases for getMaskedInput():
 * - Basic password input with default asterisk masking
 * - Custom mask characters for different types of sensitive data
 * - Input validation for security requirements
 * - Default values for optional sensitive fields
 * - Confirmation prompts for critical operations
 */
class SecureInputCommand extends Command {
    
    public function __construct() {
        parent::__construct('secure-input', [
            '--demo' => [
                ArgumentOption::DESCRIPTION => 'Type of demo to run',
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::VALUES => ['password', 'pin', 'token', 'all'],
                ArgumentOption::DEFAULT => 'all'
            ]
        ], 'Demonstrates secure masked input functionality');
    }

    public function exec(): int {
        $demo = $this->getArgValue('--demo') ?? 'all';
        
        $this->println('🔒 WebFiori CLI - Masked Input Demo');
        $this->println('===================================');
        $this->println();
        
        switch ($demo) {
            case 'password':
                $this->passwordDemo();
                break;
            case 'pin':
                $this->pinDemo();
                break;
            case 'token':
                $this->tokenDemo();
                break;
            case 'all':
            default:
                $this->passwordDemo();
                $this->println();
                $this->pinDemo();
                $this->println();
                $this->tokenDemo();
                $this->println();
                $this->advancedDemo();
                break;
        }
        
        $this->println();
        $this->success('✅ Demo completed successfully!');
        
        return 0;
    }
    
    /**
     * Demonstrates basic password input with validation.
     */
    private function passwordDemo(): void {
        $this->info('📝 Password Demo - Basic masked input with validation');
        $this->println('Enter a password (minimum 8 characters):');
        
        $validator = new InputValidator(function($password) {
            if (strlen($password) < 8) {
                return false;
            }
            if (!preg_match('/[A-Z]/', $password)) {
                return false;
            }
            if (!preg_match('/[0-9]/', $password)) {
                return false;
            }
            return true;
        }, 'Password must be at least 8 characters with uppercase letter and number!');
        
        $password = $this->getMaskedInput('Password: ', '*', null, $validator);
        
        $this->success("✅ Password accepted! Length: " . strlen($password));
        $this->println("   Captured value: $password");
    }
    
    /**
     * Demonstrates PIN input with custom mask character.
     */
    private function pinDemo(): void {
        $this->info('🔢 PIN Demo - Custom mask character');
        $this->println('Enter a 4-digit PIN (will be masked with # characters):');
        
        $validator = new InputValidator(function($pin) {
            return strlen($pin) === 4 && ctype_digit($pin);
        }, 'PIN must be exactly 4 digits!');
        
        $pin = $this->getMaskedInput('PIN: ', '#', null, $validator);
        
        $this->success("✅ PIN accepted!");
        $this->println("   Captured value: $pin");
    }
    
    /**
     * Demonstrates token input with default value.
     */
    private function tokenDemo(): void {
        $this->info('🎫 Token Demo - With default value');
        $this->println('Enter API token (or press Enter for demo token):');
        
        $token = $this->getMaskedInput('API Token: ', '•', 'demo-token-12345');
        
        $this->success("✅ Token set!");
        $this->println("   Captured value: $token");
    }
    
    /**
     * Demonstrates advanced scenarios.
     */
    private function advancedDemo(): void {
        $this->info('🚀 Advanced Demo - Multiple scenarios');
        
        // Database password with confirmation
        $this->println('Setting up database connection:');
        
        $dbPassword = $this->getMaskedInput('Database Password: ');
        $confirmPassword = $this->getMaskedInput('Confirm Password: ');
        
        if ($dbPassword !== $confirmPassword) {
            $this->error('❌ Passwords do not match!');
            $this->println("   First: $dbPassword");
            $this->println("   Second: $confirmPassword");
            return;
        }
        
        $this->success('✅ Database password confirmed');
        $this->println("   Captured value: $dbPassword");
    }
}
