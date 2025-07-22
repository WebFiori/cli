<?php
namespace WebFiori\Cli;

use ReflectionClass;
use ReflectionException;
use WebFiori\Cli\Exceptions\IOException;
use WebFiori\Cli\Streams\InputStream;
use WebFiori\Cli\Streams\OutputStream;
/**
 * An abstract class that can be used to create new CLI command.
 * The developer can extend this class and use it to create a custom CLI 
 * command. The class can be used to display output to terminal and also read 
 * user input. In addition, the output can be formatted using ANSI escape sequences.
 * 
 * @author Ibrahim
 * 
 */
abstract class CliCommand {
    /**
     * An associative array that contains extra options that can be added to 
     * the command.
     * 
     * @var array
     * 
     * @since 1.0
     */
    private $addedOptions;
    /**
     * An array that holds the arguments of the command.
     * 
     * @var array
     * 
     * @since 1.0
     */
    private $args;
    /**
     * The name of the command.
     * 
     * @var string
     * 
     * @since 1.0
     */
    private $commandName;
    /**
     * A description of what does the command do.
     * 
     * @var string
     * 
     * @since 1.0
     */
    private $description;
    /**
     * The stream that is used to read data from.
     * 
     * @var InputStream
     * 
     * @since 1.0
     */
    private $inputStream;
    /**
     * The stream at which the output will be sent to.
     * 
     * @var OutputStream
     * 
     * @since 1.0
     */
    private $outputStream;
    /**
     * The runner that is used to execute the command.
     * 
     * @var Runner
     * 
     * @since 1.0
     */
    private $runner;
    /**
     * Creates new instance of the class.
     * 
     * @param string $commandName The name of the command as it will be called in 
     * the terminal. It must be non-empty string and does not contain spaces.
     * 
     * @param array $args An optional array of arguments. The array can have 
     * sub-associative arrays or an objects of type 'WebFiori\Cli\Argument'.
     * 
     * @param string $description An optional description of the command.
     * 
     * @since 1.0
     */
    public function __construct(string $commandName = '', array $args = [], string $description = '') {
        $this->addedOptions = [];
        $this->args = [];
        $this->commandName = '';
        $this->description = '';
        $this->setName($commandName);
        $this->setDescription($description);
        $this->addArgs($args);
    }
    /**
     * Adds new argument to the command.
     * 
     * @param Argument $arg The argument that will be added.
     * 
     * @return bool If the argument is added, the method will return true.
     * If not added, the method will return false.
     * 
     * @since 1.0
     */
    public function addArg(Argument $arg) : bool {
        $name = $arg->getName();

        if (!$this->hasArg($name)) {
            $this->args[$name] = $arg;

            return true;
        }

        return false;
    }
    /**
     * Adds multiple arguments to the command at once.
     * 
     * @param array $argsArr An array that contains the arguments that will be 
     * added. The array can have sub-associative arrays or an objects of type 
     * 'WebFiori\Cli\Argument'.
     * 
     * @return int The method will return the number of arguments that where 
     * added.
     * 
     * @since 1.0
     */
    public function addArgs(array $argsArr) : int {
        $added = 0;

        foreach ($argsArr as $argNameOrObj => $options) {
            if ($options instanceof Argument) {
                if ($this->addArg($options)) {
                    $added++;
                }
            } else if (gettype($options) == 'array') {
                $arg = Argument::create($argNameOrObj, $options);

                if ($arg !== null && $this->addArg($arg)) {
                    $added++;
                }
            }
        }

        return $added;
    }
    /**
     * Adds an option to the command.
     * 
     * The developer can use this method to add custom options to the command.
     * 
     * @param string $name The name of the option.
     * 
     * @param mixed $value The value of the option.
     * 
     * @since 1.0
     */
    public function addOption(string $name, $value) {
        $this->addedOptions[$name] = $value;
    }
    /**
     * Clears the console.
     * 
     * @since 1.0
     */
    public function clear() {
        $this->prints("\e[2J\e[;H");
    }
    /**
     * Confirms with the user that he wants to continue with specific action.
     * 
     * @param string $prompt The text that will be shown to the user. Default is 
     * 'Are you sure you want to continue?'.
     * 
     * @param array $options An array that contains the possible inputs that 
     * represents 'yes' and 'no'. The array has the following structure:
     * <p>
     * $options = [<br/>
     * &nbsp;&nbsp;'y' => ['yes', 'y'],<br/>
     * &nbsp;&nbsp;'n' => ['no', 'n']<br/>
     * ]
     * </p>
     * 
     * @return bool If the user choose 'yes', the method will return true. If 
     * the user choose 'no', the method will return false.
     * 
     * @since 1.0
     */
    public function confirm(string $prompt = 'Are you sure you want to continue?', array $options = []) : bool {
        if (count($options) == 0) {
            $options = [
                'y' => ['yes', 'y'],
                'n' => ['no', 'n']
            ];
        }
        $optionsStr = '';
        $yesOptions = $options['y'] ?? ['yes', 'y'];
        $noOptions = $options['n'] ?? ['no', 'n'];
        $yesStr = '';
        $noStr = '';

        foreach ($yesOptions as $yOpt) {
            $yesStr .= '/'.$yOpt;
        }
        $yesStr = substr($yesStr, 1);

        foreach ($noOptions as $nOpt) {
            $noStr .= '/'.$nOpt;
        }
        $noStr = substr($noStr, 1);
        $optionsStr = '('.$yesStr.', '.$noStr.')';
        $input = $this->readln($prompt.' '.$optionsStr.': ');
        $inputL = strtolower($input);

        foreach ($yesOptions as $yOpt) {
            if ($inputL == strtolower($yOpt)) {
                return true;
            }
        }

        return false;
    }
    /**
     * Execute the command.
     * 
     * This method should be implemented in a way it returns a status code 
     * which is used to indicate the status of execution. If the command 
     * executed without any errors, the method should return 0.
     * 
     * @return int If the command executed without errors, the method should 
     * return 0. Other than that, it should return a number that indicates the 
     * status of execution.
     * 
     * @since 1.0
     */
    public abstract function exec() : int;
    /**
     * Returns an array that contains all added arguments.
     * 
     * @return array An array that contains an objects of type 'WebFiori\Cli\Argument'.
     * 
     * @since 1.0
     */
    public function getArgs() : array {
        return $this->args;
    }
    /**
     * Returns the value of a specific argument.
     * 
     * @param string $name The name of the argument.
     * 
     * @return string|null If the argument is provided in the command, the method 
     * will return its value. Other than that, the method will return null.
     * 
     * @since 1.0
     */
    public function getArgValue(string $name) {
        $trimmedName = trim($name);

        if ($this->hasArg($trimmedName)) {
            $argObj = $this->getArg($trimmedName);
            $val = $argObj->getValue();

            if ($val === null && $argObj->isOptional()) {
                return $argObj->getDefault();
            }

            return $val;
        }

        return null;
    }
    /**
     * Returns the name of the command.
     * 
     * @return string The name of the command. Default return value is empty string.
     * 
     * @since 1.0
     */
    public function getName() : string {
        return $this->commandName;
    }
    /**
     * Returns the value of specific option.
     * 
     * @param string $optionName The name of the option.
     * 
     * @return mixed|null If the option does exist, the method will return 
     * its value. If the option does not exist, the method will return null.
     * 
     * @since 1.0
     */
    public function getOption(string $optionName) {
        $trimmed = trim($optionName);

        if (isset($this->addedOptions[$trimmed])) {
            return $this->addedOptions[$trimmed];
        }

        return null;
    }
    /**
     * Returns an associative array that contains all added options.
     * 
     * @return array An associative array that contains all added options.
     * 
     * @since 1.0
     */
    public function getOptions() : array {
        return $this->addedOptions;
    }
    /**
     * Returns the stream at which the command reads input from.
     * 
     * @return InputStream The stream at which the command reads input from.
     * 
     * @since 1.0
     */
    public function getInputStream() : InputStream {
        return $this->inputStream;
    }
    /**
     * Returns the stream at which the command sends output to.
     * 
     * @return OutputStream The stream at which the command sends output to.
     * 
     * @since 1.0
     */
    public function getOutputStream() : OutputStream {
        return $this->outputStream;
    }
    /**
     * Returns the runner that is used to execute the command.
     * 
     * @return Runner|null If the command is being executed by a runner, the 
     * method will return it as an object. If not, the method will return null.
     * 
     * @since 1.0
     */
    public function getRunner() {
        return $this->runner;
    }
    /**
     * Checks if the command has specific argument or not.
     * 
     * @param string $name The name of the argument.
     * 
     * @return bool If the argument is added to the command, the method will 
     * return true. Other than that, the method will return false.
     * 
     * @since 1.0
     */
    public function hasArg(string $name) : bool {
        $trimmed = trim($name);

        return isset($this->args[$trimmed]);
    }
    /**
     * Checks if the command has specific option or not.
     * 
     * @param string $name The name of the option.
     * 
     * @return bool If the option is set, the method will return true. Other than 
     * that, the method will return false.
     * 
     * @since 1.0
     */
    public function hasOption(string $name) : bool {
        $trimmed = trim($name);

        return isset($this->addedOptions[$trimmed]);
    }
    /**
     * Prints a message to the output stream.
     * 
     * @param string $message The message that will be printed.
     * 
     * @param mixed $_ One or more additional arguments that can be supplied to the 
     * method. The values of the arguments will be included in the message 
     * at the place of '%s'.
     * 
     * @since 1.0
     */
    public function prints(string $message, ...$_) {
        $args = func_get_args();
        $stream = $this->getOutputStream();

        if (count($args) == 1) {
            $stream->print($message);
        } else {
            $stream->prints($message, ...array_slice($args, 1));
        }
    }
    /**
     * Prints a message to the output stream and appends a line break at the end.
     * 
     * @param string $message The message that will be printed.
     * 
     * @param mixed $_ One or more additional arguments that can be supplied to the 
     * method. The values of the arguments will be included in the message 
     * at the place of '%s'.
     * 
     * @since 1.0
     */
    public function println(string $message = '', ...$_) {
        $args = func_get_args();
        $stream = $this->getOutputStream();

        if (count($args) == 0) {
            $stream->println();
        } else if (count($args) == 1) {
            $stream->println($message);
        } else {
            $stream->println($message, ...array_slice($args, 1));
        }
    }
    /**
     * Reads a specific number of characters from input stream.
     * 
     * @param string $prompt An optional prompt. If specified, the prompt will 
     * be sent to the output stream.
     * 
     * @param int $charsCount Number of characters that will be read. If 0 is 
     * given, one character will be read.
     * 
     * @return string The method will return the string which was given as input 
     * in the input stream.
     * 
     * @since 1.0
     */
    public function read(string $prompt = '', int $charsCount = 1) : string {
        if (strlen($prompt) != 0) {
            $this->prints($prompt);
        }
        $stream = $this->getInputStream();

        if ($charsCount == 0) {
            return $stream->read();
        } else {
            $input = '';

            for ($x = 0 ; $x < $charsCount ; $x++) {
                $input .= $stream->read();
            }

            return $input;
        }
    }
    /**
     * Reads a line from input stream.
     * 
     * @param string $prompt An optional prompt. If specified, the prompt will 
     * be sent to the output stream.
     * 
     * @return string The method will return the string which was taken from 
     * the input stream without the line breaks.
     * 
     * @since 1.0
     */
    public function readln(string $prompt = '') : string {
        if (strlen($prompt) != 0) {
            $this->prints($prompt);
        }
        $stream = $this->getInputStream();
        $line = $stream->readLine();

        return trim($line);
    }
    /**
     * Reads multiple lines from input stream.
     * 
     * @param string $prompt An optional prompt. If specified, the prompt will 
     * be sent to the output stream.
     * 
     * @param string $exitInput A string which if entered by the user, the 
     * reading will stop. Default value is 'exit'.
     * 
     * @return string The method will return the string which was taken from 
     * the input stream including the line breaks.
     * 
     * @since 1.0
     */
    public function readMultiple(string $prompt = '', string $exitInput = 'exit') : string {
        if (strlen($prompt) != 0) {
            $this->println($prompt);
        }
        $this->println("Enter '$exitInput' in a new line to finish.");
        $stream = $this->getInputStream();
        $text = '';
        $line = '';

        do {
            $line = $stream->readLine();
            $trimmedLine = trim($line);

            if ($trimmedLine == $exitInput) {
                break;
            }
            $text .= $line;
        } while (true);

        return $text;
    }
    /**
     * Reads a password from input stream.
     * 
     * @param string $prompt An optional prompt. If specified, the prompt will 
     * be sent to the output stream.
     * 
     * @return string The method will return the string which was taken from 
     * the input stream.
     * 
     * @since 1.0
     */
    public function readPassword(string $prompt = '') : string {
        if (strlen($prompt) != 0) {
            $this->prints($prompt);
        }
        $stream = $this->getInputStream();
        $password = '';
        $ch = '';

        do {
            $ch = $stream->read();

            if ($ch == "\n") {
                break;
            } else if ($ch == "\010" || $ch == "\177") {
                if (strlen($password) > 0) {
                    $password = substr($password, 0, strlen($password) - 1);
                }
            } else {
                $password .= $ch;
            }
        } while (true);

        return $password;
    }
    /**
     * Reads a value from the user and validate it.
     * 
     * @param string $prompt The text that will be shown to the user.
     * 
     * @param InputValidator $validator The validator that will be used to validate
     * user input.
     * 
     * @return string The method will return the value which was taken from 
     * the user.
     * 
     * @since 1.0
     */
    public function readValue(string $prompt, InputValidator $validator) : string {
        $valid = false;
        $input = '';

        do {
            $input = $this->readln($prompt);
            $valid = $validator->isValid($input);

            if (!$valid) {
                $this->error($validator->getErrPrompt());
            }
        } while (!$valid);

        return $input;
    }
    /**
     * Sets the description of the command.
     * 
     * @param string $str A string that describes what does the command do.
     * 
     * @since 1.0
     */
    public function setDescription(string $str) {
        $this->description = trim($str);
    }
    /**
     * Sets the stream at which the command will read input from.
     * 
     * @param InputStream $stream The stream that will be used to read user input.
     * 
     * @since 1.0
     */
    public function setInputStream(InputStream $stream) {
        $this->inputStream = $stream;
    }
    /**
     * Sets the name of the command.
     * 
     * @param string $name The name of the command. It must be non-empty string 
     * and does not contain spaces.
     * 
     * @return bool If the name of the command is set, the method will return 
     * true. If not set, the method will return false.
     * 
     * @since 1.0
     */
    public function setName(string $name) : bool {
        $trimmed = trim($name);

        if (strlen($trimmed) != 0 && strpos($trimmed, ' ') === false) {
            $this->commandName = $trimmed;

            return true;
        }

        return false;
    }
    /**
     * Sets the stream at which the command will send output to.
     * 
     * @param OutputStream $stream The stream that will be used to send command 
     * output.
     * 
     * @since 1.0
     */
    public function setOutputStream(OutputStream $stream) {
        $this->outputStream = $stream;
    }
    /**
     * Sets the runner that will be used to execute the command.
     * 
     * @param Runner $runner The runner that will be used to execute the command.
     * 
     * @since 1.0
     */
    public function setRunner(Runner $runner) {
        $this->runner = $runner;
    }
    /**
     * Display a message that represents an error.
     * 
     * The message will be prefixed with the string 'Error:' in red.
     * 
     * @param string $message The message that will be shown.
     * 
     * @param mixed $_ One or more additional arguments that can be supplied to the 
     * method. The values of the arguments will be included in the message 
     * at the place of '%s'.
     * 
     * @since 1.0
     */
    public function error(string $message, ...$_) {
        $args = func_get_args();
        $formattedErr = Formatter::format("Error:", [
            'color' => 'red',
            'bold' => true,
            'ansi' => true
        ]);
        $this->prints($formattedErr.' ');

        if (count($args) == 1) {
            $this->println($message);
        } else {
            $this->println($message, ...array_slice($args, 1));
        }
    }
    /**
     * Display a message that represents extra info.
     * 
     * The message will be prefixed with the string 'Info:' in blue.
     * 
     * @param string $message The message that will be shown.
     * 
     * @param mixed $_ One or more additional arguments that can be supplied to the 
     * method. The values of the arguments will be included in the message 
     * at the place of '%s'.
     * 
     * @since 1.0
     */
    public function info(string $message, ...$_) {
        $args = func_get_args();
        $formattedErr = Formatter::format("Info:", [
            'color' => 'blue',
            'bold' => true,
            'ansi' => true
        ]);
        $this->prints($formattedErr.' ');

        if (count($args) == 1) {
            $this->println($message);
        } else {
            $this->println($message, ...array_slice($args, 1));
        }
    }
    /**
     * Display a message that represents a success status.
     * 
     * The message will be prefixed with the string 'Success:' in green.
     * 
     * @param string $message The message that will be shown.
     * 
     * @param mixed $_ One or more additional arguments that can be supplied to the 
     * method. The values of the arguments will be included in the message 
     * at the place of '%s'.
     * 
     * @since 1.0
     */
    public function success(string $message, ...$_) {
        $args = func_get_args();
        $formattedErr = Formatter::format("Success:", [
            'color' => 'green',
            'bold' => true,
            'ansi' => true
        ]);
        $this->prints($formattedErr.' ');

        if (count($args) == 1) {
            $this->println($message);
        } else {
            $this->println($message, ...array_slice($args, 1));
        }
    }
    /**
     * Display a message that represents a warning.
     * 
     * The message will be prefixed with the string 'Warning:' in yellow.
     * 
     * @param string $message The message that will be shown.
     * 
     * @param mixed $_ One or more additional arguments that can be supplied to the 
     * method. The values of the arguments will be included in the message 
     * at the place of '%s'.
     * 
     * @since 1.0
     */
    public function warning(string $message, ...$_) {
        $args = func_get_args();
        $formattedErr = Formatter::format("Warning:", [
            'color' => 'yellow',
            'bold' => true,
            'ansi' => true
        ]);
        $this->prints($formattedErr.' ');

        if (count($args) == 1) {
            $this->println($message);
        } else {
            $this->println($message, ...array_slice($args, 1));
        }
    }
    /**
     * Returns the description of the command.
     * 
     * @return string The description of the command. Default return value is 
     * empty string.
     * 
     * @since 1.0
     */
    public function getDescription() : string {
        return $this->description;
    }
    /**
     * Returns an object that represents one of the arguments of the command.
     * 
     * @param string $name The name of the argument.
     * 
     * @return Argument|null If the argument is added to the command, the method 
     * will return it as an object. If not added, the method will return null.
     * 
     * @since 1.0
     */
    public function getArg(string $name) {
        $trimmed = trim($name);

        if ($this->hasArg($trimmed)) {
            return $this->args[$trimmed];
        }

        return null;
    }
    /**
     * Checks if the command is running in interactive mode or not.
     * 
     * @return bool If the command is running in interactive mode, the method 
     * will return true. False if not.
     * 
     * @since 1.0
     */
    public function isInteractive() : bool {
        $runner = $this->getRunner();

        if ($runner !== null) {
            return $runner->isInteractive();
        }

        return false;
    }
    /**
     * Checks if the command is running in verbose mode or not.
     * 
     * @return bool If the command is running in verbose mode, the method 
     * will return true. False if not.
     * 
     * @since 1.0
     */
    public function isVerbose() : bool {
        $runner = $this->getRunner();

        if ($runner !== null) {
            return $runner->isVerbose();
        }

        return false;
    }
    /**
     * Removes an argument from the command.
     * 
     * @param string $name The name of the argument.
     * 
     * @return bool If the argument is removed, the method will return true. 
     * If not, the method will return false.
     * 
     * @since 1.0
     */
    public function removeArg(string $name) : bool {
        $trimmed = trim($name);

        if ($this->hasArg($trimmed)) {
            unset($this->args[$trimmed]);

            return true;
        }

        return false;
    }
    /**
     * Removes an option from the command.
     * 
     * @param string $name The name of the option.
     * 
     * @return bool If the option is removed, the method will return true. 
     * If not, the method will return false.
     * 
     * @since 1.0
     */
    public function removeOption(string $name) : bool {
        $trimmed = trim($name);

        if ($this->hasOption($trimmed)) {
            unset($this->addedOptions[$trimmed]);

            return true;
        }

        return false;
    }
    /**
     * Validate command arguments.
     * 
     * @return bool If all command arguments are valid, the method will return 
     * true. If there's at least one invalid argument, the method will return 
     * false.
     * 
     * @since 1.0
     */
    public function validateArgs() : bool {
        $args = $this->getArgs();
        $runner = $this->getRunner();
        $isValid = true;

        foreach ($args as $argObj) {
            $argName = $argObj->getName();
            $val = Argument::extractValue($argName, $runner);

            if ($val !== null) {
                if (!$argObj->setValue($val)) {
                    $this->error("The value '$val' is not allowed for the argument '$argName'.");
                    $isValid = false;
                }
            } else if (!$argObj->isOptional()) {
                $this->error("The argument '$argName' is required.");
                $isValid = false;
            }
        }

        return $isValid;
    }
}
