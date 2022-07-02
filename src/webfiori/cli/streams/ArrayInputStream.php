<?php
namespace webfiori\cli\streams;

use webfiori\cli\streams\InputStream;
use InvalidArgumentException;
/**
 * A stream that uses array as its source of input.
 * 
 * This stream is mainly used when the developer would like to test his commands
 * using the class 'CommandRunner'.
 *
 * @author Ibrahim
 */
class ArrayInputStream implements InputStream {
    private $inputsArr;
    private $currentLine = 0;
    private $currentLineByte = 0;
    /**
     * Creates new instance of the class.
     * 
     * @param array $inputs An array that contains lines of inputs.
     * each index in the array will represent one line. Default is empty array.
     */
    public function __construct(array $inputs = []) {
        $this->inputsArr = $inputs;
    }
    /**
     * 
     * A method that does nothing.
     * 
     * @param type $bytes
     * 
     * @return string The method will always return empty string.
     */
    public function read(int $bytes = 1) : string {
        if ($this->currentLine >= count($this->inputsArr)) {
            throw new InvalidArgumentException('Reached end of stream while trying to read line number '.($this->currentLine+1));
        }
        if ($bytes < 0) {
            throw new InvalidArgumentException('Bytes must be positive number.');
        }
        $line = $this->inputsArr[$this->currentLine];
        $retVal = '';
        $readBytes = 0;
        
        while ($readBytes < $bytes) {
            
        }
    }
    /**
     * Returns a single line from input array.
     * 
     * A single line is one index in the input array.
     * 
     * @return string A string that represents a single line.
     */
    public function readLine() : string {
        if ($this->currentLine >= count($this->inputsArr)) {
            throw new InvalidArgumentException('Reached end of stream while trying to read line number '.($this->currentLine+1));
        }
        
        $retVal = $this->inputsArr[$this->currentLine];
        $this->currentLine++;
        return $retVal;
    }

}
