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
        
        if (strlen($appPath) == 0) {
            $appPath = $this->getDir();
        }
        
        try {
            $this->createAppClass($appPath, $dirName);
            $this->createEntryPoint($appPath, $dirName, $entry);
            $this->success('App created successfully.');
            return 0;
        } catch (Exception $ex) {
            $this->error('Unable to initialize due to an exception:');
            $this->println($ex->getCode().' - '.$ex->getMessage());
            return -1;
        }
    }
    private function getDir() {
        $split = explode(DIRECTORY_SEPARATOR, __DIR__);
        $retVal = '';
        for ($x = 0 ; $x < count($split) - 2 ; $x++) {
            $retVal .= $split[$x].DIRECTORY_SEPARATOR;
        }
        return $retVal;
    }
    private function createEntryPoint(string $appPath, string $dir, string $eName) {
        $this->println('Creating "'.$dir.'/'.$eName.'.sh"...');
        $file = new File($eName.'.sh', $appPath);
        if (!$file->isExist()) {
            $file->append("#!/usr/bin/env php\n");
            $file->append("<?php\n");
            $file->append("require \"app.php\";\n\n");
            $file->write(false, true);
            return true;
        }
        $this->warning('File '.$eName.'.sh already exist!');
    }
    private function createAppClass(string $appPath, string $dirName) {
        $this->println('Creating "'.$dirName.'/app.php"...');
        $file = new File($appPath.DIRECTORY_SEPARATOR.'app.php');
        if (!$file->isExist()) {
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
        $this->warning('File app.php already exist!');
    }
}
