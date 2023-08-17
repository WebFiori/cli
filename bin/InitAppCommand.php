<?php

use webfiori\cli\CLICommand;
use webfiori\cli\CommandArgument;
use webfiori\file\File;
/**
 * A class which is used to initialize a new CLI application.
 *
 * @author Ibrahim
 */
class InitAppCommand extends CLICommand {
    public function __construct() {
        parent::__construct('init', [
            new CommandArgument('--dir', 'The name of application root directory.')
        ], 'Initialize new CLI application.');

    }
    public function exec(): int {
        $dirName = $this->getArgValue('--dir');
        $appPath = substr(__DIR__, 0, strlen(__DIR__) - strlen('vendor\webfiori\cli\bin')).$dirName;
        
        $file = new File($appPath.DIRECTORY_SEPARATOR.'app.php');
        $file->append("<?php\n\n");
        $file->append("namespace $dirName;\n\n");
        $file->append("//Entry point of your application.\n\n");
        $file->append("use webfiori\cli\Runner;\n");
        $file->append("use webfiori\cli\commands\HelpCommand;\n\n");
        
        
        $file->append("\$runner = new Runner();\n");
        $file->append("//TODO: Register Commands.\n");
        $file->append("\$runner->register(new HelpCommand());\n\n");
        $file->append("//Start your application.\n");
        $file->append("\$runner->start();\n\n");
        
        $file->write(false, true);
        $this->success('App created successfully.');
        return 0;
    }
}
