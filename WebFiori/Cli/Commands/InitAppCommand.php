<?php
declare(strict_types=1);
namespace WebFiori\Cli\Commands;

use WebFiori\Cli\Argument;
use WebFiori\Cli\ArgumentOption;
use WebFiori\Cli\Command;
use WebFiori\File\File;
/**
 * A class which is used to initialize a new CLI application.
 *
 * @author Ibrahim
 */
class InitAppCommand extends Command {
    public function __construct() {
        parent::__construct('init', [
            new Argument('--dir', 'The name of application root directory.'),
            '--entry' => [
                ArgumentOption::DESCRIPTION => 'The name of entry point that is used to execute the application.',
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => 'main'
            ],
        ], 'Initialize new CLI application.');
    }
    public function exec(): int {
        $dirName = $this->getArgValue('--dir');
        $entry = $this->getArgValue('--entry');

        if ($entry === null) {
            $entry = 'main';
        }

        if (defined('ROOT_DIR')) {
            $appPath = ROOT_DIR.DIRECTORY_SEPARATOR.$dirName;
        } else {
            $appPath = substr(__DIR__, 0, strlen(__DIR__) - strlen('vendor\WebFiori\Cli\bin')).$dirName;
        }

        try {
            $this->println('Creating new app at "'.$appPath.'" ...');
            $this->createAppClass($appPath, $dirName);
            $this->createEntryPoint($appPath, $dirName, $entry);
            $this->createSampleCommand($appPath, $dirName);
            $this->success('App created successfully.');

            return 0;
        } catch (\Exception $ex) {
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
            $file->append("use WebFiori\\Cli\\Runner;\n");

            $file->append("\$runner = new Runner();\n\n");
            $file->append("//TODO: Register Commands.\n");
            $file->append("\$runner->register(new HelloCommand());\n\n");
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
    private function createSampleCommand(string $appPath, string $dirName) {
        $this->println('Creating "'.$dirName.'/HelloCommand.php"...');
        $file = new File($appPath.DIRECTORY_SEPARATOR.'HelloCommand.php');

        if (!$file->isExist()) {
            $file->append("<?php\n\n");
            $file->append("namespace $dirName;\n\n");
            $file->append("use WebFiori\\Cli\\Command;\n");
            $file->append("use WebFiori\\Cli\\ArgumentOption;\n\n");
            $file->append("class HelloCommand extends Command {\n");
            $file->append("    public function __construct() {\n");
            $file->append("        parent::__construct('hello', [\n");
            $file->append("            '--my-name' => [\n");
            $file->append("                ArgumentOption::OPTIONAL => true,\n");
            $file->append("                ArgumentOption::DESCRIPTION => 'Your name to greet'\n");
            $file->append("            ]\n");
            $file->append("        ], 'A sample hello command');\n");
            $file->append("    }\n\n");
            $file->append("    public function exec(): int {\n");
            $file->append("        \$name = \$this->getArgValue('--my-name');\n");
            $file->append("        if (\$name !== null) {\n");
            $file->append("            \$this->println('Hello %s', \$name);\n");
            $file->append("        } else {\n");
            $file->append("            \$this->println('Hello from WebFiori CLI!');\n");
            $file->append("        }\n");
            $file->append("        return 0;\n");
            $file->append("    }\n");
            $file->append("}\n");
            $file->create(true);
            $file->write(false);
        } else {
            $this->warning('File HelloCommand.php already exist!');
        }
    }
}
