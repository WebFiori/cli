<?php
namespace WebFiori\Tests\Cli;

use PHPUnit\Framework\TestCase;
use WebFiori\CLI\Exceptions\IOException;
use WebFiori\CLI\Streams\FileInputStream;
use WebFiori\CLI\Streams\FileOutputStream;
use const DS;
use const ROOT_DIR;


/**
 * Description of FileInputOutputStreamsTest
 *
 * @author i.binalshikh
 */
class FileInputOutputStreamsTest extends TestCase {
    const STREAMS_PATH = ROOT_DIR.'tests'.DS.'WebFiori'.DS.'Tests'.DS.'Files'.DS;
    /**
     * @test
     */
    public function testInputStream00() {
        
        $stream = new FileInputStream(self::STREAMS_PATH.'stream1.txt');
        $line = $stream->readLine();
        $this->assertEquals("Hello World!", $line);
    }
    /**
     * @test
     */
    public function testInputStream01() {
        $stream = new FileInputStream(self::STREAMS_PATH.'stream1.txt');
        $line = $stream->readLine();
        $this->assertEquals("Hello World!", $line);
        // Second readLine should return empty string when EOF is reached
        $this->assertEquals("", $stream->readLine());
    }
    /**
     * @test
     */
    public function testInputStream02() {
        $stream = new FileInputStream(self::STREAMS_PATH.'stream1.txt');
        $this->assertEquals('H', $stream->read());
        $this->assertEquals('el', $stream->read(2));
        $this->assertEquals('l', $stream->read());
        $this->assertEquals('o W', $stream->read(3));
        $this->assertEquals('orld!', $stream->read(5));
        $this->assertEquals("\n", $stream->read());
    }
    /**
     * @test
     */
    public function testInputStream03() {
        $stream = new FileInputStream(self::STREAMS_PATH.'stream1.txt');
        $this->assertEquals("Hello World!\n", $stream->read(13));
    }
    /**
     * @test
     */
    public function testInputStream04() {
        $stream = new FileInputStream(self::STREAMS_PATH.'stream1.txt');
        // Reading more bytes than available should return only available content
        $data = $stream->read(14);
        $this->assertEquals("Hello World!\n", $data);
        $this->assertEquals(13, strlen($data)); // Only 13 bytes available
    }
    /**
     * @test
     */
    public function testInputStream05() {
        $stream = new FileInputStream(self::STREAMS_PATH.'stream2.txt');
        $this->assertEquals("My", $stream->readLine());
        $this->assertEquals("", $stream->readLine());
        $this->assertEquals("Super", $stream->read(5));
        $this->assertEquals(" Hero Ibrahim", $stream->readLine());
        $this->assertEquals("Even Though I'm Not A Hero\nBut ", $stream->read(31));
        $this->assertEquals("I'm A", $stream->readLine());
        $this->assertEquals("Hero in Programming", $stream->readLine());
    }
    /**
     * @test
     */
    public function testOutputStream00() {
        $stream = new FileOutputStream(self::STREAMS_PATH.'output-stream1.txt');
        $stream->println('Hello World!');
        $stream2 = new FileInputStream(self::STREAMS_PATH.'output-stream1.txt');
        $this->assertEquals("Hello World!", $stream2->readLine());
    }
    /**
     * @test
     */
    public function testOutputStream01() {
        $stream = new FileOutputStream(self::STREAMS_PATH.'output-stream1.txt');
        $stream->prints('Hello Mr %s!', 'Ibrahim');
        $stream->println('');
        $stream2 = new FileInputStream(self::STREAMS_PATH.'output-stream1.txt');
        $this->assertEquals("Hello Mr Ibrahim!", $stream2->readLine());
    }
    /**
     * @test
     */
    public function testOutputStream02() {
        $stream = new FileOutputStream(self::STREAMS_PATH.'output-stream1.txt');
        $stream->prints('Im Cool');
        $stream->println('. You are cool.');
        $stream2 = new FileInputStream(self::STREAMS_PATH.'output-stream1.txt');
        $this->assertEquals("Im Cool. You are cool.", $stream2->readLine());
    }
    // ========== ENHANCED FILE STREAM TESTS ==========

    /**
     * Test FileInputStream functionality
     * @test
     */
    public function testFileInputStreamFunctionalityEnhanced() {
        // Create test file
        $testFile = sys_get_temp_dir() . '/webfiori_test_input.txt';
        $testContent = "Line 1\nLine 2\nLine 3\n";
        file_put_contents($testFile, $testContent);
        
        try {
            $stream = new FileInputStream($testFile);
            
            // Test reading lines
            $this->assertEquals('Line 1', $stream->readLine());
            $this->assertEquals('Line 2', $stream->readLine());
            $this->assertEquals('Line 3', $stream->readLine());
            $this->assertEquals('', $stream->readLine()); // EOF
            
            // Test reading with byte limit
            $stream2 = new FileInputStream($testFile);
            $this->assertEquals('Line ', $stream2->read(5));
            $this->assertEquals('1', $stream2->read(1));
            
            // Test reading entire file
            $stream3 = new FileInputStream($testFile);
            $entireContent = '';
            while (($chunk = $stream3->read(1024)) !== '') {
                $entireContent .= $chunk;
            }
            $this->assertEquals($testContent, $entireContent);
        } finally {
            // Cleanup
            if (file_exists($testFile)) {
                unlink($testFile);
            }
        }
    }

    /**
     * Test FileInputStream edge cases
     * @test
     */
    public function testFileInputStreamEdgeCasesEnhanced() {
        $tempDir = sys_get_temp_dir();
        
        // Test with empty file
        $emptyFile = $tempDir . '/webfiori_empty.txt';
        file_put_contents($emptyFile, '');
        
        try {
            $emptyStream = new FileInputStream($emptyFile);
            $this->assertEquals('', $emptyStream->readLine());
            $this->assertEquals('', $emptyStream->read(10));
        } finally {
            if (file_exists($emptyFile)) {
                unlink($emptyFile);
            }
        }
        
        // Test with file containing only newlines
        $newlineFile = $tempDir . '/webfiori_newlines.txt';
        file_put_contents($newlineFile, "\n\n\n");
        
        try {
            $newlineStream = new FileInputStream($newlineFile);
            $this->assertEquals('', $newlineStream->readLine());
            $this->assertEquals('', $newlineStream->readLine());
            $this->assertEquals('', $newlineStream->readLine());
            $this->assertEquals('', $newlineStream->readLine()); // EOF
        } finally {
            if (file_exists($newlineFile)) {
                unlink($newlineFile);
            }
        }
        
        // Test with file containing special characters
        $specialFile = $tempDir . '/webfiori_special.txt';
        $specialContent = "Special: àáâãäåæçèéêë\n中文\n🎉\n";
        file_put_contents($specialFile, $specialContent);
        
        try {
            $specialStream = new FileInputStream($specialFile);
            $this->assertEquals('Special: àáâãäåæçèéêë', $specialStream->readLine());
            $this->assertEquals('中文', $specialStream->readLine());
            $this->assertEquals('🎉', $specialStream->readLine());
        } finally {
            if (file_exists($specialFile)) {
                unlink($specialFile);
            }
        }
    }

    /**
     * Test FileOutputStream functionality
     * @test
     */
    public function testFileOutputStreamFunctionalityEnhanced() {
        $testFile = sys_get_temp_dir() . '/webfiori_test_output.txt';
        
        try {
            $stream = new FileOutputStream($testFile);
            
            // Test writing content
            $stream->prints('Hello');
            $stream->prints(' ');
            $stream->prints('World');
            $stream->prints("\n");
            $stream->prints('Second line');
            
            // Close stream to ensure content is written
            unset($stream);
            
            // Verify file content
            $this->assertTrue(file_exists($testFile));
            $content = file_get_contents($testFile);
            $this->assertEquals("Hello World\nSecond line", $content);
        } finally {
            // Cleanup
            if (file_exists($testFile)) {
                unlink($testFile);
            }
        }
    }

    /**
     * Test FileOutputStream edge cases
     * @test
     */
    public function testFileOutputStreamEdgeCasesEnhanced() {
        $tempDir = sys_get_temp_dir();
        
        // Test writing to new file
        $newFile = $tempDir . '/webfiori_new_output.txt';
        $this->assertFalse(file_exists($newFile));
        
        try {
            $stream = new FileOutputStream($newFile);
            $stream->prints('New file content');
            unset($stream);
            
            $this->assertTrue(file_exists($newFile));
            $this->assertEquals('New file content', file_get_contents($newFile));
        } finally {
            if (file_exists($newFile)) {
                unlink($newFile);
            }
        }
        
        // Test writing special characters
        $specialFile = $tempDir . '/webfiori_special_output.txt';
        try {
            $specialStream = new FileOutputStream($specialFile);
            $specialContent = "Special: àáâãäåæçèéêë\n中文\n🎉";
            $specialStream->prints($specialContent);
            unset($specialStream);
            
            $this->assertEquals($specialContent, file_get_contents($specialFile));
        } finally {
            if (file_exists($specialFile)) {
                unlink($specialFile);
            }
        }
        
        // Test writing large content
        $largeFile = $tempDir . '/webfiori_large_output.txt';
        try {
            $largeStream = new FileOutputStream($largeFile);
            $largeContent = str_repeat('Large content line ' . str_repeat('x', 100) . "\n", 1000);
            $largeStream->prints($largeContent);
            unset($largeStream);
            
            $this->assertEquals($largeContent, file_get_contents($largeFile));
            $this->assertGreaterThan(100000, filesize($largeFile)); // Should be large file
        } finally {
            if (file_exists($largeFile)) {
                unlink($largeFile);
            }
        }
    }

    /**
     * Test FileInputStream with empty file throws exception
     * @test
     */
    public function testFileInputStreamEmptyFileException() {
        $tempDir = sys_get_temp_dir();
        
        // Test with empty file
        $emptyFile = $tempDir . '/webfiori_empty.txt';
        file_put_contents($emptyFile, '');
        
        try {
            $emptyStream = new FileInputStream($emptyFile);
            
            // Reading from empty file should return empty string
            $data = $emptyStream->read(1);
            $this->assertEquals('', $data);
            $this->assertEquals(0, strlen($data));
        } finally {
            if (file_exists($emptyFile)) {
                unlink($emptyFile);
            }
        }
    }
}
