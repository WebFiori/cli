<?php
namespace webfiori\cli\commands;

use webfiori\cli\CLICommand;
use webfiori\cli\CommandArgument;

/**
 * A class that implements a basic help command.
 *
 * @author Ibrahim
 * @version 1.0
 */
class HelpCommand extends CLICommand {
    /**
     * Creates new instance of the class.
     * 
     * The command will have name 'help'. This command 
     * is used to display help information for the registered commands.
     * The command have one extra argument which is '--command-name'. If 
     * provided, the shown help will be specific to the selected command.
     */
    public function __construct() {
        parent::__construct('help', [
            '--command-name' => [
                'optional' => true,
                'description' => 'An optional command name. If provided, help '
                .'will be specific to the given command only.'
            ]
        ], 'Display CLI Help. To display help for specific command, use the argument '
                .'"--command-name" with this command.');
    }
    /**
     * Execute the command.
     * 
     * @since 1.0
     */
    public function exec() : int {
        $regCommands = $this->getOwner()->getCommands();
        $commandName = $this->getArgValue('--command-name');
        $len = $this->getMaxCommandNameLen();

        if ($commandName !== null) {
            if (isset($regCommands[$commandName])) {
                $this->printCommandInfo($regCommands[$commandName], $len, true);
            } else {
                $this->error("Command '$commandName' is not supported.");
            }
        } else {
            $formattingOptions = [
                'bold' => true,
                'color' => 'light-yellow'
            ];
            $this->println("Usage:", $formattingOptions);
            $this->println("    command [arg1 arg2=\"val\" arg3...]\n");
            $this->printGlobalArgs($formattingOptions);
            $this->println("Available Commands:", $formattingOptions);

            foreach ($regCommands as $commandObj) {
                $this->printCommandInfo($commandObj, $len);
            }
        }

        return 0;
    }
    private function getMaxCommandNameLen() : int {
        $len = 0;

        foreach ($this->getOwner()->getCommands() as $c) {
            $xLen = strlen($c->getName());

            if ($xLen > $len) {
                $len = $xLen;
            }
        }

        return $len;
    }
    private function printArg(CommandArgument $argObj, $spaces = 25) {
        $this->prints("    %".$spaces."s:", $argObj->getName(), [
            'bold' => true,
            'color' => 'yellow'
        ]);

        if ($argObj->isOptional()) {
            $this->prints("[Optional]");
        }

        if ($argObj->getDefault() != '') {
            $default = $argObj->getDefault();
            $this->prints("[Default = '$default']");
        }
        $this->println(" %s", $argObj->getDescription());
    }

    /**
     * Prints meta information of a specific command.
     *
     * @param CLICommand $cliCommand
     *
     * @param int $len
     *
     * @param bool $withArgs
     */
    private function printCommandInfo(CLICommand $cliCommand, int $len, bool $withArgs = false) {
        $this->prints("    %s", $cliCommand->getName(), [
            'color' => 'yellow',
            'bold' => true
        ]);
        $this->prints(': ');
        $spacesCount = $len - strlen($cliCommand->getName()) + 4;
        $this->println(str_repeat(' ', $spacesCount)."%s", $cliCommand->getDescription());

        if ($withArgs) {
            $args = $cliCommand->getArgs();

            if (count($args) != 0) {
                $this->println("    Supported Arguments:", [
                    'bold' => true,
                    'color' => 'light-blue'
                ]);

                foreach ($args as $argObj) {
                    $this->printArg($argObj);
                }
            }
        }
    }
    private function printGlobalArgs(array $formattingOptions) {
        $args = $this->getOwner()->getArgs();

        if (count($args) != 0) {
            $this->println("Global Arguments:", $formattingOptions);

            foreach ($args as $argObj) {
                $this->printArg($argObj, 4);
            }
        }
    }
}
