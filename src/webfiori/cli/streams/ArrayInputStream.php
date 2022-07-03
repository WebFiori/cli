<?php
namespace webfiori\cli\streams;

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
    private $currentLine = 0;
    private $currentLineByte = 0;
    private $inputsArr;
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
     * Reads specific number of bytes.
     * 
     * @param int $bytes Number of bytes that the method will read from the
     * stream. Must be a positive number.
     * 
     * @return string The will return a string which contains the bytes that
     * the method fetched from the stream.
     */
    public function read(int $bytes = 1) : string {
        if ($this->currentLine >= count($this->inputsArr)) {
            throw new InvalidArgumentException('Reached end of stream while trying to read line number '.($this->currentLine + 1));
        }

        if ($bytes < 0) {
            throw new InvalidArgumentException('Bytes must be positive number.');
        }
        $line = $this->inputsArr[$this->currentLine];
        $retVal = '';
        $readBytes = 0;
        $lineLength = strlen($line);

        while ($readBytes < $bytes) {
            if ($this->currentLineByte == $lineLength) {
                $this->currentLineByte = 0;
                $this->currentLine++;

                if ($this->currentLine >= count($this->inputsArr)) {
                    throw new InvalidArgumentException('Reached end of stream while trying to read '.$bytes.' byte(s).');
                }
                $line = $this->inputsArr[$this->currentLine];
                $lineLength = strlen($line);
            }
            $retVal .= $line[$this->currentLineByte];
            $readBytes++;
            $this->currentLineByte++;
        }

        return $retVal;
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
            throw new InvalidArgumentException('Reached end of stream while trying to read line number '.($this->currentLine + 1));
        }

        $this->checkLineValidity();
        $retVal = substr($this->inputsArr[$this->currentLine], $this->currentLineByte);
        $this->currentLine++;
        $this->currentLineByte = 0;

        return $retVal;
    }
    private function checkLineValidity() {
        $currentLine = $this->inputsArr[$this->currentLine];
        $currentLineLen = strlen($currentLine);

        if ($this->currentLineByte == $currentLineLen && $currentLineLen != 0) {
            $this->currentLine++;
        }

        if ($this->currentLine >= count($this->inputsArr)) {
            throw new InvalidArgumentException('Reached end of stream while trying to read line number '.($this->currentLine + 1));
        }
    }
}
