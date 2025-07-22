<?php
namespace WebFiori\Cli;

use Throwable;
use WebFiori\Cli\Streams\ArrayInputStream;
use WebFiori\Cli\Streams\ArrayOutputStream;
use WebFiori\Cli\Streams\InputStream;
use WebFiori\Cli\Streams\OutputStream;
use WebFiori\Cli\Streams\StdIn;
use WebFiori\Cli\Streams\StdOut;

/**
 * The core class which is used to manage command line related operations.
 *
 * @author Ibrahim
 */
class Runner {
    /**
     * The command that will be executed now.
     * 
     * @var CliCommand
     * 
     * @since 1.0
     */
    private $activeCommand;
    /**
     * An array that holds the arguments of the command.
     * 
     * @var array
     * 
     * @since 1.0
     */
    private $args;
    /**
     * An array that holds the commands which are registered with the runner.
     * 
     * @var array
     * 
     * @since 1.0
     */
    private $commands;
    /**
     * The name of the command that will be executed if no command name was
     * provided.
     * 
     * @var string
     * 
     * @since 1.0
     */
    private $defaultCommand;
    /**
     * The stream that is used to read data from.
     * 
     * @var InputStream
     * 
     * @since 1.0
     */
    private $inputStream;
    /**
     * A boolean which is set to true if the runner is running in interactive mode.
     * 
     * @var bool
     * 
     * @since 1.0
     */
    private $isInteractive;
    /**
     * A boolean which is set to true if the runner is running in verbose mode.
     * 
     * @var bool
     * 
     * @since 1.0
     */
    private $isVerbose;
    /**
     * The stream at which the output will be sent to.
     * 
     * @var OutputStream
     * 
     * @since 1.0
     */
    private $outputStream;
    /**
     * Creates new instance of the class.
     * 
     * @since 1.0
     */
    public function __construct() {
        $this->commands = [];
        $this->args = [];
        $this->defaultCommand = '';
        $this->isInteractive = false;
        $this->isVerbose = false;
        $this->inputStream = new StdIn();
        $this->outputStream = new StdOut();
    }
    /**
     * Returns the name of the command that will be executed if no command name was
     * provided.
     * 
     * @return string The name of the command that will be executed if no command name was
     * provided. Default return value is empty string.
     * 
     * @since 1.0
     */
    public function getDefaultCommand() : string {
        return $this->defaultCommand;
    }
    /**
     * Returns an array that contains the arguments which are passed to the 
     * runner.
     * 
     * @return array An array that contains the arguments which are passed to the 
     * runner. Default is empty array.
     * 
     * @since 1.0
     */
    public function getArgsVector() : array {
        return $this->args;
    }
    /**
     * Returns an array that contains the registered commands.
     * 
     * @return array An array that contains the registered commands. The 
     * indices of the array are the names of the commands.
     * 
     * @since 1.0
     */
    public function getCommands() : array {
        return $this->commands;
    }
    /**
     * Returns the stream at which the runner reads input from.
     * 
     * @return InputStream The stream at which the runner reads input from.
     * 
     * @since 1.0
     */
    public function getInputStream() : InputStream {
        return $this->inputStream;
    }
    /**
     * Returns the output of running specific command.
     * 
     * This method is only useful if the output stream is of type 
     * 'WebFiori\Cli\Streams\ArrayOutputStream'.
     * 
     * @return array An array that contains the output of running specific 
     * command. Each index will contain one line of the output.
     * 
     * @since 1.0
     */
    public function getOutput() : array {
        $stream = $this->getOutputStream();

        if ($stream instanceof ArrayOutputStream) {
            return $stream->getOutputArray();
        }

        return [];
    }
    /**
     * Returns the stream at which the runner sends output to.
     * 
     * @return OutputStream The stream at which the runner sends output to.
     * 
     * @since 1.0
     */
    public function getOutputStream() : OutputStream {
        return $this->outputStream;
    }
    /**
     * Checks if the runner is running in interactive mode or not.
     * 
     * @return bool If the runner is running in interactive mode, the method 
     * will return true. False if not.
     * 
     * @since 1.0
     */
    public function isInteractive() : bool {
        return $this->isInteractive;
    }
    /**
     * Checks if the runner is running in verbose mode or not.
     * 
     * @return bool If the runner is running in verbose mode, the method 
     * will return true. False if not.
     * 
     * @since 1.0
     */
    public function isVerbose() : bool {
        return $this->isVerbose;
    }
    /**
     * Print a message to the output stream.
     * 
     * @param string $message The message that will be printed.
     * 
     * @param mixed $_ One or more additional arguments that can be supplied to the 
     * method. The values of the arguments will be included in the message 
     * at the place of '%s'.
     * 
     * @since 1.0
     */
    public function print(string $message, ...$_) {
        $args = func_get_args();
        $stream = $this->getOutputStream();

        if (count($args) == 1) {
            $stream->print($message);
        } else {
            $stream->prints($message, ...array_slice($args, 1));
        }
    }
    /**
     * Print a message to the output stream and appends a line break at the end.
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
     * Register new command.
     * 
     * @param CliCommand $command The command that will be registered.
     * 
     * @return bool If the command is registered, the method will return true. 
     * If not, the method will return false.
     * 
     * @since 1.0
     */
    public function register(CliCommand $command) : bool {
        $name = $command->getName();

        if (strlen($name) != 0 && !$this->hasCommand($name)) {
            $command->setInputStream($this->getInputStream());
            $command->setOutputStream($this->getOutputStream());
            $command->setRunner($this);
            $this->commands[$name] = $command;

            return true;
        }

        return false;
    }
    /**
     * Reset the runner to its default settings.
     * 
     * This method will set the input stream to 'WebFiori\Cli\Streams\StdIn' and 
     * the output stream to 'WebFiori\Cli\Streams\StdOut'. Also, it will reset 
     * the arguments vector and the registered commands.
     * 
     * @since 1.0
     */
    public function reset() {
        $this->commands = [];
        $this->args = [];
        $this->defaultCommand = '';
        $this->isInteractive = false;
        $this->isVerbose = false;
        $this->inputStream = new StdIn();
        $this->outputStream = new StdOut();
    }
    /**
     * Sets the arguments vector.
     * 
     * @param array $args An array that contains the arguments vector. Default 
     * value is $_SERVER['argv'].
     * 
     * @since 1.0
     */
    public function setArgsVector(array $args = []) {
        if (count($args) != 0) {
            $this->args = $args;
        } else {
            $this->args = $_SERVER['argv'];
        }
    }
    /**
     * Sets the name of the command that will be executed if no command name was
     * provided.
     * 
     * @param string $name The name of the command.
     * 
     * @return bool If the name of the command is set, the method will return 
     * true. If not, the method will return false.
     * 
     * @since 1.0
     */
    public function setDefaultCommand(string $name) : bool {
        $trimmed = trim($name);

        if (strlen($trimmed) != 0 && $this->hasCommand($trimmed)) {
            $this->defaultCommand = $trimmed;

            return true;
        }

        return false;
    }
    /**
     * Sets the stream at which the runner will read input from.
     * 
     * @param InputStream $stream The stream that will be used to read user input.
     * 
     * @since 1.0
     */
    public function setInputStream(InputStream $stream) {
        $this->inputStream = $stream;

        foreach ($this->getCommands() as $command) {
            $command->setInputStream($stream);
        }
    }
    /**
     * Sets an array that contains the inputs which will be supplied to the 
     * command.
     * 
     * This method is used to test the execution of the command. It will set 
     * the input stream to 'WebFiori\Cli\Streams\ArrayInputStream' and the 
     * output stream to 'WebFiori\Cli\Streams\ArrayOutputStream'.
     * 
     * @param array $inputs An array that contains the inputs which will be 
     * supplied to the command.
     * 
     * @since 1.0
     */
    public function setInputs(array $inputs) {
        $this->setInputStream(new ArrayInputStream($inputs));
        $this->setOutputStream(new ArrayOutputStream());
    }
    /**
     * Sets the stream at which the runner will send output to.
     * 
     * @param OutputStream $stream The stream that will be used to send command 
     * output.
     * 
     * @since 1.0
     */
    public function setOutputStream(OutputStream $stream) {
        $this->outputStream = $stream;

        foreach ($this->getCommands() as $command) {
            $command->setOutputStream($stream);
        }
    }
    /**
     * Start the execution of the runner.
     * 
     * @return int The method will return 0 if the command executed without 
     * errors. Other than that, it will return a number that indicates the 
     * status of execution.
     * 
     * @since 1.0
     */
    public function start() : int {
        $this->_checkIsInteractive();
        $this->_checkIsVerbose();

        if ($this->isInteractive()) {
            return $this->_runInteractive();
        } else {
            return $this->_runCommand();
        }
    }
    /**
     * Checks if the runner has specific command or not.
     * 
     * @param string $name The name of the command.
     * 
     * @return bool If the command is registered with the runner, the method 
     * will return true. Other than that, the method will return false.
     * 
     * @since 1.0
     */
    public function hasCommand(string $name) : bool {
        $trimmed = trim($name);

        return isset($this->commands[$trimmed]);
    }
    /**
     * Returns the command which is being executed.
     * 
     * @return CliCommand|null If the command is set, the method will return 
     * it as an object. If not, the method will return null.
     * 
     * @since 1.0
     */
    public function getActiveCommand() {
        return $this->activeCommand;
    }
    /**
     * Returns the command which has the given name.
     * 
     * @param string $name The name of the command.
     * 
     * @return CliCommand|null If the command is registered with the runner, the 
     * method will return it as an object. If not, the method will return null.
     * 
     * @since 1.0
     */
    public function getCommand(string $name) {
        $trimmed = trim($name);

        if ($this->hasCommand($trimmed)) {
            return $this->commands[$trimmed];
        }

        return null;
    }
    private function _checkIsInteractive() {
        $this->isInteractive = Argument::extractValue('-i', $this) !== null;
    }
    private function _checkIsVerbose() {
        $this->isVerbose = Argument::extractValue('-v', $this) !== null;
    }
    private function _getCommandName() {
        $args = $this->getArgsVector();

        if (count($args) >= 2) {
            $potentialName = $args[1];

            if ($this->hasCommand($potentialName)) {
                return $potentialName;
            }
        }

        return $this->getDefaultCommand();
    }
    private function _runCommand() {
        $commandName = $this->_getCommandName();

        if (strlen($commandName) != 0) {
            $command = $this->getCommand($commandName);
            $this->activeCommand = $command;

            if ($command->validateArgs()) {
                try {
                    return $command->exec();
                } catch (Throwable $ex) {
                    $this->println("Error: %s", $ex);

                    return -1;
                }
            }

            return -1;
        } else {
            $this->println("Error: No command was specified to run.");

            return -1;
        }
    }
    private function _runInteractive() {
        $this->println(">> Running in interactive mode.");
        $this->println(">> Type command name or 'exit' to close.");
        $this->println(">>");
        $command = '';

        do {
            $command = trim($this->getInputStream()->readLine());

            if ($command == 'exit') {
                return 0;
            } else if ($this->hasCommand($command)) {
                $this->activeCommand = $this->getCommand($command);

                if ($this->activeCommand->validateArgs()) {
                    try {
                        $this->activeCommand->exec();
                    } catch (Throwable $ex) {
                        $this->println("Error: %s", $ex);
                    }
                }
            } else {
                $this->println("Error: Command '$command' is not supported.");
            }
        } while (true);
    }
}
