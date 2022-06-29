<?php
namespace webfiori\cli;

use webfiori\cli\CLICommand;
use webfiori\cli\streams\ArrayInputStream;
use webfiori\cli\streams\ArrayOutputStream;
use webfiori\cli\streams\StdIn;
use webfiori\cli\streams\StdOut;
use webfiori\cli\streams\InputStream;
use webfiori\cli\streams\OutputStream;
/**
 * The core class which is used to manage command line related operations.
 *
 * @author Ibrahim
 */
class Runner {
    /**
     * 
     * @var CLICommand|null
     */
    private $defaultCommand;
    private $commandExitVal;
    /**
     * 
     * @var InputStream
     * 
     */
    private $inputStream;
    /**
     * The command that will be executed now.
     * 
     * @var CLICommand|null
     */
    private $activeCommand;
    /**
     * An associative array that contains supported commands. 
     * 
     * @var array
     * 
     */
    private $commands;
    /**
     * An attribute which is set to true if CLI is running in interactive mode 
     * or not.
     * 
     * @var boolean
     */
    private $isInteractive;
    /**
     * 
     * @var OutputStream
     */
    private $outputStream;
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
     * Register new command.
     * 
     * @param CLICommand $cliCommand The command that will be registered.
     * 
     */
    public function register(CLICommand $cliCommand) {
        $this->commands[$cliCommand->getName()] = $cliCommand;
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
     * Creates new instance of the class.
     */
    public function __construct() {
        $this->commands = [];
        $this->isInteractive = false;
        $this->inputStream = new StdIn();
        $this->outputStream = new StdOut();
        if (self::isCLI()) {
            if (isset($_SERVER['argv'])) {
                foreach ($_SERVER['argv'] as $arg) {
                    $this->isInteractive = $arg == '-i' || $this->isInteractive;
                }
            }

            if (defined('CLI_HTTP_HOST')) {
                $host = CLI_HTTP_HOST;
            } else {
                $host = '127.0.0.1';
                define('CLI_HTTP_HOST', $host);
            }
            $_SERVER['HTTP_HOST'] = $host;
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            $_SERVER['DOCUMENT_ROOT'] = ROOT_DIR;
            $_SERVER['REQUEST_URI'] = '/';
            putenv('HTTP_HOST='.$host);
            putenv('REQUEST_URI=/');

            if (defined('USE_HTTP') && USE_HTTP === true) {
                $_SERVER['HTTPS'] = 'no';
            } else {
                $_SERVER['HTTPS'] = 'yes';
            }
            
        }
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
     * Checks if CLI is running in interactive mode or not.
     * 
     * @return boolean If CLI is running in interactive mode, the method will 
     * return true. False otherwise.
     * 
     */
    public function isIntaractive() : bool {
        return $this->isInteractive;
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
     * Returns the stream at which the engine is using to send outputs.
     * 
     * @return OutputStream The default input stream is 'StdOut'.
     */
    public function getOutputStream() : OutputStream {
        return $this->outputStream;
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
     * @return int The method will return an integer that represents exit status of
     * running the command. Usually, if the command exit with a number other than 0,
     * it means that there was an error in execution.
     */
    public function runCommand(CLICommand $c = null, array $args = []) {
        $commandName = null;
        
        if ($c === null) {
            if (count($args) === 0) {
                $c = $this->getDefaultCommand();
            } else {
                if (isset($args[0])) {
                    $commandName = filter_var($args[0], FILTER_DEFAULT);
                    $args = array_slice($args, 1);
                    $c = $this->getCommandByName($commandName);
                } else {
                    $c = $this->getDefaultCommand();
                }
            }
            
            if ($c === null) {
                if ($commandName == null) {
                    $this->getOutputStream()->println("Info: No command was specified to run.");
                } else {
                    $this->getOutputStream()->println("Error: The command '".$commandName."' is not supported.");
                }

                return -1;
            }
        }
        $this->setArgV($args);
        $this->setActiveCommand($c);
        $this->commandExitVal = $c->excCommand();
        $this->setActiveCommand();
        return $this->commandExitVal;
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
        $_SERVER['argv'] = $argV;
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
    private function readInteractiv() {
        $input = trim($this->getInputStream()->readLine());
        return strlen($input) != 0 ? explode(' ', $input) : [];
    }
    /**
     * Start command line process.
     * 
     * @return int The method will return an integer that represents exit status of
     * the process. Usually, if the process exit with a number other than 0,
     * it means that there was an error in execution.
     */
    public static function start() : int {
        if ($this->isIntaractive()) {
            $this->getOutputStream()->println('Running CLI in interactive mode.');
            $this->getOutputStream()->println('WF-CLI > Type commant name or "exit" to close.');
            $this->getOutputStream()->prints('>>');
            $exit = false;

            while (!$exit) {
                $args = $this->readInteractiv();
                $argsCount = count($args);
                if ($argsCount == 0) {
                    $this->getOutputStream()->println('No input. Type "help" to display help.');
                } else {
                    if ($args[0] == 'exit') {
                        return 0;
                    }
                    $this->runCommand(null, $args);
                }
                $this->getOutputStream()->prints('>>');
            }
        } else {
            return $this->run();
        }
        return 0;
    }
    /**
     * Run the command line as single run.
     * 
     * @param type $args
     * @return type
     */
    private function run() {
        $argsArr = array_splice($_SERVER['argv'], 1);
        if (count($argsArr) == 0) {
            $command = $this->getDefaultCommand();

            if (!defined('__PHPUNIT_PHAR__') && $command !== null) {
                return $this->runCommand($command);
            }
        }

        return $this->runCommand(null, $argsArr);
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
            $this->getActiveCommand()->setOuner();
        }
        $this->activeCommand = $c;
        if ($this->getActiveCommand() !== null) {
            $this->getActiveCommand()->setOutputStream($this->getOutputStream());
            $this->getActiveCommand()->setInputStream($this->getInputStream());
            $this->getActiveCommand()->setOuner($this);
        }
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
     * Checks if the class is running through command line interface (CLI) or 
     * through a web server.
     * 
     * @return boolean If the class is running through a command line, 
     * the method will return true. False if not.
     * 
     */
    public static function isCLI() : bool {
        //best way to check if app is runing through CLi
        // or in a web server.
        // Did a lot of reaseach on that.
        return http_response_code() === false;
    }
}
