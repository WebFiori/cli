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
            new CommandArgument('--dir', 'The name of application root directory.'),
            new CommandArgument('--entry', 'The name of entry point that is used to execute the application. Default value is application directory name.', true)
        ], 'Initialize new CLI application.');

    }
    public function exec(): int {
        $dirName = $this->getArgValue('--dir');
        $entry = $this->getArgValue('--entry');
        if ($entry === null) {
            $entry = $dirName;
        }
        $appPath = substr(__DIR__, 0, strlen(__DIR__) - strlen('vendor\webfiori\cli\bin')).$dirName;
        $this->createAppClass($appPath, $dirName);
        $this->createEntryPoint($appPath, $dirName, $entry);
        $this->success('App created successfully.');
        return 0;
    }
    private function createEntryPoint(string $appPath, string $dir, string $eName) {
        $this->success('Creating "'.$dir.'/'.$eName.'"...');
        $file = new File($dir, $appPath);
        $file->append("#!/usr/bin/env php\n"
                . "<?php\n"
                . "require \"app.php\";\n\n");
        $file->write();
    }
    private function createAppClass(string $appPath, string $dirName) {
        $this->println('Creating "'.$dirName.'/app.php" ...');
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
        
    }
}
