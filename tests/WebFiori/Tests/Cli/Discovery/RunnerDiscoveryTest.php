<?php
namespace WebFiori\Tests\Cli\Discovery;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Discovery\CommandCache;
use WebFiori\Cli\Discovery\CommandDiscovery;
use WebFiori\Cli\Runner;
use WebFiori\Tests\Cli\Discovery\TestCommands\TestCommand;

/**
 * Test cases for Runner discovery integration.
 */
class RunnerDiscoveryTest extends TestCase {
    private Runner $runner;
    private string $testCommandsPath;
    
    protected function setUp(): void {
        $this->runner = new Runner();
        $this->testCommandsPath = __DIR__ . '/TestCommands';
    }
    
    /**
     * @test
     */
    public function testEnableAutoDiscovery() {
        $result = $this->runner->enableAutoDiscovery();
        
        $this->assertInstanceOf(Runner::class, $result);
        $this->assertTrue($this->runner->isAutoDiscoveryEnabled());
        $this->assertInstanceOf(CommandDiscovery::class, $this->runner->getCommandDiscovery());
    }
    
    /**
     * @test
     */
    public function testDisableAutoDiscovery() {
        $this->runner->enableAutoDiscovery();
        $result = $this->runner->disableAutoDiscovery();
        
        $this->assertInstanceOf(Runner::class, $result);
        $this->assertFalse($this->runner->isAutoDiscoveryEnabled());
    }
    
    /**
     * @test
     */
    public function testAddDiscoveryPath() {
        $result = $this->runner->addDiscoveryPath($this->testCommandsPath);
        
        $this->assertInstanceOf(Runner::class, $result);
        $this->assertTrue($this->runner->isAutoDiscoveryEnabled());
    }
    
    /**
     * @test
     */
    public function testAddDiscoveryPaths() {
        $paths = [$this->testCommandsPath, __DIR__];
        $result = $this->runner->addDiscoveryPaths($paths);
        
        $this->assertInstanceOf(Runner::class, $result);
        $this->assertTrue($this->runner->isAutoDiscoveryEnabled());
    }
    
    /**
     * @test
     */
    public function testExcludePattern() {
        $result = $this->runner->excludePattern('*Test*');
        
        $this->assertInstanceOf(Runner::class, $result);
        $this->assertTrue($this->runner->isAutoDiscoveryEnabled());
    }
    
    /**
     * @test
     */
    public function testExcludePatterns() {
        $patterns = ['*Test*', '*Abstract*'];
        $result = $this->runner->excludePatterns($patterns);
        
        $this->assertInstanceOf(Runner::class, $result);
        $this->assertTrue($this->runner->isAutoDiscoveryEnabled());
    }
    
    /**
     * @test
     */
    public function testSetDiscoveryStrictMode() {
        $result = $this->runner->setDiscoveryStrictMode(true);
        
        $this->assertInstanceOf(Runner::class, $result);
        $this->assertTrue($this->runner->isAutoDiscoveryEnabled());
    }
    
    /**
     * @test
     */
    public function testSetCommandDiscovery() {
        $discovery = new CommandDiscovery();
        $result = $this->runner->setCommandDiscovery($discovery);
        
        $this->assertInstanceOf(Runner::class, $result);
        $this->assertTrue($this->runner->isAutoDiscoveryEnabled());
        $this->assertSame($discovery, $this->runner->getCommandDiscovery());
    }
    
    /**
     * @test
     */
    public function testDiscoverCommands() {
        $this->runner->addDiscoveryPath($this->testCommandsPath);
        $result = $this->runner->discoverCommands();
        
        $this->assertInstanceOf(Runner::class, $result);
        
        // Check that commands were registered
        $commands = $this->runner->getCommands();
        $this->assertArrayHasKey('test-cmd', $commands);
        $this->assertInstanceOf(TestCommand::class, $commands['test-cmd']);
    }
    
    /**
     * @test
     */
    public function testAutoRegister() {
        $result = $this->runner->autoRegister($this->testCommandsPath, ['*Abstract*']);
        
        $this->assertInstanceOf(Runner::class, $result);
        
        // Check that commands were registered
        $commands = $this->runner->getCommands();
        $this->assertArrayHasKey('test-cmd', $commands);
    }
    
    /**
     * @test
     */
    public function testDiscoverCommandsOnlyOnce() {
        $this->runner->addDiscoveryPath($this->testCommandsPath);
        
        // First discovery
        $this->runner->discoverCommands();
        $commandsCount1 = count($this->runner->getCommands());
        
        // Second discovery should not add duplicates
        $this->runner->discoverCommands();
        $commandsCount2 = count($this->runner->getCommands());
        
        $this->assertEquals($commandsCount1, $commandsCount2);
    }
    
    /**
     * @test
     */
    public function testGetDiscoveryCache() {
        $this->runner->enableAutoDiscovery();
        $cache = $this->runner->getDiscoveryCache();
        
        $this->assertInstanceOf(CommandCache::class, $cache);
    }
    
    /**
     * @test
     */
    public function testEnableDiscoveryCache() {
        $cacheFile = sys_get_temp_dir() . '/runner_test_cache.json';
        $result = $this->runner->enableDiscoveryCache($cacheFile);
        
        $this->assertInstanceOf(Runner::class, $result);
        $this->assertTrue($this->runner->isAutoDiscoveryEnabled());
        
        $cache = $this->runner->getDiscoveryCache();
        $this->assertTrue($cache->isEnabled());
        $this->assertEquals($cacheFile, $cache->getCacheFile());
    }
    
    /**
     * @test
     */
    public function testDisableDiscoveryCache() {
        $this->runner->enableAutoDiscovery();
        $result = $this->runner->disableDiscoveryCache();
        
        $this->assertInstanceOf(Runner::class, $result);
        
        $cache = $this->runner->getDiscoveryCache();
        $this->assertFalse($cache->isEnabled());
    }
    
    /**
     * @test
     */
    public function testClearDiscoveryCache() {
        $cacheFile = sys_get_temp_dir() . '/runner_clear_test_cache.json';
        $this->runner->enableDiscoveryCache($cacheFile)
                    ->addDiscoveryPath($this->testCommandsPath)
                    ->discoverCommands();
        
        // Cache file should exist
        $this->assertTrue(file_exists($cacheFile));
        
        $result = $this->runner->clearDiscoveryCache();
        $this->assertInstanceOf(Runner::class, $result);
        
        // Cache file should be deleted
        $this->assertFalse(file_exists($cacheFile));
    }
    
    /**
     * @test
     */
    public function testDiscoveryWithoutEnabledDoesNothing() {
        // Don't enable auto-discovery
        $result = $this->runner->discoverCommands();
        
        $this->assertInstanceOf(Runner::class, $result);
        
        // Should not have discovered any commands (except default help command)
        $commands = $this->runner->getCommands();
        $expectedCommands = ['help' => $this->runner->getCommandByName('help')];
        $this->assertEquals($expectedCommands, $commands);
    }
}
