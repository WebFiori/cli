<?php
namespace webfiori\cli;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Throwable;
use webfiori\cli\streams\ArrayInputStream;
use webfiori\cli\streams\ArrayOutputStream;
use webfiori\cli\streams\InputStream;
use webfiori\cli\streams\OutputStream;
use webfiori\cli\streams\StdIn;
use webfiori\cli\streams\StdOut;
use webfiori\tests\cli\OutputFormatterTest;

/**
 * The core class which is used to manage command line related operations.
 *
 * @author Ibrahim
 */
class Runner {
    /**
     * The command that will be executed now.
     * 
     * @var CLICommand|null
     */
    private $activeCommand;
    private $beforeStartPool;
    private $commandExitVal;
    private $argsV;
    private $isAnsi;
    /**
     * An associative array that contains supported commands. 
     * 
     * @var array
     * 
     */
    private $commands;
    /**
     * 
     * @var CLICommand|null
     */
    private $defaultCommand;
    private $globalArgs;
    /**
     * 
     * @var InputStream
     * 
     */
    private $inputStream;
    /**
     * An attribute which is set to true if CLI is running in interactive mode 
     * or not.
     * 
     * @var bool
     */
    private $isInteractive;
    /**
     * 
     * @var OutputStream
     */
    private $outputStream;
    /**
     * Creates new instance of the class.
     */
    public function __construct() {
        $this->commands = [];
        $this->globalArgs = [];
        $this->argsV = [];
        $this->isInteractive = false;
        $this->isAnsi = false;
        $this->inputStream = new StdIn();
        $this->outputStream = new StdOut();
        $this->commandExitVal = 0;

        $this->addArg('--ansi', [
            'optional' => true,
            'description' => 'Force the use of ANSI output.'
        ]);
        $this->setBeforeStart(function (Runner $r) {
            if (count($r->getArgsVector()) == 0) {
                $r->setArgsVector($_SERVER['argv']);
            }
            $r->checkIsIntr();
        });
    }
    /**
     * Adds a global command argument.
     * 
     * An argument is a string that comes after the name of the command. The value 
     * of an argument can be set using equal sign. For example, if command name 
     * is 'do-it' and one argument has the name 'what-to-do', then the full 
     * CLI command would be "do-it what-to-do=say-hi". An argument can be 
     * also treated as an option.
     * 
     * @param string $name The name of the argument. It must be non-empty string 
     * and does not contain spaces.
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
     * @since 1.0
     */
    public function addArg(string $name, array $options = []) : bool {
        $toAdd = CommandArgument::create($name, $options);

        if ($toAdd === null) {
            return false;
        }

        return $this->addArgument($toAdd);
    }
    /**
     * Adds an argument to the set of global arguments.
     * 
     * Global arguments are set of arguments that will be added automatically
     * to any command which is registered by the runner.
     * 
     * @param CommandArgument $arg An object that holds argument info.
     * 
     * @return bool If the argument is added, the method will return true.
     * Other than that, false is returned.
     */
    public function addArgument(CommandArgument $arg) : bool {
        if (!$this->hasArg($arg->getName())) {
            $this->globalArgs[] = $arg;

            return true;
        }

        return false;
    }
    /**
     * Returns the command which is being executed.
     * 
     * @return CLICommand|null If a command is requested and currently in execute 
     * stage, the method will return it as an object. If 
     * no command is active, the method will return null.
     * 
     */
    public function getActiveCommand() {
        return $this->activeCommand;
    }
    /**
     * Returns an array that contains objects that represents global arguments.
     * 
     * @return array An array that contains objects that represents global arguments.
     */
    public function getArgs() : array {
        return $this->globalArgs;
    }
    /**
     * Returns an array that contains arguments vector values.
     * 
     * @return array Each index will have one part of arguments vector.
     */
    public function getArgsVector() : array {
        return $this->argsV;
    }
    /**
     * Returns a registered command given its name.
     * 
     * @param string $name The name of the command as specified when it was
     * initialized.
     * 
     * @return CLICommand|null If the command is registered, it is returned
     * as an object. Other than that, null is returned.
     */
    public function getCommandByName(string $name) {
        if (isset($this->getCommands()[$name])) {
            return $this->getCommands()[$name];
        }
    }
    /**
     * Returns an associative array of registered commands.
     * 
     * @return array The method will return an associative array. The keys of 
     * the array are the names of the commands and the value of the key is 
     * an object that holds command information.
     * 
     */
    public function getCommands() : array {
        return $this->commands;
    }
    /**
     * Return the command which will get executed in case no command name
     * was provided as argument.
     * 
     * @return CLICommand|null If set, it will be returned as object. Other
     * than that, null is returned.
     */
    public function getDefaultCommand() {
        return $this->defaultCommand;
    }
    /**
     * Returns the stream at which the engine is using to get inputs.
     * 
     * @return InputStream The default input stream is 'StdIn'.
     */
    public function getInputStream() : InputStream {
        return $this->inputStream;
    }
    /**
     * Returns an array that contain all generated output by executing a command.
     * 
     * This method should be only used when testing the execution process of a
     * command The method will return empty array if output stream type
     * is not ArrayOutputStream.
     * 
     * @return array An array that contains all output lines which are generated
     * by executing a specific command.
     */
    public function getOutput() : array {
        $outputStream = $this->getOutputStream();

        if ($outputStream instanceof ArrayOutputStream) {
            return $outputStream->getOutputArray();
        }

        return [];
    }
    /**
     * Returns the stream at which the engine is using to send outputs.
     * 
     * @return OutputStream The default input stream is 'StdOut'.
     */
    public function getOutputStream() : OutputStream {
        return $this->outputStream;
    }
    /**
     * Checks if the runner has specific global argument or not given its name.
     * 
     * @param string $name The name of the argument.
     * 
     * @return bool If the runner has such argument, true is returned. Other than
     * that, false is returned.
     */
    public function hasArg(string $name) : bool {
        foreach ($this->getArgs() as $argObj) {
            if ($argObj->getName() == $name) {
                return true;
            }
        }

        return false;
    }
    /**
     * Checks if the class is running through command line interface (CLI) or 
     * through a web server.
     * 
     * @return bool If the class is running through a command line,
     * the method will return true. False if not.
     * 
     */
    public static function isCLI() : bool {
        //best way to check if app is running through CLi
        // or in a web server.
        // Did a lot of research on that.
        return http_response_code() === false;
    }
    /**
     * Checks if CLI is running in interactive mode or not.
     * 
     * @return bool If CLI is running in interactive mode, the method will
     * return true. False otherwise.
     * 
     */
    public function isInteractive() : bool {
        return $this->isInteractive;
    }
    /**
     * Register new command.
     * 
     * @param CLICommand $cliCommand The command that will be registered.
     * 
     */
    public function register(CLICommand $cliCommand) {
        $this->commands[$cliCommand->getName()] = $cliCommand;
    }
    /**
     * Removes an argument from the global args set given its name.
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
        $this->globalArgs = $temp;

        return $removed;
    }

    /**
     * Reset input stream, output stream and, registered commands to default.
     */
    public function reset() {
        $this->inputStream = new StdIn();
        $this->outputStream = new StdOut();
        $this->commands = [];
    }

    /**
     * Returns exit status code of last executed command.
     *
     * @return int For success run, the method should return 0. Other than that,
     * it means the command was executed with an error.
     */
    public function getLastCommandExitStatus() : int {
        return $this->commandExitVal;
    }
    private function printMsg(string $msg, string $prefix = null, string $color = null) {
        if ($prefix !== null) {
            $prefix = Formatter::format($prefix, [
                'color' => $color,
                'bold' => true,
                'ansi' => $this->isAnsi
            ]);
            $this->getOutputStream()->prints("$prefix ");
        }
        if (strlen($msg) != 0) {
            $this->getOutputStream()->println($msg);
        }
    }
    /**
     * Executes a command given as object.
     * 
     * @param CLICommand $c The command that will be executed. If null is given,
     * the method will take command name from the array '$args'.
     * 
     * @param array $args An optional array that can hold command arguments.
     * The keys of the array should be arguments names and the value of each index
     * is the value of the argument. Note that if the first parameter of the
     * method is null, the first index of the array should hold
     * the name of the command that will be executed.
     *
     * @param bool $ansi If set to true, then the output will render with ANSI escape sequences.
     * 
     * @return int The method will return an integer that represents exit status of
     * running the command. Usually, if the command exit with a number other than 0,
     * it means that there was an error in execution.
     */
    public function runCommand(CLICommand $c = null, array $args = [], bool $ansi = false) : int {
        $commandName = null;

        if ($c === null) {
            if (count($args) == 0) {
                $c = $this->getDefaultCommand();
            } else {
                if (isset($args[0])) {
                    $commandName = filter_var($args[0], FILTER_DEFAULT);
                    
                    $c = $this->getCommandByName($commandName);
                } else {
                    $c = $this->getDefaultCommand();
                }
            }

            if ($c === null) {
                if ($commandName == null) {

                    $this->printMsg("No command was specified to run.", 'Info:', 'blue');
                    return 0;
                } else {
                    $this->printMsg("The command '".$commandName."' is not supported.", 'Error:', 'red');
                    $this->commandExitVal = -1;
                    return -1;
                }

            }
        }
        if ($ansi) {
            $args[] = '--ansi';
        }
        $this->setArgV($args);
        $this->setActiveCommand($c);
        $this->commandExitVal = $c->excCommand();
        $this->setActiveCommand();

        return $this->commandExitVal;
    }
    /**
     * Sets the command which is currently in execution stage.
     * 
     * This method is used internally by execution engine to set the command which
     * is being executed.
     * 
     * @param CLICommand $c The command which is in execution stage.
     */
    public function setActiveCommand(CLICommand $c = null) {
        if ($this->getActiveCommand() !== null) {
            $this->getActiveCommand()->setOwner();
        }
        $this->activeCommand = $c;

        if ($this->getActiveCommand() !== null) {
            $this->getActiveCommand()->setOutputStream($this->getOutputStream());
            $this->getActiveCommand()->setInputStream($this->getInputStream());
            $this->getActiveCommand()->setOwner($this);
        }
    }
    /**
     * Sets arguments vector to have specific value.
     * 
     * This method is mainly used to simulate running the class using an
     * actual terminal. Also, it can be used to setup the test run parameters
     * for testing a command.
     * 
     * @param array $argsVector An array that contains arguments vector. Usually,
     * the first argument of the vector is the entry point (such as app.php).
     * The second argument is the name of the command that will get executed
     * and, remaining parts are any additional arguments that the command
     * might use.
     */
    public function setArgsVector(array $argsVector) {
        $this->argsV = $argsVector;
    }
    /**
     * Sets a callable to call before start running CLI engine.
     * 
     * This can be used to register custom made commands before running
     * the engine.
     * 
     * @param callable $func An executable function. The function will have
     * one parameter which is the runner that the function will be added to.
     */
    public function setBeforeStart(callable $func) {
        $this->beforeStartPool[] = $func;
    }
    /**
     * Sets the default command that will be get executed in case no command
     * name was provided as argument.
     * 
     * @param string $commandName The name of the command that will be set as
     * default command. Note that it must be a registered command.
     */
    public function setDefaultCommand(string $commandName) {
        $c = $this->getCommandByName($commandName);

        if ($c !== null) {
            $this->defaultCommand = $c;
        }
    }
    /**
     * Sets an array as an input for running specific command.
     * 
     * This method is used to test the execution process of specific command.
     * The developer can use it to mimic the inputs which could be provided
     * by the user when actually running the command through a terminal.
     * The developer can use the method 'Runner::getOutput()' to get generated
     * output and compare it with expected output.
     * 
     * Note that this method will set the input stream to 'ArrayInputStream' 
     * and output stream to 'ArrayOutputStream'.
     * 
     * @param array $inputs An array that contain lines of inputs.
     */
    public function setInput(array $inputs = []) {
        $this->setInputStream(new ArrayInputStream($inputs));
        $this->setOutputStream(new ArrayOutputStream());
    }

    /**
     * Sets the stream at which the runner will be using to read inputs from.
     * 
     * @param InputStream $stream The new stream that will holds inputs.
     */
    public function setInputStream(InputStream $stream) {
        $this->inputStream = $stream;
    }
    /**
     * Sets the stream at which the runner will be using to send outputs to.
     * 
     * @param OutputStream $stream The new stream that will holds inputs.
     */
    public function setOutputStream(OutputStream $stream) {
        $this->outputStream = $stream;
    }
    /**
     * Start command line process.
     * 
     * @return int The method will return an integer that represents exit status of
     * the process. Usually, if the process exit with a number other than 0,
     * it means that there was an error in execution.
     */
    public function start() : int {
        foreach ($this->beforeStartPool as $func) {
            call_user_func_array($func, [$this]);
        }
        if ($this->isInteractive()) {
            $this->isAnsi = in_array('--ansi', $this->getArgsVector());
            $this->printMsg('Running in interactive mode.', '>>', 'blue');
            $this->printMsg("Type command name or 'exit' to close.", ">>", 'blue');
            $this->printMsg('', '>>', 'blue');

            while (true) {
                $args = $this->readInteractive();
                $this->setArgsVector($args);
                $argsCount = count($args);

                if ($argsCount == 0) {
                    $this->getOutputStream()->println('No input.');
                } else if ($args[0] == 'exit') {
                    return 0;
                } else {
                    try {
                        $this->runCommand(null, $args, $this->isAnsi);
                    } catch (Throwable $ex) {
                        $this->printMsg('An exception was thrown.', 'Error:', 'red');
                        $this->printMsg($ex->getMessage(), 'Exception Message:', 'yellow');
                        $this->printMsg($ex->getFile(), 'At:', 'yellow');
                        $this->printMsg($ex->getLine(), 'Line:', 'yellow');
                    }
                }
                $this->printMsg('', '>>', 'blue');
            }
        } else {
            return $this->run();
        }
    }
    private function checkIsIntr() {
        foreach ($this->getArgsVector() as $arg) {
            $this->isInteractive = $arg == '-i' || $this->isInteractive;
        }
    }

    private function readInteractive() {
        $input = trim($this->getInputStream()->readLine());

        $argsArr = strlen($input) != 0 ? explode(' ', $input) : [];
        if (in_array('--ansi', $argsArr)) {
            return array_diff($argsArr, ['--ansi']);
        }
        return $argsArr;
    }
    /**
     * Run the command line as single run.
     *
     * @return type
     */
    private function run() {
        $argsArr = array_slice($this->getArgsVector(), 1);

        if (in_array('--ansi', $argsArr)) {
            $this->isAnsi = true;
            $tempArgs = [];

            foreach ($argsArr as $argName => $val) {
                if (gettype($argName) == 'integer') {
                    if ($val != '--ansi') {
                        $tempArgs[] = $val;
                    }
                } else {
                    $tempArgs[$argName] = $val;
                }
            }
            $argsArr = $tempArgs;
        }
        if (count($argsArr) == 0) {
            $command = $this->getDefaultCommand();
            return $this->runCommand($command, [], $this->isAnsi);
        }

        return $this->runCommand(null, $argsArr, $this->isAnsi);
    }
    private function setArgV(array $args) {
        $argV = [];

        foreach ($args as $argName => $argVal) {
            if (gettype($argName) == 'integer') {
                $argV[] = $argVal;
            } else {
                $argV[] = $argName.'='.$argVal;
            }
        }
        $this->argsV = $argV;
    }
}
