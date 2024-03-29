<?php
namespace webfiori\cli\streams;

/**
 * A stream that uses array as its source of output.
 * 
 * This stream is mainly used when the developer would like to test his commands
 * using the class 'CommandRunner'.
 *
 * @author Ibrahim
 */
class ArrayOutputStream implements OutputStream {
    private $isLastPrintLn;
    private $isPrintln;
    private $outputArr;
    public function __construct() {
        $this->outputArr = [];
        $this->isPrintln = false;
        $this->isLastPrintLn = false;
    }
    /**
     * Returns the array that holds all output values.
     * 
     * @return array The array will have the output with selected formatting
     * options.
     */
    public function getOutputArray() : array {
        return $this->outputArr;
    }
    /**
     * Sends a line as output to the array.
     * 
     * @param string $str The string that represents the output.
     * 
     * @param array $_ Any extra formatting options.
     */
    public function println(string $str, ...$_) {
        $this->isPrintln = true;
        $toPass = [$str."\n"];

        foreach ($_ as $val) {
            $toPass[] = $val;
        }
        call_user_func_array([$this, 'prints'], $toPass);
        $this->isPrintln = false;
    }
    /**
     * Sends a string to the stream.
     * 
     * This method is similar to php's 'prints' function.
     * 
     * @param string $str The string that will be printed.
     * 
     * @param array $_ Any extra parameters that the string needs.
     */
    public function prints(string $str, ...$_) {
        $arrayToPass = [$str];

        foreach ($_ as $val) {
            $type = gettype($val);

            if ($type != 'array') {
                $arrayToPass[] = $val;
            }
        }
        $index = count($this->outputArr);

        if ($index >= 1) {
            if ($this->isLastPrintLn) {
                $this->outputArr[] = call_user_func_array('sprintf', $arrayToPass);
            } else {
                $this->outputArr[$index - 1] .= call_user_func_array('sprintf', $arrayToPass);
            }
            $this->isLastPrintLn = false;
        } else {
            $this->outputArr[] = call_user_func_array('sprintf', $arrayToPass);
        }

        $this->isLastPrintLn = $this->isPrintln;
    }
    /**
     * Removes all stored output.
     */
    public function reset() {
        $this->outputArr = [];
    }
}
