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

$baseDir = $rootDir.'WebFiori'.DS.'Cli';
require_once $baseDir.DS.'Streams'.DS.'InputStream.php';
require_once $baseDir.DS.'Streams'.DS.'OutputStream.php';
require_once $baseDir.DS.'CliCommand.php';
require_once $baseDir.DS.'Argument.php';
require_once $baseDir.DS.'Formatter.php';
require_once $baseDir.DS.'KeysMap.php';
require_once $baseDir.DS.'Runner.php';
require_once $baseDir.DS.'Option.php';
require_once $baseDir.DS.'InputValidator.php';
require_once $baseDir.DS.'CommandTestCase.php';
require_once $baseDir.DS.'Streams'.DS.'ArrayInputStream.php';
require_once $baseDir.DS.'Streams'.DS.'ArrayOutputStream.php';
require_once $baseDir.DS.'Streams'.DS.'FileInputStream.php';
require_once $baseDir.DS.'Streams'.DS.'FileOutputStream.php';
require_once $baseDir.DS.'Streams'.DS.'StdIn.php';
require_once $baseDir.DS.'Streams'.DS.'StdOut.php';
require_once $baseDir.DS.'Exceptions'.DS.'IOException.php';
require_once $baseDir.DS.'Commands'.DS.'HelpCommand.php';

require_once $rootDir.DS.'bin'.DS.'InitAppCommand.php';
require_once $rootDir.'/tests/Cli/TestCommand.php';
require_once $rootDir.'/tests/Cli/TestCommands/Command00.php';
require_once $rootDir.'/tests/Cli/TestCommands/Command01.php';
require_once $rootDir.'/tests/Cli/TestCommands/Command03.php';
require_once $rootDir.'/tests/Cli/TestCommands/WithExceptionCommand.php';
require_once $rootDir.'/tests/TestStudent.php';
require_once $rootDir.'/tests/TestStudent2.php';
