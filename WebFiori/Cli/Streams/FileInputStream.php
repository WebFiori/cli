<?php
namespace WebFiori\Cli\Streams;

use WebFiori\Cli\Exceptions\IOException;
use WebFiori\Cli\KeysMap;
use WebFiori\File\Exceptions\FileException;
use WebFiori\File\File;
/**
 * A class that implements input stream which can be based on files.
 *
 * @author Ibrahim
 */
class FileInputStream implements InputStream {
    private $file;
    private $seek;
    /**
     * Creates new instance of the class.
     * 
     * @param string $path The absolute path to the file that CLI engine
     * will read inputs from.
     */
    public function __construct(string $path) {
        $this->file = new File($path);
        $this->seek = 0;
    }

    /**
     * Reads a string of bytes from the file.
     *
     * This method is used to read specific number of characters from the
     * file which is given as input stream.
     *
     * @return string The method will return a string from the file.
     *
     * @throws IOException If the method was not able to read the file.
     *
     */
    public function read(int $bytes = 1) : string {
        try {
            $this->file->read($this->seek, $this->seek + $bytes);
            $this->seek += $bytes;

            $result = $this->file->getRawData();
            
            // Normalize line endings to Unix format for consistent behavior
            // This ensures tests pass regardless of the original file's line ending format
            $result = str_replace(["\r\n", "\r"], "\n", $result);
            
            return $result;
        } catch (FileException $ex) {
            throw new IOException('Unable to read '.$bytes.' byte(s) due to an error: "'.$ex->getMessage().'"', $ex->getCode(), $ex);
        }
    }
    /**
     * Reads one line from the file.
     * 
     * The method will continue to read from the file till it finds end of 
     * line character "\n".
     * 
     * @return string The method will return the string which was taken from 
     * the file without the end of line character.
     * 
     */
    public function readLine() : string {
        $result = KeysMap::readLine($this);
        
        // Normalize line endings to Unix format for consistent behavior
        // This ensures tests pass regardless of the original file's line ending format
        $result = str_replace(["\r\n", "\r"], "\n", $result);
        
        return $result;
    }
}
