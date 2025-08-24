<?php
namespace WebFiori\Cli;

use WebFiori\Cli\Streams\InputStream;
/**
 * A utility class which can be used to map control characters to string values.
 *
 * @author Ibrahim
 */
class KeysMap {
    /**
     * An array that holds a map of special keyboard keys codes and the 
     * meaning of each code.
     * 
     * @var array
     * 
     */
    const KEY_MAP = [
        "\033[A" => 'UP',
        "\033[B" => 'DOWN',
        "\033[C" => 'RIGHT',
        "\033[D" => 'LEFT',
        "\n" => 'LF',
        "\r" => 'CR',
        " " => 'SPACE',
        "\010" => 'BACKSPACE',
        "\177" => 'BACKSPACE',
        "\t" => 'TAP',
        "\e" => 'ESC'
    ];
    /**
     * Maps a control character to a string that represents its value.
     * 
     * @param string $ch The control character such as '\n'.
     * 
     * @return string If the given character maps to a control character, its
     * value is returned as string. For example, if the character is '\n',
     * the method will return the value "LF" which stands for "line feed". If the
     * character does not map to any control character, the same character is
     * returned. Possible return values are:
     * <ul>
     * <li>TAP for control character \t</li>
     * <li>ESC for control character \e</li>
     * <li>BACKSPACE for control character \010 and \177</li>
     * <li>SPACE for control character 'space'</li>
     * <li>CR for control character \r</li>
     * <li>LF for control character \n</li>
     * <li>UP for control character \033[A</li>
     * <li>DOWN for control character \033[B</li>
     * <li>RIGHT for control character \033[C</li>
     * <li>LEFT for control character \033[D</li>
     * 
     * </ul>
     */
    public static function map(string $ch) : string {
        $keyMap = self::KEY_MAP;

        if (isset($keyMap[$ch])) {
            return $keyMap[$ch];
        }

        return $ch;
    }
    /**
     * Reads a string of bytes from specific input stream.
     * 
     * This method is used to read specific number of bytes from any
     * input stream.
     * 
     * @return string The method will return the string which was given as input 
     * in the stream.
     * 
     */
    public static function read(InputStream $stream, $bytes = 1) : string {
        $input = '';
        $len = strlen($input);

        while ($len < $bytes) {
            $char = self::readAndTranslate($stream);
            self::appendChar($char, $input);
            $len = strlen($input);
        }

        return $input;
    }

    /**
     * Reads one character from specific input stream and check if the character
     * maps to any control character.
     * 
     * @param InputStream $stream
     * 
     * @return string If the character maps to control character, a value from
     * the array InputTranslator::KEY_MAP is returned. Other than that,
     * the character itself will be returned.
     */
    public static function readAndTranslate(InputStream $stream) : string {
        $keypress = $stream->read();

        return self::map($keypress);
    }
    /**
     * Reads one line from specific input stream.
     * 
     * The method will continue to read from the stream till it finds end of 
     * line character "\n".
     * 
     * @param InputStream $stream
     * 
     * @return string The method will return the string which was taken from 
     * the stream. Note that end of line character will be included in the
     * final input.
     * 
     */
    public static function readLine(InputStream $stream) : string {
        $input = '';
        $char = '';

        while ($char != 'LF') {
            $char = self::readAndTranslate($stream);
            self::appendChar($char, $input);
        }

        return $input;
    }
    private static function appendChar($ch, &$input) {
        if ($ch == 'BACKSPACE' && strlen($input) > 0) {
            $input = substr($input, 0, strlen($input) - 1);
        } else if ($ch == 'ESC') {
            $input .= "\e";
        } else if ($ch == "CR") {
            // Do nothing?
            $input .= "\r";
        } else if ($ch == "LF") {
            // Do nothing?
            $input .= "\n";
        } else if ($ch == 'DOWN') {
            // read history;
            //$input .= ' ';
        } else if ($ch == 'UP') {
            // read history;
            //$input .= ' ';
        } else if ($ch != 'CR' && $ch != 'LF') {
            if ($ch == 'SPACE') {
                $input .= ' ';
            } else {
                $input .= $ch;
            }
        }
    }
}
