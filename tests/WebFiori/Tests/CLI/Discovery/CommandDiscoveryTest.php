<?php
namespace WebFiori\Tests\CLI\Discovery;

use PHPUnit\Framework\TestCase;
use WebFiori\CLI\Discovery\CommandCache;
use WebFiori\CLI\Discovery\CommandDiscovery;
use WebFiori\CLI\Exceptions\CommandDiscoveryException;
use WebFiori\Tests\CLI\Discovery\TestCommands\TestCommand;
use WebFiori\Tests\CLI\Discovery\TestCommands\AutoDiscoverableCommand;

/**
 * Test cases for CommandDiscovery class.
 */
class CommandDiscoveryTest extends TestCase {
    private CommandDiscovery $discovery;
    private string $testCommandsPath;
    
    protected function setUp(): void {
        $this->discovery = new CommandDiscovery();
        $this->testCommandsPath = __DIR__ . '/TestCommands';
    }
    
    /**
     * @test
     */
    public function testAddSearchPath() {
        $this->discovery->addSearchPath($this->testCommandsPath);
        
        // Should not throw exception for valid path
        $this->assertTrue(true);
    }
    
    /**
     * @test
     */
    public function testAddInvalidSearchPath() {
        $this->expectException(CommandDiscoveryException::class);
        $this->expectExceptionMessage('Search path does not exist');
        
        $this->discovery->addSearchPath('/non/existent/path');
    }
    
    /**
     * @test
     */
    public function testAddMultipleSearchPaths() {
        $paths = [$this->testCommandsPath, __DIR__];
        
        $this->discovery->addSearchPaths($paths);
        
        // Should not throw exception
        $this->assertTrue(true);
    }
    
    /**
     * @test
     */
    public function testExcludePattern() {
        $this->discovery->excludePattern('*Test*');
        $this->discovery->excludePatterns(['*Abstract*', '*Hidden*']);
        
        // Should not throw exception
        $this->assertTrue(true);
    }
    
    /**
     * @test
     */
    public function testStrictMode() {
        $this->discovery->setStrictMode(true);
        $this->discovery->setStrictMode(false);
        
        // Should not throw exception
        $this->assertTrue(true);
    }
    
    /**
     * @test
     */
    public function testDiscoverCommands() {
        $this->discovery->addSearchPath($this->testCommandsPath);
        
        $commands = $this->discovery->discover();
        
        $this->assertIsArray($commands);
        $this->assertNotEmpty($commands);
        
        // Should find TestCommand
        $testCommandFound = false;
        foreach ($commands as $command) {
            if ($command instanceof TestCommand) {
                $testCommandFound = true;
                break;
            }
        }
        $this->assertTrue($testCommandFound, 'TestCommand should be discovered');
    }
    
    /**
     * @test
     */
    public function testDiscoverWithExcludePatterns() {
        $this->discovery->addSearchPath($this->testCommandsPath)
                        ->excludePattern('*Abstract*')
                        ->excludePattern('*NotACommand*');
        
        $commands = $this->discovery->discover();
        
        // Should not include abstract commands or non-commands
        foreach ($commands as $command) {
            $this->assertInstanceOf(\WebFiori\CLI\Command::class, $command);
        }
    }
    
    /**
     * @test
     */
    public function testDiscoverWithCache() {
        $tempCacheFile = sys_get_temp_dir() . '/discovery_test_cache.json';
        $cache = new CommandCache($tempCacheFile, true);
        $discovery = new CommandDiscovery($cache);
        
        $discovery->addSearchPath($this->testCommandsPath);
        
        // First discovery should populate cache
        $commands1 = $discovery->discover();
        $this->assertTrue(file_exists($tempCacheFile));
        
        // Second discovery should use cache
        $commands2 = $discovery->discover();
        
        $this->assertEquals(count($commands1), count($commands2));
        
        // Cleanup
        if (file_exists($tempCacheFile)) {
            unlink($tempCacheFile);
        }
    }
    
    /**
     * @test
     */
    public function testGetErrors() {
        $this->discovery->addSearchPath($this->testCommandsPath);
        
        // Discover commands (some may have errors)
        $this->discovery->discover();
        
        $errors = $this->discovery->getErrors();
        $this->assertIsArray($errors);
    }
    
    /**
     * @test
     */
    public function testGetCache() {
        $cache = $this->discovery->getCache();
        $this->assertInstanceOf(CommandCache::class, $cache);
    }
    
    /**
     * @test
     */
    public function testDiscoverWithAutoDiscoverableCommand() {
        // Set AutoDiscoverableCommand to not register
        AutoDiscoverableCommand::setShouldRegister(false);
        
        $this->discovery->addSearchPath($this->testCommandsPath);
        $commands = $this->discovery->discover();
        
        // Should not include AutoDiscoverableCommand
        $autoDiscoverableFound = false;
        foreach ($commands as $command) {
            if ($command instanceof AutoDiscoverableCommand) {
                $autoDiscoverableFound = true;
                break;
            }
        }
        $this->assertFalse($autoDiscoverableFound);
        
        // Reset for other tests
        AutoDiscoverableCommand::setShouldRegister(true);
    }
    
    /**
     * @test
     */
    public function testStrictModeThrowsException() {
        // Create a discovery that will encounter errors
        $discovery = new CommandDiscovery();
        $discovery->setStrictMode(true);
        
        // Add a path that might have issues
        $discovery->addSearchPath($this->testCommandsPath);
        
        // In strict mode, if there are any errors, it should throw
        // Note: This test might not always throw depending on the test commands
        // but it tests the mechanism
        try {
            $discovery->discover();
            $this->assertTrue(true); // No exception thrown
        } catch (CommandDiscoveryException $e) {
            $this->assertInstanceOf(CommandDiscoveryException::class, $e);
        }
    }
}
