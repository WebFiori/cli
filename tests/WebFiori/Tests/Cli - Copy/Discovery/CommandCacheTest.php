<?php
namespace WebFiori\Tests\Cli\Discovery;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Discovery\CommandCache;

/**
 * Test cases for CommandCache class.
 */
class CommandCacheTest extends TestCase {
    private string $tempCacheFile;
    private CommandCache $cache;
    
    protected function setUp(): void {
        $this->tempCacheFile = sys_get_temp_dir() . '/test_commands_cache.json';
        $this->cache = new CommandCache($this->tempCacheFile, true);
    }
    
    protected function tearDown(): void {
        if (file_exists($this->tempCacheFile)) {
            unlink($this->tempCacheFile);
        }
    }
    
    /**
     * @test
     */
    public function testCacheEnabledByDefault() {
        $cache = new CommandCache();
        $this->assertTrue($cache->isEnabled());
    }
    
    /**
     * @test
     */
    public function testCacheCanBeDisabled() {
        $cache = new CommandCache('test.json', false);
        $this->assertFalse($cache->isEnabled());
    }
    
    /**
     * @test
     */
    public function testGetReturnsNullWhenCacheDisabled() {
        $this->cache->setEnabled(false);
        $result = $this->cache->get();
        $this->assertNull($result);
    }
    
    /**
     * @test
     */
    public function testGetReturnsNullWhenCacheFileDoesNotExist() {
        $result = $this->cache->get();
        $this->assertNull($result);
    }
    
    /**
     * @test
     */
    public function testStoreAndGet() {
        $commands = [
            ['className' => 'TestCommand', 'name' => 'test'],
            ['className' => 'AnotherCommand', 'name' => 'another']
        ];
        $files = [__FILE__];
        
        $this->cache->store($commands, $files);
        
        $this->assertTrue(file_exists($this->tempCacheFile));
        
        $retrieved = $this->cache->get();
        $this->assertEquals($commands, $retrieved);
    }
    
    /**
     * @test
     */
    public function testCacheInvalidatedWhenFileModified() {
        $tempFile = sys_get_temp_dir() . '/test_file.php';
        file_put_contents($tempFile, '<?php // test');
        
        $commands = [['className' => 'TestCommand', 'name' => 'test']];
        $files = [$tempFile];
        
        $this->cache->store($commands, $files);
        
        // Get the cached result first to ensure it works
        $result1 = $this->cache->get();
        $this->assertEquals($commands, $result1);
        
        // Modify the file with a significant time difference
        sleep(1); // Ensure different timestamp
        file_put_contents($tempFile, '<?php // modified test');
        clearstatcache(); // Clear PHP's file stat cache
        
        $result2 = $this->cache->get();
        $this->assertNull($result2, 'Cache should be invalidated after file modification');
        
        unlink($tempFile);
    }
    
    /**
     * @test
     */
    public function testCacheInvalidatedWhenFileDeleted() {
        $tempFile = sys_get_temp_dir() . '/test_file.php';
        file_put_contents($tempFile, '<?php // test');
        
        $commands = [['className' => 'TestCommand', 'name' => 'test']];
        $files = [$tempFile];
        
        $this->cache->store($commands, $files);
        
        // Delete the file
        unlink($tempFile);
        
        $result = $this->cache->get();
        $this->assertNull($result);
    }
    
    /**
     * @test
     */
    public function testClear() {
        $commands = [['className' => 'TestCommand', 'name' => 'test']];
        $files = [__FILE__];
        
        $this->cache->store($commands, $files);
        $this->assertTrue(file_exists($this->tempCacheFile));
        
        $this->cache->clear();
        $this->assertFalse(file_exists($this->tempCacheFile));
    }
    
    /**
     * @test
     */
    public function testSettersAndGetters() {
        $this->cache->setEnabled(false);
        $this->assertFalse($this->cache->isEnabled());
        
        $this->cache->setEnabled(true);
        $this->assertTrue($this->cache->isEnabled());
        
        $newCacheFile = '/tmp/new_cache.json';
        $this->cache->setCacheFile($newCacheFile);
        $this->assertEquals($newCacheFile, $this->cache->getCacheFile());
    }
    
    /**
     * @test
     */
    public function testStoreDoesNothingWhenDisabled() {
        $this->cache->setEnabled(false);
        
        $commands = [['className' => 'TestCommand', 'name' => 'test']];
        $files = [__FILE__];
        
        $this->cache->store($commands, $files);
        
        $this->assertFalse(file_exists($this->tempCacheFile));
    }
}
