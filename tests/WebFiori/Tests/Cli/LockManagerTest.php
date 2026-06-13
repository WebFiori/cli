<?php
declare(strict_types=1);

namespace WebFiori\Tests\Cli;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\LockManager;

class LockManagerTest extends TestCase {
    /**
     * @test
     */
    public function testInitialState() {
        $lm = new LockManager();
        $this->assertFalse($lm->isLocked());
        $this->assertNull($lm->getLockPath());
    }

    /**
     * @test
     */
    public function testAcquireAndRelease() {
        $lm = new LockManager();
        $this->assertTrue($lm->acquire('test-cmd'));
        $this->assertTrue($lm->isLocked());
        $this->assertNotNull($lm->getLockPath());
        $this->assertStringContainsString('wfcli-test-cmd.lock', $lm->getLockPath());

        $lm->release();
        $this->assertFalse($lm->isLocked());
    }

    /**
     * @test
     */
    public function testCustomLockPath() {
        $path = sys_get_temp_dir() . '/custom-test-lock.lock';
        $lm = new LockManager();
        $this->assertTrue($lm->acquire('ignored', $path));
        $this->assertEquals($path, $lm->getLockPath());

        $lm->release();
    }

    /**
     * @test
     */
    public function testConcurrentAcquireFails() {
        $lm1 = new LockManager();
        $lm2 = new LockManager();

        $this->assertTrue($lm1->acquire('concurrent-test'));
        $this->assertFalse($lm2->acquire('concurrent-test'));

        $lm1->release();

        // Now second can acquire
        $this->assertTrue($lm2->acquire('concurrent-test'));
        $lm2->release();
    }

    /**
     * @test
     */
    public function testReleaseWithoutAcquire() {
        $lm = new LockManager();
        // Should not throw
        $lm->release();
        $this->assertFalse($lm->isLocked());
    }

    /**
     * @test
     */
    public function testLockFileContainsPid() {
        $lm = new LockManager();
        $lm->acquire('pid-test');
        $path = $lm->getLockPath();
        $content = file_get_contents($path);
        $this->assertEquals((string) getmypid(), $content);

        $lm->release();
    }

    /**
     * @test
     */
    public function testAcquireFailsOnInvalidPath() {
        $lm = new LockManager();
        $result = $lm->acquire('test', '/nonexistent/dir/lock.lock');
        $this->assertFalse($result);
        $this->assertFalse($lm->isLocked());
    }
}
