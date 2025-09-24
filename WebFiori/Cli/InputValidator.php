<?php
namespace WebFiori\Cli;

use ReflectionClass;
use Throwable;

/**
 * A class which is used to validate input using a callback.
 *
 * @author Ibrahim
 */
class InputValidator {
    private $callback;
    private $errPrompt;
    private $params;

    /**
     * Creates new instance of the class.
     * 
     * @param callable $func The function that will be used to validate input.
     * It must return true for valid input and false for invalid. The first parameter
     * of the function will be the input.
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
     * Returns the string that should be shown to the user if the validation 
     * fails.
     * 
     * @return string The string that should be shown to the user if the validation 
     * fails. Default value is 'Invalid input is given. Try again.'.
     */
    public function getErrPrompt() : string {
        return $this->errPrompt;
    }
    /**
     * Checks if a string represents a valid class or not.
     * 
     * Note that the method will attempt to create an instance of the given
     * class to check validity.
     * 
     * @param string $classNs The namespace of the class.
     *
     * @param array $args An optional array that holds arguments that will be passed to
     * class constructor.
     *
     * @return bool If the class exist and loaded, the method will return 
     * true. Other than that, false is returned.
     */
    public static function isClass(string $classNs, array $args = []) : bool {
        try {
            if (class_exists($classNs)) {
                $reflection = new ReflectionClass($classNs);
                $clazz = $reflection->newInstanceArgs($args);

                return gettype($clazz) == 'object';
            }
        } catch (Throwable $ex) {
            return false;
        }

        return false;
    }
    /**
     * Checks if given string represents floating number or not.
     * 
     * @param string $val The string that will be validated.
     * 
     * @return bool If the given string represents a floating number, true is returned.
     * False otherwise.
     */
    public static function isFloat(string $val) : bool {
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
     * Execute the validation function.
     * 
     * @param string $input The input that will be validated. Note that if
     * the value of the input is changed on the validation callback, it will
     * affect original variable as the passed value is a reference.
     * 
     * @return bool The return value of this method will depend on the implementation
     * of the validation callback. If it returns true, the method will
     * return true. If it returns false, the method will return false.
     */
    public function isValid(string &$input) : bool {
        return call_user_func_array($this->callback, array_merge([&$input], $this->params));
    }
    /**
     * Checks if a given string represents a valid class name or not.
     * 
     * @param string $name A string to check such as 'My_Super_Class'.
     * 
     * @return bool If the given string is a valid class name, the method
     * will return true. False otherwise.
     */
    public static function isValidClassName(string $name) : bool {
        $len = strlen($name);

        if ($len > 0) {
            return self::validateNsOrClassName($len, $name);
        }

        return false;
    }
    /**
     * Checks if provided string represents a valid namespace or not.
     * 
     * @param string $ns A string to be validated.
     * 
     * @return bool If the provided string represents a valid namespace, the
     * method will return true. False if it does not represent a valid namespace.
     */
    public static function isValidNamespace(string $ns) : bool {
        if ($ns == '\\') {
            return true;
        }

        if (strlen($ns) == 0) {
            return false;
        }
        $split = explode('\\', $ns);

        foreach ($split as $subNs) {
            $len = strlen($subNs);

            if (!self::validateNsOrClassName($len, $subNs)) {
                return false;
            }
        }

        return true;
    }
    private static function validateNsOrClassName(int $len, string $name) : bool {
        for ($x = 0 ; $x < $len ; $x++) {
            $char = $name[$x];

            if ($x == 0 && $char >= '0' && $char <= '9') {
                return false;
            }

            if (!(($char <= 'Z' && $char >= 'A') || ($char <= 'z' && $char >= 'a') || ($char >= '0' && $char <= '9') || $char == '_')) {
                return false;
            }
        }

        return true;
    }
}
