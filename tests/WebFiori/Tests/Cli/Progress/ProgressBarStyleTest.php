<?php
namespace WebFiori\Tests\Cli\Progress;

use PHPUnit\Framework\TestCase;
use WebFiori\Cli\Progress\ProgressBarStyle;

/**
 * Test cases for ProgressBarStyle class.
 */
class ProgressBarStyleTest extends TestCase {
    
    /**
     * @test
     */
    public function testDefaultConstructor() {
        $style = new ProgressBarStyle();
        
        $this->assertEquals('█', $style->getBarChar());
        $this->assertEquals('░', $style->getEmptyChar());
        $this->assertEquals('█', $style->getProgressChar());
    }
    
    /**
     * @test
     */
    public function testCustomConstructor() {
        $style = new ProgressBarStyle('=', '-', '>');
        
        $this->assertEquals('=', $style->getBarChar());
        $this->assertEquals('-', $style->getEmptyChar());
        $this->assertEquals('>', $style->getProgressChar());
    }
    
    /**
     * @test
     */
    public function testFromNameDefault() {
        $style = ProgressBarStyle::fromName(ProgressBarStyle::DEFAULT);
        
        $this->assertEquals('█', $style->getBarChar());
        $this->assertEquals('░', $style->getEmptyChar());
        $this->assertEquals('█', $style->getProgressChar());
    }
    
    /**
     * @test
     */
    public function testFromNameAscii() {
        $style = ProgressBarStyle::fromName(ProgressBarStyle::ASCII);
        
        $this->assertEquals('=', $style->getBarChar());
        $this->assertEquals('-', $style->getEmptyChar());
        $this->assertEquals('>', $style->getProgressChar());
    }
    
    /**
     * @test
     */
    public function testFromNameDots() {
        $style = ProgressBarStyle::fromName(ProgressBarStyle::DOTS);
        
        $this->assertEquals('●', $style->getBarChar());
        $this->assertEquals('○', $style->getEmptyChar());
        $this->assertEquals('●', $style->getProgressChar());
    }
    
    /**
     * @test
     */
    public function testFromNameArrow() {
        $style = ProgressBarStyle::fromName(ProgressBarStyle::ARROW);
        
        $this->assertEquals('▶', $style->getBarChar());
        $this->assertEquals('▷', $style->getEmptyChar());
        $this->assertEquals('▶', $style->getProgressChar());
    }
    
    /**
     * @test
     */
    public function testFromNameInvalid() {
        $style = ProgressBarStyle::fromName('invalid-style');
        
        // Should fallback to default
        $this->assertEquals('█', $style->getBarChar());
        $this->assertEquals('░', $style->getEmptyChar());
        $this->assertEquals('█', $style->getProgressChar());
    }
    
    /**
     * @test
     */
    public function testSetBarChar() {
        $style = new ProgressBarStyle();
        $result = $style->setBarChar('#');
        
        $this->assertSame($style, $result); // Test fluent interface
        $this->assertEquals('#', $style->getBarChar());
    }
    
    /**
     * @test
     */
    public function testSetEmptyChar() {
        $style = new ProgressBarStyle();
        $result = $style->setEmptyChar('.');
        
        $this->assertSame($style, $result); // Test fluent interface
        $this->assertEquals('.', $style->getEmptyChar());
    }
    
    /**
     * @test
     */
    public function testSetProgressChar() {
        $style = new ProgressBarStyle();
        $result = $style->setProgressChar('*');
        
        $this->assertSame($style, $result); // Test fluent interface
        $this->assertEquals('*', $style->getProgressChar());
    }
    
    /**
     * @test
     */
    public function testFluentInterface() {
        $style = new ProgressBarStyle();
        $result = $style->setBarChar('#')
                        ->setEmptyChar('.')
                        ->setProgressChar('*');
        
        $this->assertSame($style, $result);
        $this->assertEquals('#', $style->getBarChar());
        $this->assertEquals('.', $style->getEmptyChar());
        $this->assertEquals('*', $style->getProgressChar());
    }
}
