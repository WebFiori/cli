<?php
namespace webfiori\cli\streams;

use webfiori\cli\KeysMap;
/**
 * A class that implements default standard input for command line interface.
 *
 * @author Ibrahim
 * 
 * @since 2.3.1
 * 
 * @version 1.0
 */
class StdIn implements InputStream {
    /**
     * Reads a string of bytes from STDIN.
     * 
     * This method is used to read specific number of characters from STDIN. Each
     * character is represented as a single byte.
     * 
     * @return string The method will return the string which was given as input 
     * in STDIN.
     * 
     * @since 1.0
     */
    public function read(int $bytes = 1) : string {
        $input = '';

        while (strlen($input) < $bytes) {
            $char = KeysMap::map(fgetc(STDIN));

            if ($char == 'BACKSPACE' && strlen($input) > 0) {
                $input = substr($input, 0, strlen($input) - 1);
            } else if ($char == 'ESC') {
                return '';
            } else if ($char == 'DOWN') {
                // read history?
            } else if ($char == 'UP') {
                // read history?
            } else {
                if ($char == 'SPACE') {
                    $input .= ' ';
                } else {
                    $input .= $char;
                }
            }
        }

        return $input;
    }
    /**
     * Reads one line from STDIN.
     * 
     * The method will continue to read from STDIN till it finds end of 
     * line character "\n".
     * 
     * @return string The method will return the string which was taken from 
     * STDIN without the end of line character.
     * 
     * @since 1.0
     */
    public function readLine() : string {
        return KeysMap::readLine($this);
    }
}
