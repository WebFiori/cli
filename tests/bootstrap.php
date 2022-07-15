<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
$testsDirName = 'tests';
$rootDir = substr(__DIR__, 0, strlen(__DIR__) - strlen($testsDirName));
$DS = DIRECTORY_SEPARATOR;
$rootDirTrimmed = trim($rootDir,'/\\');
//echo 'Include Path: \''.get_include_path().'\''."\n";

if (explode($DS, $rootDirTrimmed)[0] == 'home') {
    //linux.
    $rootDir = $DS.$rootDirTrimmed.$DS;
} else {
    $rootDir = $rootDirTrimmed.$DS;
}
define('ROOT_DIR', $rootDir);
define('DS', DIRECTORY_SEPARATOR);
//echo 'Root Directory: \''.$rootDir.'\'.'."\n";
$jsonLibPath = $rootDir.'vendor'.DS.'webfiori'.DS.'jsonx'.DS.'webfiori'.DS.'json';
require_once $jsonLibPath.DS.'JsonI.php';
require_once $jsonLibPath.DS.'Json.php';
require_once $jsonLibPath.DS.'JsonConverter.php';
require_once $jsonLibPath.DS.'CaseConverter.php';
require_once $jsonLibPath.DS.'JsonTypes.php';
require_once $jsonLibPath.DS.'Property.php';

$fileLibPath = $rootDir.'vendor'.DS.'webfiori'.DS.'file'.DS.'webfiori'.DS.'file';
require_once $fileLibPath.DS.'File.php';
require_once $fileLibPath.DS.'MIME.php';
require_once $fileLibPath.DS.'exceptions'.DS.'FileException.php';

$baseDir = $rootDir.'webfiori'.DS.'cli';
require_once $baseDir.DS.'streams'.DS.'InputStream.php';
require_once $baseDir.DS.'streams'.DS.'OutputStream.php';
require_once $baseDir.DS.'CLICommand.php';
require_once $baseDir.DS.'CommandArgument.php';
require_once $baseDir.DS.'Formatter.php';
require_once $baseDir.DS.'KeysMap.php';
require_once $baseDir.DS.'Runner.php';
require_once $baseDir.DS.'InputValidator.php';
require_once $baseDir.DS.'streams'.DS.'ArrayInputStream.php';
require_once $baseDir.DS.'streams'.DS.'ArrayOutputStream.php';
require_once $baseDir.DS.'streams'.DS.'FileInputStream.php';
require_once $baseDir.DS.'streams'.DS.'FileOutputStream.php';
require_once $baseDir.DS.'streams'.DS.'StdIn.php';
require_once $baseDir.DS.'streams'.DS.'StdOut.php';
require_once $baseDir.DS.'exceptions'.DS.'IOException.php';
require_once $baseDir.DS.'commands'.DS.'HelpCommand.php';

require_once $rootDir.'/tests/webfiori/tests/cli/TestCommand.php';
require_once $rootDir.'/tests/webfiori/tests/cli/testCommands/Command00.php';
require_once $rootDir.'/tests/webfiori/tests/cli/testCommands/Command01.php';
require_once $rootDir.'/tests/webfiori/tests/cli/testCommands/WithExceptionCommand.php';


