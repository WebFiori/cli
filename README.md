# WebFiori CLI
Class library that can help in writing command line based applications with minimum dependencies using PHP.


<p align="center">
  <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php84.yml">
    <img src="https://github.com/WebFiori/cli/actions/workflows/php83.yml/badge.svg?branch=main">
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

## Content
* [Supported PHP Versions](#supported-php-versions)
* [Features](#features)
* [Sample Application](#sample-application)
* [Installation](#installation)
* [Creating and Running Commands](#creating-and-running-commands)
  * [Creating a Command](#creating-a-command)
  * [Running a Command](#running-a-command)
  * [Arguments](#arguments)
    * [Adding Arguments to Commands](#adding-arguments-to-commands)
    * [Accessing Argument Value](#accessing-argument-value)
* [Interactive Mode](#interactive-mode)
* [The `help` Command](#the-help-command)
  * [Setting Help Instructions](#setting-help-instructions)
  * [Running `help` Command](#running-help-command)
    * [General Help](#general-help)
    * [Command-Specific Help](#command-specific-help)
* [Unit-Testing Commands](#unit-testing-commands)

## Supported PHP Versions
|                                                                                      Build Status                                                                                       |
|:---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php80.yml"><img src="https://github.com/WebFiori/cli/actions/workflows/php80.yml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php81.yml"><img src="https://github.com/WebFiori/cli/actions/workflows/php81.yml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php82.yml"><img src="https://github.com/WebFiori/cli/actions/workflows/php82.yml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php83.yml"><img src="https://github.com/WebFiori/cli/actions/workflows/php83.yml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php84.yml"><img src="https://github.com/WebFiori/cli/actions/workflows/php84.yml/badge.svg?branch=main"></a> |

## Features
* Help in creating command line based applications.
* Support for interactive mode.
* Support for ANSI output.
* Support for implementing custom input and output streams.
* Ability to write tests for commands and test them using test automation tools.

## Sample Application

A sample application can be found here: https://github.com/WebFiori/cli/tree/main/example

## Installation

To install the library, simply include it in your `composer.json`'s `require` section: `"webfiori\cli":"*"`.

## Creating and Running Commands

### Creating a Command


First step in creating new command is to create a new class that extends the class `webfiori\cli\CLICommand`. The class `CLICommand` is a utility class which has methods that can be used to read inputs, send outputs and use command line arguments.

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

### Running a Command

The class `webfiori\cli\Runner` is the class which is used to manage the logic of executing the commands. In order to run a command, an instance of this class must be created and used to register the command and start running the application.

To register a command, the method `Runner::register()` is used. To start the application, the method `Runner::start()` is used.

``` php
// File src/main.php
require_once '../vendor/autoload.php';

use webfiori\cli\Runner;
use SampleCommand;


$runner = new Runner();
$runner->register(new SampleCommand());
exit($runner->start());
```

Now if terminal is opened and following command is executed:

``` bash
php main.php say-hi
```

The output will be the string `Hi People!`.

### Arguments

Arguments is a way that can be used to pass values from the terminal to PHP process. They can be used to configure execution of the command. For example, a command might require some kind of file as input. 

#### Adding Arguments to Commands

Arguments can be added in the constructor of the class as follows:

``` php
<?php
//File 'src/SampleCommand.php'
use webfiori\cli\CLICommand;
use webfiori\cli\Option;

class SampleCommand extends CLICommand {

    public function __construct(){
        parent::__construct('say-hi', [
            '--person-name' => [
                Option::OPTIONAL => true
            ]
        ]);
    }

    public function exec(): int {
        $this->println("Hi People!");
    }

}

```

Arguments can be provided as an associative array or array of objects of type `webfiori\cli\Argument`. In case of associative array, Index is name of the argument and the value of the index is sub-associative array of options. Each argument can have the following options:
* `optional`: A boolean. if set to true, it means that the argument is optional. Default is false.
* `default`: An optional default value for the argument to use if it is not provided.
* `description`: A description of the argument which will be shown if the command `help` is executed.
* `values`: A set of values that the argument can have. If provided, only the values on the list will be allowed.

The class `webfiori\cli\Option` can be used to access the options.

#### Accessing Argument Value

Accessing the value of an argument is performed using the method `CLICommand::getArgValue(string $argName)`. If argument is provided, the method will return its value as `string`. If not provided, `null` is returned.

``` php
<?php
//File 'src/SampleCommand.php'
use webfiori\cli\CLICommand;
use webfiori\cli\Option;

class SampleCommand extends CLICommand {

    public function __construct(){
        parent::__construct('say-hi', [
            '--person-name' => [
                Option::OPTIONAL => true
            ]
        ]);
    }

    public function exec(): int {
        $personName = $this->getArgValue('--person-name');
        
        if ($personName !== null) {
            $this->println("Hi %s!", $personName);
        } else {
            $this->println("Hi People!");
        }
        
    }

}

```

## Interactive Mode

Interactive mode is a way that can be used to keep your application running and execute more than one command using same PHP process. To start the application in interactive mode, add the argument `-i` when starting the application as follows:

``` bash
php main.php -i
```

This will show following output in terminal:

``` bash
>> Running in interactive mode.
>> Type command name or 'exit' to close.
>>
```

## `help` Command
One of the commands which comes by default with the library is the `help` command. It can be used to display help instructions for all registered commands. 

> Note: In order to use this command, it must be registered using the method `Runner::register()`. 

### Setting Help Instructions

Help instructions are provided by the developer who created the command during its implementation. Instructions can be set on the constructor of the class that extends the class `webfiori\cli\CLICommand` as a description. The description can be set for the command and its arguments.

``` php
<?php
//File 'src/SampleCommand.php'
use webfiori\cli\CLICommand;
use webfiori\cli\Option;

class GreetingsCommand extends CLICommand {

    public function __construct() {
        parent::__construct('hello', [
            '--person-name' => [
                Option::DESCRIPTION => 'Name of someone to greet.',
                Option::OPTIONAL => true
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

Help command can be used in two ways, one way is to display a general help for the application and another one for specific command.

#### General Help

To show general help of the application, following command can be executed.

``` bash
//File 'src/main.php'
php main.php help 
```

Output of this command will be as follows:

```
Usage:
    command [arg1 arg2="val" arg3...]

Global Arguments:
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
//File 'src/main.php'
php main.php help --command-name=hello
```

Output of this command will be as follows:

```
hello:         A command to show greetings.
    Supported Arguments:
                --person-name:[Optional] Name of someone to greet.
```

## Unit-Testing Commands

The library provides the helper class `webfiori\cli\CommandTestCase` which can be used to write unit tests for diffrent commands. The developer have to only extend the class and use utility methods to write tests. The class is based on PHPUnit.

The class has two methods which can be used to execute tests:

* `CommandTestCase::executeSingleCommand()`: Used to run one command at a time and return its output.
* `CommandTestCase::executeMultiCommand()`: Used to register multiple commands,set default command and/or run one of registered commands.

First method is good to verify the output of one specific command. The second one is usefule to simulate the execution of an application with multiple commands.

Both methods support simulating arguments vector and user inputs.


``` php
namespace tests\cli;

use webfiori\cli\CommandTestCase;

class HelloCommandTest extends CommandTestCase {
    /**
     * @test
     */
    public function test00() {
        
        //Verify test results
        
        $this->assertEquals([
            "Hello World!\n"
        ], $this->executeSingleCommand(new HelloWorldCommand()));
        $this->assertEquals(0, $this->getExitCode());
    }
}

```

A sample of tests can be found [here](https://github.com/WebFiori/cli/tree/main/example/tests/HelloCommandTest.php)


