# CLI
Class library that can help in writing command line based applications using PHP.


<p align="center">
  <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php81.yml">
    <img src="https://github.com/WebFiori/cli/workflows/Build%20PHP%208.1/badge.svg?branch=main">
  </a>
  <a href="https://codecov.io/gh/WebFiori/cli">
    <img src="https://codecov.io/gh/WebFiori/cli/branch/main/graph/badge.svg" />
  </a>
  <a href="https://sonarcloud.io/dashboard?id=WebFiori_cli">
      <img src="https://sonarcloud.io/api/project_badges/measure?project=WebFiori_cli&metric=alert_status" />
  </a>
  <a href="https://github.com/WebFiori/cli/releases">
      <img src="https://img.shields.io/github/release/WebFiori/cli.svg?label=latest" />
  </a>
  <a href="https://packagist.org/packages/webfiori/cli">
    <img src="https://img.shields.io/packagist/dt/webfiori/cli?color=light-green">
  </a>
</p>

## Supported PHP Versions
|                                                                                      Build Status                                                                                       |
|:---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php70.yml"><img src="https://github.com/WebFiori/cli/workflows/Build%20PHP%207.0/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php71.yml"><img src="https://github.com/WebFiori/cli/workflows/Build%20PHP%207.1/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php72.yml"><img src="https://github.com/WebFiori/cli/workflows/Build%20PHP%207.2/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php73.yml"><img src="https://github.com/WebFiori/cli/workflows/Build%20PHP%207.3/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php74.yml"><img src="https://github.com/WebFiori/cli/workflows/Build%20PHP%207.4/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php80.yml"><img src="https://github.com/WebFiori/cli/workflows/Build%20PHP%208.0/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php81.yml"><img src="https://github.com/WebFiori/cli/workflows/Build%20PHP%208.1/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php82.yml"><img src="https://github.com/WebFiori/cli/workflows/Build%20PHP%208.2/badge.svg?branch=main"></a> |

## Features
* Help in creating command line based applications.
* Support for interactive mode.
* Support for ANSI output.
* Support for implementing custom input and output streams.
* Ability to write tests for commands and test them using test automation tools.

## Installation

To install the library, simply include it in your `composer.json`'s `requre` section: `webfiori\cli`.

## Creating and Running a Command

### Creating a Command


First step in creating new command is to create a new class that extends the class `CLICommand`. The class `CLICommand` is a utility class which has methods which can be used to read inputs and send outputs.

The class has one abstract method that must be implemented. The code that will exist in the body of the method will represent the logic of the command.

``` php
<?php
//File 'src/SampleCommand.php'
use webfiori\cli\CLICommand;

class SampleCommand extends CLICommand {

    public function __construct(){
        parent::__construct('say-hi');
    }

    public function exec(): int {
        $this->println("Hi People!");
    }

}

```

### Running the Command

The class `Runner` is the class which is used to manage the logic of executing the commands. In order to run a command, an instance of this class must be created and used to register the command.

``` php
// File src/app.php
require_once './vendor/autoload.php';

use webfiori\cli\Runner;
use SampleCommand;


$runner = new Runner();
$runner->register(new SampleCommand());
$runner->start();
```

Now if terminal is opened and following command is executed:

``` bash
php app.php say-hi
```

The output will be the string `Hi People!`.

## Interactive Mode

Interactive mode is a way that can be used to keep your application running and execute more than one command using same PHP process. To start the application in interactive mode, add the argument `-i` when starting the application as follows:

``` bash
php app.php -i
```

This will show following output in terminal:

``` bash
>> Running in interactive mode.
>> Type command name or 'exit' to close.
>>
```

## The `help` Command
One of the commands which comes by default is the `help` command. It can be used to display help instructions for all registered commands. 

### Setting Help Instructions

Help instructions are provided by the developer who created the command during its implementation. Instructions can be set on the constructor of the class that extends the class `CLICommand` as a description. The description can be set for the command and its arguments.

``` php
<?php
//File 'src/SampleCommand.php'
use webfiori\cli\CLICommand;

class GreetingsCommand extends CLICommand {

    public function __construct() {
        parent::__construct('hello', [
            '--person-name' => [
                'description' => 'Name of someone to greet.',
                'optional' => true
            ]
        ], 'A command to show greetings.');
    }

    public function exec(): int {
        $name = $this->getArgValue('--person-name');

        if ($name === null) {
            $this->println("Hello World!");
        } else {
            $this->println("Hello %s!", $name);
        }

        return 0;
    }
}

```

### Running `help` Command

#### General Help

To show general help of the application, following command can be executed.

``` bash
//File 'src/app.php'
php app.php help 
```

Output of this command will be as follows:

```
Usage:
    command [arg1 arg2="val" arg3...]

Global Arguments:[0m[k
    --ansi:[Optional] Force the use of ANSI output.
Available Commands:
    help:          Display CLI Help. To display help for specific command, use the argument "--command-name" with this command.
    hello:         A command to show greetings.
    open-file:     Reads a text file and display its content.

```

> Note: Depending on registered commands, output may differ.

#### Command-Specific Help

To show help instructions for a specific command, the name of the command can be included using the argument `--command-name` as follows:

``` bash
//File 'src/app.php'
php app.php help --command-name=hello
```

Output of this command will be as follows:

```
hello:         A command to show greetings.
    Supported Arguments:
                --person-name:[Optional] Name of someone to greet.
```
