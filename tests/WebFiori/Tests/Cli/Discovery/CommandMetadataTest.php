<?php
namespace WebFiori\Tests\Cli\Discovery;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\ArgumentOption;
use WebFiori\Cli\Exceptions\CommandDiscoveryException;
use WebFiori\Cli\Discovery\CommandMetadata;
use WebFiori\Tests\Cli\Discovery\TestCommands\TestCommand;
use WebFiori\Tests\Cli\Discovery\TestCommands\HiddenCommand;
use WebFiori\Tests\Cli\Discovery\TestCommands\AbstractTestCommand;
use WebFiori\Tests\Cli\Discovery\TestCommands\NotACommand;

/**
 * Test cases for CommandMetadata class.
 */
class CommandMetadataTest extends TestCase {
    
    /**
     * @test
     */
    public function testExtractValidCommand() {
        $metadata = CommandMetadata::extract(TestCommand::class);
        
        $this->assertEquals(TestCommand::class, $metadata['className']);
        $this->assertEquals('test-cmd', $metadata['name']);
        $this->assertEquals('A test command', $metadata[ArgumentOption::DESCRIPTION]);
        $this->assertEquals('test', $metadata['group']);
        $this->assertFalse($metadata['hidden']);
        $this->assertIsString($metadata['file']);
    }
    
    /**
     * @test
     */
    public function testExtractHiddenCommand() {
        $metadata = CommandMetadata::extract(HiddenCommand::class);
        
        $this->assertEquals(HiddenCommand::class, $metadata['className']);
        $this->assertEquals('hidden', $metadata['name']);
        $this->assertTrue($metadata['hidden']);
    }
    
    /**
     * @test
     */
    public function testExtractNonExistentClass() {
        $this->expectException(CommandDiscoveryException::class);
        $this->expectExceptionMessage('Class NonExistentClass does not exist');
        
        CommandMetadata::extract('NonExistentClass');
    }
    
    /**
     * @test
     */
    public function testExtractNonCommandClass() {
        $this->expectException(CommandDiscoveryException::class);
        $this->expectExceptionMessage('is not a Command');
        
        CommandMetadata::extract(NotACommand::class);
    }
    
    /**
     * @test
     */
    public function testExtractAbstractCommand() {
        $this->expectException(CommandDiscoveryException::class);
        $this->expectExceptionMessage('is abstract');
        
        CommandMetadata::extract(AbstractTestCommand::class);
    }
    
    /**
     * @test
     */
    public function testExtractCommandNameFromClassName() {
        // Create a temporary command class without annotations
        $tempClass = new class extends \WebFiori\Cli\Command {
            public function __construct() {
                parent::__construct('temp', [], 'Temp command');
            }
            public function exec(): int { return 0; }
        };
        
        $className = get_class($tempClass);
        $metadata = CommandMetadata::extract($className);
        
        // Should convert class name to kebab-case
        $this->assertIsString($metadata['name']);
        $this->assertNotEmpty($metadata['name']);
    }
    
    /**
     * @test
     */
    public function testExtractDescriptionFromDocblock() {
        $metadata = CommandMetadata::extract(TestCommand::class);
        
        // Should extract description from @Command annotation
        $this->assertEquals('A test command', $metadata[ArgumentOption::DESCRIPTION]);
    }
    
    /**
     * @test
     */
    public function testExtractGroupFromNamespace() {
        $metadata = CommandMetadata::extract(TestCommand::class);
        
        // Should extract group from @Command annotation
        $this->assertEquals('test', $metadata['group']);
    }
}
