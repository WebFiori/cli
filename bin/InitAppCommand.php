<?php

use webfiori\cli\Argument;
use webfiori\cli\CLICommand;
use webfiori\file\File;
/**
 * A class which is used to initialize a new CLI application.
 *
 * @author Ibrahim
 */
class InitAppCommand extends CLICommand {
    public function __construct() {
        parent::__construct('init', [
            new Argument('--dir', 'The name of application root directory.'),
            new Argument('--entry', 'The name of entry point that is used to execute the application. Default value is application directory name.', true)
        ], 'Initialize new CLI application.');
    }
    public function exec(): int {
        $dirName = $this->getArgValue('--dir');
        $entry = $this->getArgValue('--entry');

        if ($entry === null) {
            $entry = $dirName;
        }


        if (defined('ROOT_DIR')) {
            $appPath = ROOT_DIR.DIRECTORY_SEPARATOR.$dirName;
        } else {
            $appPath = substr(__DIR__, 0, strlen(__DIR__) - strlen('vendor\webfiori\cli\bin')).$dirName;
        }

        try {
            $this->println('Creating new app at"'.$appPath.'" ...');
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
    private function createAppClass(string $appPath, string $dirName) {
        $this->println('Creating "'.$dirName.'/main.php"...');
        $file = new File($appPath.DIRECTORY_SEPARATOR.'main.php');

        if (!$file->isExist()) {
            $file->append("<?php\n\n");
            $file->append("namespace $dirName;\n\n");
            $file->append("//Entry point of your application.\n\n");
            $file->append("require '../vendor/autoload.php';\n\n");
            $file->append("use webfiori\cli\Runner;\n");
            $file->append("use webfiori\cli\commands\HelpCommand;\n\n");


            $file->append("\$runner = new Runner();\n");
            $file->append("//TODO: Register Commands.\n");
            $file->append("\$runner->register(new HelpCommand());\n");
            $file->append("\$runner->setDefaultCommand('help');\n\n");
            $file->append("//Start your application.\n");
            $file->append("exit(\$runner->start());\n\n");
            $file->create(true);
            $file->write(false);

            return true;
        }
        $this->warning('File main.php already exist!');
    }
    private function createEntryPoint(string $appPath, string $dir, string $eName) {
        $this->println('Creating "'.$dir.'/'.$eName.'"...');
        $file = new File($eName, $appPath);

        if (!$file->isExist()) {
            $data = "#!/usr/bin/env php\n"
                    ."<?php\n"
                    ."require \"main.php\";\n\n";
            $file->create(true);
            file_put_contents($file->getDir().DIRECTORY_SEPARATOR.$eName, $data);

            return true;
        }
        $this->warning('File '.$eName.' already exist!');
    }
}
