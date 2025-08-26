<?php
namespace WebFiori\Cli;

/**
 * A class which is used to represent command line argument.
 *
 * @author Ibrahim
 */
class Argument {
    private $allowedValues;
    private $default;
    private $description;
    private $isOptional;
    private $name;
    private $value;
    /**
     * Creates new instance of the class.
     * 
     * @param string $name The name of the argument such as '--version'.
     * 
     * @param string $description A short description of the argument and how
     * it affects the execution of the command that it belongs to.
     * 
     * @param bool $optional If set to true, the argument will be considered as
     * optional.
     */
    public function __construct(string $name = 'arg', string $description = '', bool $optional = false) {
        if (!$this->setName($name)) {
            $this->name = 'arg';
        }
        $this->isOptional = $optional;
        $this->allowedValues = [];
        $this->default = '';
        $this->description = $description;
    }
    /**
     * Adds a value to the set of allowed argument values.
     * 
     * @param string $val A string that represents the value.
     * 
     * @return Argument The method will return the same instance at which
     * the method was called on.
     */
    public function addAllowedValue(string $val) : Argument {
        $trim = trim($val);

        if (!in_array($trim, $this->getAllowedValues())) {
            $this->allowedValues[] = $trim;
        }

        return $this;
    }
    /**
     * Creates an instance of the class provided its name and a set of options.
     * 
     * @param string $name The name of the command such as 'help'
     * 
     * @param array $options An associative array of options which is used to
     * configure created instance. Supported options are:
     * <ul>
     * <li><b>optional</b>: A boolean. if set to true, it means that the argument 
     * is optional and can be ignored when running the command.</li>
     * <li><b>default</b>: An optional default value for the argument 
     * to use if it is not provided and is optional.</li>
     * <li><b>description</b>: A description of the argument which 
     * will be shown if the command 'help' is executed.</li>
     * <li><b>values</b>: A set of values that the argument can have. If provided, 
     * only the values on the list will be allowed. Note that if null or empty string 
     * is in the array, it will be ignored. Also, if boolean values are 
     * provided, true will be converted to the string 'y' and false will 
     * be converted to the string 'n'.</li>
     * </ul>
     * 
     * @return Argument|null If the instance is created, the method will
     * return it as an object. Other than that, null is returned.
     */
    public static function create(string $name, array $options) {
        if (strlen($name) == 0) {
            return null;
        }
        $arg = new Argument($name);

        if ($arg->getName() == 'arg') {
            return null;
        }

        if (isset($options[Option::OPTIONAL])) {
            $arg->setIsOptional($options[Option::OPTIONAL]);
        }
        $desc = isset($options[Option::DESCRIPTION]) ? trim($options[Option::DESCRIPTION]) : '<NO DESCRIPTION>';

        if (strlen($desc) != 0) {
            $arg->setDescription($desc);
        } else {
            $arg->setDescription('<NO DESCRIPTION>');
        }
        $allowedValues = $options[Option::VALUES] ?? [];

        foreach ($allowedValues as $val) {
            $arg->addAllowedValue($val);
        }

        if (isset($options[Option::DEFAULT]) && gettype($options[Option::DEFAULT]) == 'string') {
            $arg->setDefault($options[Option::DEFAULT]);
        }

        return $arg;
    }
    /**
     * Extract the value of an argument give its name.
     * 
     * @param string $argName The name of the argument as provided
     * in the terminal.
     * 
     * @return string|null If the argument is provided and its value is set,
     * the method will return its value. If provided without any value,
     * the method will return empty string. If not provided, null is returned.
     */
    public static function extractValue(string $argName, ?Runner $runner = null) {
        $trimmedOptName = trim($argName);

        if ($runner !== null) {
            $argsV = $runner->getArgsVector();
        } else {
            $argsV = $_SERVER['argv'];
        }

        foreach ($argsV as $option) {
            $optionClean = filter_var($option);
            $optExpl = explode('=', $optionClean);
            $optionNameFromCLI = $optExpl[0];

            if ($optionNameFromCLI == $trimmedOptName) {
                if (count($optExpl) == 2) {
                    return trim(trim(trim($optExpl[1],'"'), "'"));
                } else {
                    //If arg is provided, set its value empty string
                    return '';
                }
            }
        }

        return null;
    }
    /**
     * Returns an array that contains all allowed argument values.
     * 
     * @return array An array that contains all allowed argument values.
     */
    public function getAllowedValues() : array {
        return $this->allowedValues;
    }
    /**
     * Returns the default value of the argument.
     * 
     * @return string The default value of the argument. Default return value is
     * empty string.
     */
    public function getDefault() : string {
        return $this->default;
    }
    /**
     * Returns a string that represents the description of the argument.
     * 
     * The value is used by the command 'help' to show argument help.
     * 
     * @return string A string that represents the description of the argument.
     * Default is empty string.
     */
    public function getDescription() : string {
        return $this->description;
    }
    /**
     * Returns the name of the argument.
     * 
     * 
     * @return string The name of the argument. Default return value is 'arg'.
     */
    public function getName() : string {
        return $this->name;
    }
    /**
     * Returns the value of the argument as provided in the terminal.
     * 
     * @return string|null If set, the method will return its value as string.
     * If not set, null is returned. Note that if the argument is provided in
     * terminal but its value is not set, the returned value will be empty 
     * string.
     */
    public function getValue() {
        return $this->value;
    }
    /**
     * Checks if the argument is optional or not.
     * 
     * @return bool If the argument is set as optional, the method will return
     * true. False if not optional. Default is false.
     */
    public function isOptional() : bool {
        return $this->isOptional;
    }
    /**
     * Reset the value of the argument and set it to null.
     */
    public function resetValue() {
        $this->value = null;
    }
    /**
     * Sets a string as default value for the argument.
     * 
     * @param string $default A string that will be set as default value if the
     * argument is not provided in terminal. Note that the value will be trimmed.
     */
    public function setDefault(string $default) {
        $this->default = trim($default);
    }
    /**
     * Sets the description of the argument.
     * 
     * The value is used by the command 'help' to show argument help.
     * 
     * @param string $desc A string that represents the description of the argument.
     */
    public function setDescription(string $desc) {
        $this->description = trim($desc);
    }
    /**
     * Make the argument as optional argument or mandatory.
     * 
     * @param bool $optional True to make it optional. False to make it mandatory.
     */
    public function setIsOptional(bool $optional) {
        $this->isOptional = $optional;
    }
    /**
     * Sets the name of the argument.
     * 
     * @param string $name A string such as '--config' or similar. It must be
     * non-empty string and have no spaces.
     * 
     * @return boolean If set, the method will return true. False otherwise.
     */
    public function setName(string $name) : bool {
        $trimmed = trim($name);

        if (strlen($trimmed) == 0 || strpos($trimmed, ' ') !== false) {
            return false;
        }
        $this->name = $trimmed;

        return true;
    }
    /**
     * Sets the value of the argument.
     * 
     * Note that the method will return false only if the argument can have a
     * fixed set of values and provided value is not one of them.
     * 
     * @param string $val The value to set. Note that spaces in the provided value
     * will be trimmed.
     * 
     * @return bool If the value of the argument is set, the method will return
     * true. If not, the method will return false.
     */
    public function setValue(string $val) : bool {
        $allowed = $this->getAllowedValues();

        if (count($allowed) == 0 || in_array($val, $allowed)) {
            $this->value = trim($val);

            return true;
        }

        return false;
    }
}
