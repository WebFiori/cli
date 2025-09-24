<?php
namespace WebFiori\Cli;

use Error;
use Exception;
use ReflectionClass;
use ReflectionException;
use WebFiori\Cli\Exceptions\IOException;
use WebFiori\Cli\Progress\ProgressBar;
use WebFiori\Cli\Streams\InputStream;
use WebFiori\Cli\Streams\OutputStream;
use WebFiori\Cli\Table\TableBuilder;
use WebFiori\Cli\Table\TableOptions;
use WebFiori\Cli\Table\TableTheme;
/**
 * An abstract class that can be used to create new CLI command.
 * The developer can extend this class and use it to create a custom CLI 
 * command. The class can be used to display output to terminal and also read 
 * user input. In addition, the output can be formatted using ANSI escape sequences.
 * 
 * @author Ibrahim
 * 
 */
abstract class Command {
    /**
     * An array of aliases for the command.
     * @var array
     */
    private $aliases;
    /**
     * An associative array that contains extra options that can be added to 
     * the command.
     * @var array
     */
    private $commandArgs;
    /**
     * The name of the command such as 'help'.
     * @var string 
     */
    private $commandName;
    /**
     * A description of how to use the command or what it does.
     * @var string
     */
    private $description;
    /**
     * 
     * @var InputStream
     * 
     */
    private $inputStream;
    /**
     * 
     * @var OutputStream
     * 
     */
    private $outputStream;
    private $owner;
    /**
     * Creates new instance of the class.
     * 
     * @param string $commandName A string that represents the name of the 
     * command such as '-v' or 'help'. If invalid name provided, the 
     * value 'new-command' is used.
     * 
     * @param array $args An associative array of sub-associative arrays of arguments (or options) which can 
     * be supplied to the command when running it. The 
     * key of each sub array is argument name. Each 
     * sub-array can have the following indices as argument options:
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
     * @param string $description A string that describes what does the job 
     * do. The description will appear when the command 'help' is executed.
     * 
     * @param array $aliases An optional array of aliases for the command.
     */
    public function __construct(string $commandName, array $args = [], string $description = '', array $aliases = []) {
        if (!$this->setName($commandName)) {
            $this->setName('new-command');
        }
        $this->aliases = $aliases;
        $this->addArgs($args);

        if (!$this->setDescription($description)) {
            $this->setDescription('<NO DESCRIPTION>');
        }
    }
    /**
     * Add command argument.
     * 
     * An argument is a string that comes after the name of the command. The value 
     * of an argument can be set using equal sign. For example, if command name 
     * is 'do-it' and one argument has the name 'what-to-do', then the full 
     * CLI command would be "do-it what-to-do=say-hi". An argument can be 
     * also treated as an option.
     * 
     * @param string $name The name of the argument. It must be non-empty string 
     * and does not contain spaces. Note that if the argument is already added and 
     * the developer is trying to add it again, the new options array will override 
     * the existing options array.
     * 
     * @param array $options An optional array of options. Available options are:
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
     * @return bool If the argument is added, the method will return true.
     * Other than that, the method will return false.
     * 
     */
    public function addArg(string $name, array $options = []) : bool {
        $toAdd = Argument::create($name, $options);

        if ($toAdd === null) {
            return false;
        }

        return $this->addArgument($toAdd);
    }
    /**
     * Adds multiple arguments to the command.
     * 
     * @param array $arr An associative array of sub associative arrays. The 
     * key of each sub array is argument name. This can also be
     * an array of objects of type 'CommandArgument'. For arrays, Each 
     * sub-array can have the following indices:
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
     */
    public function addArgs(array $arr) {
        $this->commandArgs = [];

        foreach ($arr as $optionName => $options) {
            if ($options instanceof Argument) {
                $this->addArgument($options);
            } else {
                $this->addArg($optionName, $options);
            }
        }
    }
    /**
     * Adds new command argument.
     * 
     * @param Argument $arg The argument that will be added.
     * 
     * @return bool If the argument is added, the method will return true.
     * If not, false is returned. The argument will not be added only if an argument
     * which has same name is added.
     */
    public function addArgument(Argument $arg) : bool {
        if (!$this->hasArg($arg->getName())) {
            $this->commandArgs[] = $arg;

            return true;
        }

        return false;
    }
    /**
     * Clears the output before or after cursor position.
     * 
     * This method will replace the visible characters with spaces.
     * Note that support for this operation depends on terminal support for 
     * ANSI escape codes.
     * 
     * @param int $numberOfCols Number of columns to clear. The columns that 
     * will be cleared are before and after cursor position. They don't include 
     * the character at which the cursor is currently pointing to.
     * @param bool $beforeCursor If set to true, the characters which
     * are before the cursor will be cleared. Default is true.
     * 
     * @return Command The method will return the instance at which the
     * method is called on.
     * 
     */
    public function clear(int $numberOfCols = 1, bool $beforeCursor = true) : Command {
        if ($numberOfCols >= 1 && $beforeCursor) {
            for ($x = 0 ; $x < $numberOfCols ; $x++) {
                $this->moveCursorLeft();
                $this->prints(" ");
                $this->moveCursorLeft();
            }
            $this->moveCursorRight($numberOfCols);
        } else if ($numberOfCols >= 1) {
            $this->moveCursorRight();

            for ($x = 0 ; $x < $numberOfCols ; $x++) {
                $this->prints(" ");
            }
            $this->moveCursorLeft($numberOfCols + 1);
        }

        return $this;
    }
    /**
     * Clears the whole content of the console.
     * 
     * Note that support for this operation depends on terminal support for 
     * ANSI escape codes.
     * 
     * @return Command The method will return the instance at which the
     * method is called on.
     */
    public function clearConsole() : Command {
        $this->prints("\ec");

        return $this;
    }
    /**
     * Clears the line at which the cursor is in and move it back to the start 
     * of the line.
     * 
     * Note that support for this operation depends on terminal support for 
     * ANSI escape codes.
     * 
     */
    public function clearLine() {
        $this->prints("\e[2K");
        $this->prints("\r");
    }
    /**
     * Asks the user to conform something.
     * 
     * This method will display the question and wait for the user to confirm the 
     * action by entering 'y' or 'n' in the terminal. If the user give something 
     * other than 'Y' or 'n', it will show an error and ask him to confirm
     * again. If a default answer is provided, it will appear in upper case in the 
     * terminal. For example, if default is set to true, at the end of the prompt, 
     * the string that shows the options would be like '(Y/n)'.
     * 
     * @param string $confirmTxt The text of the question of which will be asked.
     * 
     * @param bool|null $default Default answer to use if empty input is given.
     * It can be true for 'y' and false for 'n'. Default value is null which 
     * means no default will be used.
     *
     * @return bool If the user choose 'y', the method will return true. If
     * he chooses 'n', the method will return false.
     *
     * 
     */
    public function confirm(string $confirmTxt, ?bool $default = null) : bool {
        $answer = null;

        do {
            if ($default === true) {
                $optionsStr = '(Y/n)';
            } else if ($default === false) {
                $optionsStr = '(y/N)';
            } else {
                $optionsStr = '(y/n)';
            }
            $this->prints(trim($confirmTxt), [
                'color' => 'gray',
                'bold' => true
            ]);
            $this->println($optionsStr, [
                'color' => 'light-blue'
            ]);

            $input = strtolower(trim($this->readln()));

            if ($input == 'n') {
                $answer = false;
            } else if ($input == 'y') {
                $answer = true;
            } else if (strlen($input) == 0 && $default !== null) {
                return $default === true;
            } else {
                $this->error('Invalid answer. Choose \'y\' or \'n\'.');
            }
        } while ($answer === null);

        return $answer;
    }

    /**
     * Creates and returns a new progress bar instance.
     * 
     * @param int $total Total number of steps
     * @return ProgressBar
     */
    public function createProgressBar(int $total = 100): ProgressBar {
        return new ProgressBar($this->getOutputStream(), $total);
    }
    /**
     * Display a message that represents an error.
     * 
     * The message will be prefixed with the string 'Error:' in 
     * red.
     * 
     * @param string $message The message that will be shown.
     * 
     */
    public function error(string $message) {
        $this->printMsg($message, 'Error', 'light-red');
    }
    /**
     * Execute the command.
     * 
     * This method should not be called manually by the developer.
     * 
     * @return int If the command is executed, the method will return 0.
     * Other than that, it will return a number which depends on the return value of
     * the method 'Command::exec()'.
     * 
     */
    public function excCommand() : int {
        $retVal = -1;

        $runner = $this->getOwner();

        if ($runner !== null) {
            foreach ($runner->getArgs() as $arg) {
                $this->addArgument($arg);
            }
        }

        if ($this->parseArgsHelper()) {
            // Check for help first, before validating required arguments
            if ($this->isArgProvided('help') || $this->isArgProvided('-h')) {
                $help = $runner->getCommandByName('help');
                $help->setArgValue('--command-name', $this->getName());
                $help->setOwner($runner);
                $help->setOutputStream($runner->getOutputStream());
                $this->removeArgument('help');
                
                return $help->exec();
            } else if ($this->checkIsArgsSetHelper()) {
                $retVal = $this->exec();
            }
            
        }

        if ($runner !== null) {
            foreach ($runner->getArgs() as $arg) {
                $this->removeArgument($arg->getName());
                $arg->resetValue();
            }
        }

        return $retVal;
    }
    /**
     * Execute the command.
     * 
     * The implementation of this method should contain the code that will run 
     * when the command is executed.
     * 
     * @return int The developer should implement this method in a way it returns 0 
     * if the command is executed successfully and return -1 if the 
     * command did not execute successfully.
     * 
     */
    public abstract function exec() : int;
    /**
     * Execute a registered command using a sub-runner.
     * 
     * This method can be used to execute a registered command within the runner 
     * using another
     * runner instance which shares argsv, input and output streams with the
     * main runner. It can be used to invoke another command from within a
     * running command.
     * 
     * @param string $name The name of the command. It must be a part of
     * registered commands.
     * 
     * @param array $additionalArgs An associative array that represents additional arguments
     * to be passed to the command.
     * 
     * @return int The method will return an integer that represent exit status
     * code of the command after execution.
     */
    public function execSubCommand(string $name, $additionalArgs = []) : int {
        $runner = $this->getOwner();

        if ($runner === null) {
            return -1;
        }

        return $runner->runCommandAsSub($name, $additionalArgs);
    }
    /**
     * Returns an array of aliases for the command.
     * 
     * @return array An array of aliases.
     */
    public function getAliases() : array {
        return $this->aliases;
    }
    
    /**
     * Sets the aliases for the command.
     * 
     * @param array $aliases An array of aliases.
     */
    public function setAliases(array $aliases): void {
        $this->aliases = $aliases;
    }
    
    /**
     * Adds an alias to the command.
     * 
     * @param string $alias The alias to add.
     */
    public function addAlias(string $alias): void {
        if (!in_array($alias, $this->aliases)) {
            $this->aliases[] = $alias;
        }
    }
    /**
     * Returns an object that holds argument info if the command.
     * 
     * @param string $name The name of command argument.
     * 
     * @return Argument|null If the command has an argument with the
     * given name, it will be returned. Other than that, null is returned.
     */
    public function getArg(string $name) {
        foreach ($this->getArgs() as $arg) {
            if ($arg->getName() == $name) {
                return $arg;
            }
        }

        return null;
    }
    /**
     * Returns an associative array that contains command args.
     * 
     * @return array An associative array. The indices of the array are 
     * the names of the arguments and the values are sub-associative arrays. 
     * the sub arrays will have the following indices: 
     * <ul>
     * <li>optional</li>
     * <li>description</li>
     * <li>default</li>
     * <ul>
     * Note that the last index might not be set.
     * 
     */
    public function getArgs() : array {
        return $this->commandArgs;
    }
    /**
     * Returns an array that contains the names of command arguments.
     * 
     * @return array An array of strings.
     */
    public function getArgsNames() : array {
        return array_map(function ($el) {
            return $el->getName();
        }, $this->getArgs());
    }
    /**
     * Returns the value of command option from CLI given its name.
     * 
     * @param string $optionName The name of the option.
     * 
     * @return string|null If the value of the option is set, the method will 
     * return its value as string. If it is not set, the method will return null.
     * 
     */
    public function getArgValue(string $optionName) {
        $trimmedOptName = trim($optionName);
        $arg = $this->getArg($trimmedOptName);

        if ($arg !== null) {
            $runner = $this->getOwner();

            // Always return the set value if it exists, regardless of interactive mode
            if ($arg->getValue() !== null) {
                return $arg->getValue();
            }

            return Argument::extractValue($trimmedOptName, $runner);
        }

        return null;
    }
    /**
     * Returns the description of the command.
     * 
     * The description of the command is a string that describes what does the 
     * command do, and it will appear in CLI if the command 'help' is executed.
     * 
     * @return string The description of the command. Default return value 
     * is '&lt;NO DESCRIPTION&gt;'
     * 
     */
    public function getDescription() : string {
        return $this->description;
    }

    /**
     * Take an input value from the user.
     *
     * @param string $prompt The string that will be shown to the user. The
     * string must be non-empty.
     *
     * @param string|null $default An optional default value to use in case the user
     * hit "Enter" without entering any value. If null is passed, no default
     * value will be set.
     *
     * @param InputValidator|null $validator A callback that can be used to validate user
     * input. The callback accepts one parameter which is the value that
     * the user has given. If the value is valid, the callback must return true.
     * If the callback returns anything else, it means the value which is given
     * by the user is invalid and this method will ask the user to enter the
     * value again.
     *
     * @return string|null The method will return the value which was taken from the
     * user. If prompt string is empty, null will be returned. 
     * Note that if the input has special characters or spaces at the
     * beginning or the end, they will be trimmed.
     *
     */
    public function getInput(string $prompt, ?string $default = null, ?InputValidator $validator = null) {
        $trimmed = trim($prompt);

        if (strlen($trimmed) > 0) {
            do {
                $this->prints($trimmed, [
                    'color' => 'gray',
                    'bold' => true
                ]);

                if ($default !== null) {
                    $this->prints(" Enter = '".$default."'", [
                        'color' => 'light-blue'
                    ]);
                }
                $this->println();
                $input = trim($this->readln());

                $check = $this->getInputHelper($input, $validator, $default);

                if ($check['valid']) {
                    return $check['value'];
                }
            } while (true);
        }

        return null;
    }
    /**
     * Returns the stream at which the command is sing to read inputs.
     * 
     * @return null|InputStream If the stream is set, it will be returned as 
     * an object. Other than that, the method will return null.
     * 
     */
    public function getInputStream() : InputStream {
        return $this->inputStream;
    }
    /**
     * Returns the name of the command.
     * 
     * The name of the command is a string which is used to call the command 
     * from CLI.
     * 
     * @return string The name of the command (such as 'v' or 'help'). Default 
     * return value is 'new-command'.
     * 
     */
    public function getName() : string {
        return $this->commandName;
    }
    /**
     * Returns the stream at which the command is using to send output.
     * 
     * @return null|OutputStream If the stream is set, it will be returned as 
     * an object. Other than that, the method will return null.
     * 
     */
    public function getOutputStream() : OutputStream {
        return $this->outputStream;
    }
    /**
     * Returns the runner which is used to execute the command.
     * 
     * @return Runner|null If the command was called using a runner, this method
     * will return an instance that can be used to access runner's properties.
     * If not called through a runner, null is returned.
     */
    public function getOwner() {
        return $this->owner;
    }
    /**
     * Checks if the command has a specific command line argument or not.
     * 
     * @param string $argName The name of the command line argument.
     * 
     * @return bool If the argument is added to the command, the method will
     * return true. If no argument which has the given name does exist, the method 
     * will return false.
     * 
     */
    public function hasArg(string $argName) : bool {
        foreach ($this->getArgs() as $arg) {
            if ($arg->getName() == $argName) {
                return true;
            }
        }

        return false;
    }
    /**
     * Display a message that represents extra information.
     * 
     * The message will be prefixed with the string 'Info:' in 
     * blue.
     * 
     * @param string $message The message that will be shown.
     * 
     */
    public function info(string $message) {
        $this->printMsg($message, 'Info', 'blue');
    }
    /**
     * Checks if an argument is provided in the CLI or not.
     * 
     * The method will not check if the argument has a value or not.
     * 
     * @param string $argName The name of the command line argument.
     * 
     * @return bool If the argument is provided, the method will return
     * true. Other than that, the method will return false.
     * 
     */
    public function isArgProvided(string $argName) : bool {
        $argObj = $this->getArg($argName);

        if ($argObj !== null) {
            return $argObj->getValue() !== null;
        }

        return false;
    }
    /**
     * Moves the cursor down by specific number of lines.
     * 
     * Note that support for this operation depends on terminal support for 
     * ANSI escape codes.
     * 
     * @param int $lines The number of lines the cursor will be moved. Default 
     * value is 1.
     * 
     */
    public function moveCursorDown(int $lines = 1) {
        if ($lines >= 1) {
            $this->prints("\e[".$lines."B");
        }
    }
    /**
     * Moves the cursor to the left by specific number of columns.
     * 
     * Note that support for this operation depends on terminal support for 
     * ANSI escape codes.
     * 
     * @param int $numberOfCols The number of columns the cursor will be moved. Default 
     * value is 1.
     * 
     */
    public function moveCursorLeft(int $numberOfCols = 1) {
        if ($numberOfCols >= 1) {
            $this->prints("\e[".$numberOfCols."D");
        }
    }
    /**
     * Moves the cursor to the right by specific number of columns.
     * 
     * Note that support for this operation depends on terminal support for 
     * ANSI escape codes.
     * 
     * @param int $numberOfCols The number of columns the cursor will be moved. Default 
     * value is 1.
     * 
     */
    public function moveCursorRight(int $numberOfCols = 1) {
        if ($numberOfCols >= 1) {
            $this->prints("\e[".$numberOfCols."C");
        }
    }
    /**
     * Moves the cursor to specific position in the terminal.
     * 
     * If no arguments are supplied to the method, it will move the cursor 
     * to the upper-left corner of the screen (line 0, column 0).
     * Note that support for this operation depends on terminal support for 
     * ANSI escape codes.
     * 
     * @param int $line The number of line at which the cursor will be moved 
     * to. If not specified, 0 is used.
     * 
     * @param int $col The number of column at which the cursor will be moved 
     * to. If not specified, 0 is used.
     * 
     */
    public function moveCursorTo(int $line = 0, int $col = 0) {
        if ($line > -1 && $col > -1) {
            $this->prints("\e[".$line.";".$col."H");
        }
    }
    /**
     * Moves the cursor up by specific number of lines.
     * 
     * Note that support for this operation depends on terminal support for 
     * ANSI escape codes.
     * 
     * @param int $lines The number of lines the cursor will be moved. Default 
     * value is 1.
     * 
     */
    public function moveCursorUp(int $lines = 1) {
        if ($lines >= 1) {
            $this->prints("\e[".$lines."A");
        }
    }
    /**
     * Prints an array as a list of items.
     * 
     * This method is useful if the developer would like to print out a list 
     * of multiple items. Each item will be prefixed with a number that represents 
     * its index in the array.
     * 
     * @param array $array The array that will be printed.
     * 
     */
    public function printList(array $array) {
        for ($x = 0 ; $x < count($array) ; $x++) {
            $this->prints("- ", [
                'color' => 'green'
            ]);
            $this->println($array[$x]);
        }
    }
    /**
     * Print out a string and terminates the current line by writing the 
     * line separator string.
     * 
     * This method will work like the function fprintf(). The difference is that 
     * it will print out to the stream at which was specified by the method 
     * Command::setOutputStream() and the text can have formatting 
     * options. Note that support for output formatting depends on terminal support for 
     * ANSI escape codes.
     * 
     * @param string $str The string that will be printed.
     * 
     * @param mixed $_ One or more extra arguments that can be supplied to the 
     * method. The last argument can be an array that contains text formatting options. 
     * for available options, check the method Command::formatOutput().
     */
    public function println(string $str = '', ...$_) {
        $argsCount = count($_);

        if ($argsCount != 0 && gettype($_[$argsCount - 1]) == 'array') {
            //Last index contains formatting options.
            $_[$argsCount - 1]['ansi'] = $this->isArgProvided('--ansi');
            $str = Formatter::format($str, $_[$argsCount - 1]);
        }
        call_user_func_array([$this->getOutputStream(), 'println'], $this->_createPassArray($str, $_));
    }
    /**
     * Print out a string.
     * 
     * This method works exactly like the function 'fprintf()'. The only 
     * difference is that the method will print out the output to the stream 
     * that was specified using the method Command::setOutputStream() and 
     * the method accepts formatting options as last argument to format the output. 
     * Note that support for output formatting depends on terminal support for 
     * ANSI escape codes.
     * 
     * @param string $str The string that will be printed.
     * 
     * @param mixed $_ One or more extra arguments that can be supplied to the 
     * method. The last argument can be an array that contains text formatting options. 
     * for available options, check the method Command::formatOutput().
     * 
     */
    public function prints(string $str, ...$_) {
        $argCount = count($_);
        $formattingOptions = [];

        if ($argCount != 0 && gettype($_[$argCount - 1]) == 'array') {
            $formattingOptions = $_[$argCount - 1];
        }

        $formattingOptions['ansi'] = $this->isArgProvided('--ansi');

        $formattedStr = Formatter::format($str, $formattingOptions);

        call_user_func_array([$this->getOutputStream(), 'prints'], $this->_createPassArray($formattedStr, $_));
    }

    /**
     * Reads a string of bytes from input stream.
     * 
     * This method is used to read specific number of characters from input stream.
     * 
     * @return string The method will return the string which was given as input 
     * in the input stream.
     * 
     */
    public function read(int $bytes = 1) : string {
        return $this->getInputStream()->read($bytes);
    }
    /**
     * Reads and validates class name.
     * 
     * @param string|null $suffix An optional string to append to class name.
     * 
     * @param string $prompt The text that will be shown to the user as prompt for
     * class name.
     * 
     * @param string $errMsg A string to show in case provided class name is
     * not valid.
     * 
     * @return string A string that represents a valid class name. If suffix is
     * not null, the method will return the name with the suffix included.
     */
    public function readClassName(string $prompt, ?string $suffix = null, string $errMsg = 'Invalid class name is given.') {
        return $this->getInput($prompt, null, new InputValidator(function (&$className, $suffix) {
            if ($suffix !== null) {
                $subSuffix = substr($className, strlen($className) - strlen($suffix));

                if ($subSuffix != $suffix) {
                    $className .= $suffix;
                }
            }

            return InputValidator::isValidClassName($className);
        }, $errMsg, [$suffix]));
    }

    /**
     * Reads a value as float.
     *
     * @param string $prompt The string that will be shown to the user. The
     * string must be non-empty.
     *
     * @param float|null $default An optional default value to use in case the user
     * hit "Enter" without entering any value. If null is passed, no default
     * value will be set.
     *
     * @return float
     */
    public function readFloat(string $prompt, ?float $default = null) : float {
        return $this->getInput($prompt, $default, new InputValidator(function ($val) {
            return InputValidator::isFloat($val);
        }, 'Provided value is not a floating number!'));
    }

    /**
     * Reads the namespace of class and return an instance of it.
     *
     * @param string $prompt The string that will be shown to the user. The
     * string must be non-empty.
     *
     * @param string $errMsg A string to show in case provided namespace is
     * invalid or an instance of the class cannot be created.
     *
     * @return object The method will return an instance of the class.
     *
     * @throws ReflectionException If the method was not able to initiate class instance.
     */
    public function readInstance(string $prompt, string $errMsg = 'Invalid Class!', $constructorArgs = []) {
        $clazzNs = $this->getInput($prompt, null, new InputValidator(function ($input) {
            if (InputValidator::isClass($input)) {
                return true;
            }

            return false;
        }, $errMsg));

        $reflection = new ReflectionClass($clazzNs);

        return $reflection->newInstanceArgs($constructorArgs);
    }
    /**
     * Reads a value as an integer.
     * 
     * @param string $prompt The string that will be shown to the user. The 
     * string must be non-empty.
     * 
     * @param int $default An optional default value to use in case the user 
     * hit "Enter" without entering any value. If null is passed, no default 
     * value will be set.
     * 
     * @return int
     */
    public function readInteger(string $prompt, ?int $default = null) : int {
        return $this->getInput($prompt, $default, new InputValidator(function ($val) {
            return InputValidator::isInt($val);
        }, 'Provided value is not an integer!'));
    }
    /**
     * Reads one line from input stream.
     * 
     * The method will continue to read from input stream till it finds end of 
     * line character "\n".
     * 
     * @return string The method will return the string which was taken from 
     * input stream without the end of line character.
     * 
     */
    public function readln() : string {
        return $this->getInputStream()->readLine();
    }

    /**
     * Reads a string that represents class namespace.
     *
     * @param string $prompt The string that will be shown to the user. The
     * string must be non-empty.
     *
     * @param string $defaultNs A default string that represents default namespace.
     * Note that the method will throw an exception if this parameter does not
     * represent a valid namespace.
     *
     * @param string $errMsg A string that will be shown if provided input does
     * not represent a valid namespace.
     *
     * @return string The method will return a string that represent a valid namespace.
     *
     * @throws IOException If given default namespace does not represent a namespace.
     */
    public function readNamespace(string $prompt, ?string $defaultNs = null, string $errMsg = 'Invalid Namespace!') {
        if ($defaultNs !== null && !InputValidator::isValidNamespace($defaultNs)) {
            throw new IOException('Provided default namespace is not valid.');
        }

        return $this->getInput($prompt, $defaultNs, new InputValidator(function ($input) {
            if (InputValidator::isValidNamespace($input)) {
                return true;
            }

            return false;
        }, $errMsg));
    }
    /**
     * Removes an argument from the command given its name.
     * 
     * @param string $name The name of the argument that will be removed.
     * 
     * @return bool If removed, true is returned. Other than that, false is
     * returned.
     */
    public function removeArgument(string $name) : bool {
        $removed = false;
        $temp = [];

        foreach ($this->getArgs() as $arg) {
            if ($arg->getName() !== $name) {
                $temp[] = $arg;
            } else {
                $removed = true;
            }
        }
        $this->commandArgs = $temp;

        return $removed;
    }

    /**
     * Ask the user to select one of multiple values.
     * 
     * This method will display a prompt and wait for the user to select 
     * a value from a set of values. If the user give something other than the listed values,
     * it will show an error and ask him to select again. The
     * user can select an answer by typing its text or its number which will appear 
     * in the terminal.
     * 
     * @param string $prompt The text that will be shown for the user.
     * 
     * @param array $choices An indexed array of values to select from.
     * 
     * @param int $defaultIndex The index of the default value in case no value 
     * is selected and the user hit enter.
     * 
     * @return string|null The method will return the value which is selected by
     * the user. If choices array is empty, null is returned.
     * 
     */
    public function select(string $prompt, array $choices, int $defaultIndex = -1) {
        if (count($choices) != 0) {
            do {
                $this->println($prompt, [
                    'color' => 'gray',
                    'bold' => true
                ]);

                $this->printChoices($choices, $defaultIndex);
                $input = trim($this->readln());

                $check = $this->checkSelectedChoice($choices, $defaultIndex, $input);

                if ($check !== null) {
                    return $check;
                }
            } while (true);
        }

        return null;
    }
    /**
     * Sets the value of an argument.
     * 
     * This method is useful in writing test cases for the commands.
     * 
     * @param string $argName The name of the argument.
     * 
     * @param string $argValue The value to set.
     * 
     * @return bool If the value of the argument is set, the method will return
     * true. If not set, the method will return false. The value of the attribute 
     * will be not set in the following cases:
     * <ul>
     * <li>If the argument can have a specific set of values and the given 
     * value is not one of them.</li>
     * <li>The given value is empty string or null.</li>
     * </u>
     * 
     */
    public function setArgValue(string $argName, string $argValue = ''): bool {
        $trimmedArgName = trim($argName);
        $argObj = $this->getArg($trimmedArgName);

        if ($argObj !== null) {
            return $argObj->setValue($argValue);
        }

        return false;
    }
    /**
     * Sets the description of the command.
     * 
     * The description of the command is a string that describes what does the 
     * command do, and it will appear in CLI if the command 'help' is executed.
     * 
     * @param string $str A string that describes the command. It must be non-empty 
     * string.
     * 
     * @return bool If the description of the command is set, the method will return
     * true. Other than that, the method will return false.
     */
    public function setDescription(string $str) : bool {
        $trimmed = trim($str);

        if (strlen($trimmed) > 0) {
            $this->description = $trimmed;

            return true;
        }

        return false;
    }
    /**
     * Sets the stream at which the command will read input from.
     * 
     * @param InputStream $stream An instance that implements an input stream.
     * 
     */
    public function setInputStream(InputStream $stream) {
        $this->inputStream = $stream;
    }
    /**
     * Sets the name of the command.
     * 
     * The name of the command is a string which is used to call the command 
     * from CLI.
     * 
     * @param string $name The name of the command (such as 'v' or 'help'). 
     * It must be non-empty string and does not contain spaces.
     * 
     * @return bool If the name of the command is set, the method will return
     * true. Other than that, the method will return false.
     * 
     */
    public function setName(string $name) : bool {
        $trimmed = trim($name);

        if (strlen($trimmed) > 0 && !strpos($trimmed, ' ')) {
            $this->commandName = $name;

            return true;
        }

        return false;
    }
    /**
     * Sets the stream at which the command will send output to.
     * 
     * @param OutputStream $stream An instance that implements output stream.
     * 
     */
    public function setOutputStream(OutputStream $stream) {
        $this->outputStream = $stream;
    }
    /**
     * Sets the runner that owns the command.
     * 
     * The runner is the instance that will execute the command.
     * 
     * @param Runner $owner
     */
    public function setOwner(?Runner $owner = null) {
        $this->owner = $owner;
    }
    /**
     * Display a message that represents a success status.
     * 
     * The message will be prefixed with the string "Success:" in green. 
     * 
     * @param string $message The message that will be displayed.
     * 
     */
    public function success(string $message) {
        $this->printMsg($message, 'Success', 'light-green');
    }

    /**
     * Creates and displays a table with the given data.
     * 
     * This method provides a convenient way to display tabular data in CLI applications
     * using the WebFiori CLI Table feature. It supports various table styles, themes,
     * column configuration, and data formatting options.
     * 
     * @param array $data The data to display. Can be:
     *                   - Array of arrays (indexed): [['John', 30], ['Jane', 25]]
     *                   - Array of associative arrays: [['name' => 'John', 'age' => 30]]
     * @param array $headers Optional headers for the table columns. If not provided
     *                      and data contains associative arrays, keys will be used as headers.
     * @param array $options Optional configuration options. Use TableOptions constants for keys:
     *                      - TableOptions::STYLE: Table style ('bordered', 'simple', 'minimal', 'compact', 'markdown')
     *                      - TableOptions::THEME: Color theme ('default', 'dark', 'light', 'colorful', 'professional', 'minimal')
     *                      - TableOptions::TITLE: Table title to display above the table
     *                      - TableOptions::WIDTH: Maximum table width (auto-detected if not specified)
     *                      - TableOptions::SHOW_HEADERS: Whether to show column headers (default: true)
     *                      - TableOptions::COLUMNS: Column-specific configuration
     *                      - TableOptions::COLORIZE: Column colorization rules
     *                      - TableOptions::AUTO_WIDTH: Auto-calculate column widths (default: true)
     *                      - TableOptions::SHOW_ROW_SEPARATORS: Show separators between rows (default: false)
     *                      - TableOptions::SHOW_HEADER_SEPARATOR: Show separator after headers (default: true)
     *                      - TableOptions::PADDING: Cell padding configuration
     *                      - TableOptions::WORD_WRAP: Enable word wrapping (default: false)
     *                      - TableOptions::ELLIPSIS: Truncation string (default: '...')
     *                      - TableOptions::SORT: Sort configuration
     *                      - TableOptions::LIMIT: Limit number of rows displayed
     *                      - TableOptions::FILTER: Filter function for rows
     * 
     * @return Command Returns the same instance for method chaining.
     * 
     * 
     * Example usage:
     * ```php
     * use WebFiori\Cli\Table\TableOptions;
     * 
     * // Basic table
     * $this->table([
     *     ['John Doe', 30, 'Active'],
     *     ['Jane Smith', 25, 'Inactive']
     * ], ['Name', 'Age', 'Status']);
     * 
     * // Advanced table with constants
     * $this->table($users, ['Name', 'Status', 'Balance'], [
     *     TableOptions::STYLE => 'bordered',
     *     TableOptions::THEME => 'colorful',
     *     TableOptions::TITLE => 'User Management',
     *     TableOptions::COLUMNS => [
     *         'Balance' => ['align' => 'right', 'formatter' => fn($v) => '$' . number_format($v, 2)]
     *     ],
     *     TableOptions::COLORIZE => [
     *         'Status' => fn($v) => match($v) {
     *             'Active' => ['color' => 'green', 'bold' => true],
     *             'Inactive' => ['color' => 'red'],
     *             default => []
     *         }
     *     ]
     * ]);
     * ```
     */
    public function table(array $data, array $headers = [], array $options = []): Command {
        // Handle empty data
        if (empty($data)) {
            $this->info('No data to display in table.');

            return $this;
        }

        try {
            // Create table builder instance
            $tableBuilder = TableBuilder::create();

            // Set headers
            if (!empty($headers)) {
                $tableBuilder->setHeaders($headers);
            }

            // Set data
            $tableBuilder->setData($data);

            // Apply style (support both constant and string)
            $style = $options[TableOptions::STYLE] ?? $options['style'] ?? 'bordered';
            $tableBuilder->useStyle($style);

            // Apply theme (support both constant and string)
            $theme = $options[TableOptions::THEME] ?? $options['theme'] ?? null;

            if ($theme !== null) {
                $themeObj = TableTheme::create($theme);
                $tableBuilder->setTheme($themeObj);
            }

            // Set title (support both constant and string)
            $title = $options[TableOptions::TITLE] ?? $options['title'] ?? null;

            if ($title !== null) {
                $tableBuilder->setTitle($title);
            }

            // Set width (support both constant and string)
            $width = $options[TableOptions::WIDTH] ?? $options['width'] ?? $this->getTerminalWidth();
            $tableBuilder->setMaxWidth($width);

            // Configure headers visibility (support both constant and string)
            $showHeaders = $options[TableOptions::SHOW_HEADERS] ?? $options['showHeaders'] ?? true;
            $tableBuilder->showHeaders($showHeaders);

            // Configure columns (support both constant and string)
            $columns = $options[TableOptions::COLUMNS] ?? $options['columns'] ?? [];

            if (!empty($columns) && is_array($columns)) {
                foreach ($columns as $columnName => $columnConfig) {
                    $tableBuilder->configureColumn($columnName, $columnConfig);
                }
            }

            // Apply colorization (support both constant and string)
            $colorize = $options[TableOptions::COLORIZE] ?? $options['colorize'] ?? [];

            if (!empty($colorize) && is_array($colorize)) {
                foreach ($colorize as $columnName => $colorizer) {
                    if (is_callable($colorizer)) {
                        $tableBuilder->colorizeColumn($columnName, $colorizer);
                    }
                }
            }

            // Render and display the table
            $output = $tableBuilder->render();
            $this->prints($output);
        } catch (Exception $e) {
            $this->error('Failed to display table: '.$e->getMessage());
        } catch (Error $e) {
            $this->error('Table display error: '.$e->getMessage());
        }

        return $this;
    }
    /**
     * Display a message that represents a warning.
     * 
     * The message will be prefixed with the string 'Warning:' in 
     * red.
     * 
     * @param string $message The message that will be shown.
     * 
     */
    public function warning(string $message) {
        $this->prints('Warning: ', [
            'color' => 'light-yellow',
            'bold' => true
        ]);
        $this->println($message);
    }

    /**
     * Executes a callback for each item with a progress bar.
     * 
     * @param iterable $items Items to iterate over
     * @param callable $callback Callback to execute for each item
     * @param string $message Optional message to display
     * @return void
     */
    public function withProgressBar(iterable $items, callable $callback, string $message = ''): void {
        $items = is_array($items) ? $items : iterator_to_array($items);
        $total = count($items);

        $progressBar = $this->createProgressBar($total);
        $progressBar->start($message);

        foreach ($items as $key => $item) {
            $callback($item, $key);
            $progressBar->advance();
        }

        $progressBar->finish();
    }

    private function _createPassArray($string, array $args) : array {
        $retVal = [$string];

        foreach ($args as $arg) {
            if (gettype($arg) != 'array') {
                $retVal[] = $arg;
            }
        }

        return $retVal;
    }
    private function checkIsArgsSetHelper() {
        $missingMandatory = [];

        foreach ($this->commandArgs as $argObj) {
            if (!$argObj->isOptional() && $argObj->getValue() === null && $argObj->getDefault() != '') {
                $argObj->setValue($argObj->getDefault());
            } else if (!$argObj->isOptional() && $argObj->getValue() === null) {
                $missingMandatory[] = $argObj->getName();
            }
        }

        if (count($missingMandatory) != 0) {
            $missingStr = 'The following required argument(s) are missing: ';
            $comma = '';

            foreach ($missingMandatory as $opt) {
                $missingStr .= $comma."'".$opt."'";
                $comma = ', ';
            }
            $this->error($missingStr);

            return false;
        }

        return true;
    }
    private function checkSelectedChoice($choices, $defaultIndex, $input) {
        $retVal = null;

        if (in_array($input, $choices)) {
            //Given input is exactly same as one of choices
            $retVal = $input;
        } else if (strlen($input) == 0 && $defaultIndex !== null) {
            //Given input is empty string (enter hit). 
            //Return default if specified.
            $retVal = $this->getDefaultChoiceHelper($choices, $defaultIndex);
        } else if (InputValidator::isInt($input)) {
            //Selected option is an index. Search for it and return its value.
            $retVal = $this->getChoiceAtIndex($choices, $input);
        }

        if ($retVal === null) {
            $this->error('Invalid answer.');
        }

        return $retVal;
    }
    private function getChoiceAtIndex(array $choices, int $input) {
        $index = 0;

        foreach ($choices as $choice) {
            if ($index == $input) {
                return $choice;
            }
            $index++;
        }

        return null;
    }
    private function getDefaultChoiceHelper(array $choices, int $defaultIndex) {
        $index = 0;

        foreach ($choices as $choice) {
            if ($index == $defaultIndex) {
                return $choice;
            }
            $index++;
        }
    }

    /**
     * Validate user input and show error message if user input is invalid.
     * @param string $input
     * @param InputValidator|null $validator
     * @param string|null $default
     * @return array The method will return an array with two indices, 'valid' and
     * 'value'. The 'valid' index contains a boolean that is set to true if the
     * value is valid. The index 'value' will contain the passed value.
     */
    private function getInputHelper(string &$input, ?InputValidator $validator = null, ?string $default = null) : array {
        $retVal = [
            'valid' => true
        ];

        if (strlen($input) == 0 && $default !== null) {
            $input = $default;
        } else if ($validator !== null) {
            $retVal['valid'] = $validator->isValid($input);

            if (!($retVal['valid'] === true)) {
                $this->error($validator->getErrPrompt());
            }
        }
        $retVal['value'] = $input;

        return $retVal;
    }

    /**
     * Get terminal width for responsive table display.
     * 
     * @return int Terminal width in characters, defaults to 80 if unable to detect.
     */
    private function getTerminalWidth(): int {
        // Try to get terminal width using tput
        $width = @exec('tput cols 2>/dev/null');

        if (is_numeric($width) && $width > 0) {
            return (int)$width;
        }

        // Try environment variable
        $width = getenv('COLUMNS');

        if ($width !== false && is_numeric($width) && $width > 0) {
            return (int)$width;
        }

        // Try using stty
        $width = @exec('stty size 2>/dev/null | cut -d" " -f2');

        if (is_numeric($width) && $width > 0) {
            return (int)$width;
        }

        // Default fallback
        return 80;
    }
    private function parseArgsHelper() : bool {
        $options = $this->getArgs();
        $invalidArgsVals = [];

        foreach ($options as $argObj) {
            $val = $this->getArgValue($argObj->getName());

            if ($val !== null && !$argObj->setValue($val)) {
                $invalidArgsVals[] = $argObj->getName();
            }
        }

        if (count($invalidArgsVals) != 0) {
            $invalidStr = 'The following argument(s) have invalid values: ';
            $comma = '';

            foreach ($invalidArgsVals as $argName) {
                $invalidStr .= $comma."'".$argName."'";
                $comma = ', ';
            }
            $this->error($invalidStr);

            foreach ($invalidArgsVals as $argName) {
                $this->info("Allowed values for the argument '$argName':");

                foreach ($this->getArg($argName)->getAllowedValues() as $val) {
                    $this->println($val);
                }
            }

            return false;
        }

        return true;
    }
    private function printChoices($choices, $default) {
        $index = 0;

        foreach ($choices as $choiceTxt) {
            if ($default !== null && $index == $default) {
                $this->prints($index.": ".$choiceTxt, [
                    'color' => 'light-blue',
                    'bold' => 'true'
                ]);
                $this->println(' <--');
            } else {
                $this->println($index.": ".$choiceTxt);
            }
            $index++;
        }
    }
    private function printMsg(string $msg, string $prefix, string $color) {
        $this->prints("$prefix: ", [
            'color' => $color,
            'bold' => true,

        ]);
        $this->println($msg);
    }
}
