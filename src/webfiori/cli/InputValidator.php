<?php
namespace webfiori\cli;

/**
 * A class which is used to validate input using a callback.
 *
 * @author Ibrahim
 */
class InputValidator {
    private $params;
    private $callback;
    private $errPrompt;
    
    /**
     * Creates new instance of the class.
     * 
     * @param callable $func The function that will be used to validate input.
     * It must return true for valid input and false for invalid.
     * 
     * @param string $errMessage The message that will appear in case the user
     * provided invalid value.
     * 
     * @param array $callbackParams Any extra parameters to be passed to the callback.
     */
    public function __construct(callable $func, string $errMessage = '', array $callbackParams = []) {
        $this->callback = $func;
        $this->params = $callbackParams;
        $trimmed = trim($errMessage);
        if (strlen($trimmed) != 0) {
            $this->errPrompt = $trimmed;
        } else {
            $this->errPrompt = 'Invalid input is given. Try again.';
        }
    }
    /**
     * Execute the validation function.
     * 
     * @param string $input The input that will be validated.
     * 
     * @return bool The return value of this method will depends on the implementation
     * of the validation callback. If it returns true, the method will
     * return true. If it returns false, the method will return false.
     */
    public function isValid(string $input) : bool {
        return call_user_func_array($this->callback, array_merge([$input], $this->params));
    }
    /**
     * Returns the string that should be shown to the user if the validation 
     * fails.
     * 
     * @return string
     */
    public function getErrPrompt() : string {
        return $this->errPrompt;
    }
    /**
     * Checks if given string represents an integer value or not.
     * 
     * This method will basically compare all the characters of the string
     * if they are in the range '0' to '9' inclusive.
     * 
     * @param string $val The string that will be validated.
     * 
     * @return bool If the given string represents an integer, true is returned.
     * False otherwise.
     */
    public static function isInt(string $val) : bool {
        $len = strlen($val);
        if ($len == 0) {
            return false;
        }
        $isNum = true;
        for ($x = 0 ; $x < $len ; $x++) {
            $char = $val[$x];
            $isNum = $char >= '0' && $char <= '9';
            if (!$isNum) {
                break;
            }
        }
        return $isNum;
    }
    /**
     * Checks if given string represents floating number or not.
     * 
     * @param string $val The string that will be validated.
     * 
     * @return bool If the given string represents a floating number, true is returned.
     * False otherwise.
     */
    public static function isFloat(string $val) {
        $len = strlen($val);
        if ($len == 0) {
            return false;
        }
        
        $split = explode('.', $val);
        if (count($split) > 2) {
            return false;
        }
        $isFloat = true;
        foreach ($split as $sub) {
            $isFloat = $isFloat && self::isInt($sub);
        }
        return $isFloat;
    }
}
