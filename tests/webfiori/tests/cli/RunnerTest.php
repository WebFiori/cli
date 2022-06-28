<?php
namespace webfiori\tests\cli;

use webfiori\cli\streams\ArrayInputStream;
use webfiori\cli\streams\ArrayOutputStream;
use webfiori\cli\streams\StdIn;
use webfiori\cli\streams\StdOut;
use webfiori\cli\Runner;
use PHPUnit\Framework\TestCase;
/**
 * Description of RunnerTest
 *
 * @author Ibrahim
 */
class RunnerTest extends TestCase {
    /**
     * @test
     */
    public function testSetStreams00() {
        $runner = new Runner();
        $runner->reset();
        $this->assertTrue($runner->getOutputStream() instanceof StdOut);
        $this->assertTrue($runner->getInputStream() instanceof StdIn);
        $runner->setInputStream(new ArrayInputStream());
        $runner->setOutputStream(new ArrayOutputStream());
        $this->assertFalse($runner->getOutputStream() instanceof StdOut);
        $this->assertFalse($runner->getInputStream() instanceof StdIn);
        $this->assertTrue($runner->getInputStream() instanceof ArrayInputStream);
        $this->assertTrue($runner->getOutputStream() instanceof ArrayOutputStream);
    }
}
