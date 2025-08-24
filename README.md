# WebFiori CLI
Class library that can help in writing command line based applications with minimum dependencies using PHP.

<p align="center">
  <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php84.yaml">
    <img src="https://github.com/WebFiori/cli/actions/workflows/php83.yaml/badge.svg?branch=main">
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
* [Quick Start](#quick-start)
* [Sample Application](#sample-application)
* [Installation](#installation)
* [Basic Usage](#basic-usage)
  * [Simple Command Example](#simple-command-example)
  * [Command with Arguments](#command-with-arguments)
  * [Multi-Command Application](#multi-command-application)
* [Creating and Running Commands](#creating-and-running-commands)
  * [Creating a Command](#creating-a-command)
  * [Running a Command](#running-a-command)
  * [Arguments](#arguments)
    * [Adding Arguments to Commands](#adding-arguments-to-commands)
    * [Accessing Argument Value](#accessing-argument-value)
* [Advanced Features](#advanced-features)
  * [Interactive Mode](#interactive-mode)
  * [Input and Output Streams](#input-and-output-streams)
  * [ANSI Colors and Formatting](#ansi-colors-and-formatting)
  * [Progress Bars](#progress-bars)
  * [Table Display](#table-display)
* [The `help` Command](#the-help-command)
  * [Setting Help Instructions](#setting-help-instructions)
  * [Running `help` Command](#running-help-command)
    * [General Help](#general-help)
    * [Command-Specific Help](#command-specific-help)
* [Unit-Testing Commands](#unit-testing-commands)
* [Examples](#examples)

## Supported PHP Versions
|                                                                                      Build Status                                                                                       |
|:---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php81.yaml"><img src="https://github.com/WebFiori/cli/actions/workflows/php81.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php82.yaml"><img src="https://github.com/WebFiori/cli/actions/workflows/php82.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php83.yaml"><img src="https://github.com/WebFiori/cli/actions/workflows/php83.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/cli/actions/workflows/php84.yaml"><img src="https://github.com/WebFiori/cli/actions/workflows/php84.yaml/badge.svg?branch=main"></a> |

## Features
* **Easy Command Creation**: Simple class-based approach to building CLI commands
* **Argument Handling**: Support for required and optional arguments with validation
* **Interactive Mode**: Keep your application running and execute multiple commands
* **ANSI Output**: Rich text formatting with colors and styles
* **Input/Output Streams**: Custom input and output stream implementations
* **Progress Bars**: Built-in progress indicators for long-running operations
* **Table Display**: Format and display data in clean, readable tables
* **Help System**: Automatic help generation for commands and arguments
* **Unit Testing**: Built-in testing utilities for command validation
* **Minimal Dependencies**: Lightweight library with minimal external requirements

## Quick Start

Get up and running in minutes:

```bash
# Install via Composer
composer require webfiori/cli

# Create your first command
php -r "
require 'vendor/autoload.php';
use WebFiori\Cli\Command;
use WebFiori\Cli\Runner;

class HelloCommand extends Command {
    public function __construct() {
        parent::__construct('hello', [], 'Say hello to the world');
    }
    public function exec(): int {
        \$this->println('Hello, World!');
        return 0;
    }
}

\$runner = new Runner();
\$runner->register(new HelloCommand());
exit(\$runner->start());
" hello
```

## Sample Application

A complete sample application with multiple examples can be found here: **[📁 View Sample Application](https://github.com/WebFiori/cli/tree/main/examples)**

The sample application includes:
- **[Basic Commands](https://github.com/WebFiori/cli/tree/main/examples/01-basic-command)** - Simple command creation
- **[Arguments Handling](https://github.com/WebFiori/cli/tree/main/examples/02-command-with-args)** - Working with command arguments
- **[Interactive Mode](https://github.com/WebFiori/cli/tree/main/examples/03-interactive-mode)** - Building interactive applications
- **[Multi-Command Apps](https://github.com/WebFiori/cli/tree/main/examples/10-multi-command-app)** - Complex applications with multiple commands
- **[Progress Bars](https://github.com/WebFiori/cli/tree/main/examples/05-progress-bars)** - Visual progress indicators
- **[Table Display](https://github.com/WebFiori/cli/tree/main/examples/06-table-display)** - Formatting data in tables
- **[Testing Examples](https://github.com/WebFiori/cli/tree/main/examples/tests)** - Unit testing your commands

## Installation

Install WebFiori CLI using Composer:

```bash
composer require webfiori/cli
```

Or add it to your `composer.json`:

```json
{
    "require": {
        "webfiori/cli": "*"
    }
}
```

## Basic Usage

### Simple Command Example

Create a basic command that outputs a message:

```php
<?php
require_once 'vendor/autoload.php';

use WebFiori\Cli\Command;
use WebFiori\Cli\Runner;

class GreetCommand extends Command {
    public function __construct() {
        parent::__construct('greet', [], 'Greet the user');
    }

    public function exec(): int {
        $this->println("Hello from WebFiori CLI!");
        return 0;
    }
}

$runner = new Runner();
$runner->register(new GreetCommand());
exit($runner->start());
```

**Usage:**
```bash
php app.php greet
# Output: Hello from WebFiori CLI!
```

**[📖 View Complete Example](https://github.com/WebFiori/cli/tree/main/examples/01-basic-command)**

### Command with Arguments

Create a command that accepts and processes arguments:

```php
<?php
use WebFiori\Cli\Command;
use WebFiori\Cli\Option;

class PersonalGreetCommand extends Command {
    public function __construct() {
        parent::__construct('greet-person', [
            '--name' => [
                Option::OPTIONAL => false,
                Option::DESCRIPTION => 'Name of the person to greet'
            ],
            '--title' => [
                Option::OPTIONAL => true,
                Option::DEFAULT => 'Friend',
                Option::DESCRIPTION => 'Title to use (Mr, Ms, Dr, etc.)'
            ]
        ], 'Greet a specific person');
    }

    public function exec(): int {
        $name = $this->getArgValue('--name');
        $title = $this->getArgValue('--title');
        
        $this->println("Hello %s %s!", $title, $name);
        return 0;
    }
}
```

**Usage:**
```bash
php app.php greet-person --name=John --title=Mr
# Output: Hello Mr John!

php app.php greet-person --name=Sarah
# Output: Hello Friend Sarah!
```

**[📖 View Complete Example](https://github.com/WebFiori/cli/tree/main/examples/02-command-with-args)**

### Multi-Command Application

Build applications with multiple commands:

```php
<?php
use WebFiori\Cli\Runner;

// Register multiple commands
$runner = new Runner();
$runner->register(new GreetCommand());
$runner->register(new PersonalGreetCommand());
$runner->register(new FileProcessCommand());
$runner->register(new DatabaseCommand());

// Set application info
$runner->setAppName('My CLI App');
$runner->setAppVersion('1.0.0');

exit($runner->start());
```

**Usage:**
```bash
php app.php help                    # Show all available commands
php app.php greet                   # Run greet command
php app.php greet-person --name=Bob # Run greet-person command
php app.php -i                      # Start interactive mode
```

**[📖 View Complete Example](https://github.com/WebFiori/cli/tree/main/examples/10-multi-command-app)**

## Creating and Running Commands

### Creating a Command

First step in creating new command is to create a new class that extends the class `WebFiori\Cli\Command`. The class `Command` is a utility class which has methods that can be used to read inputs, send outputs and use command line arguments.

The class has one abstract method that must be implemented. The code that will exist in the body of the method will represent the logic of the command.

``` php
<?php
//File 'src/SampleCommand.php'
use WebFiori\Cli\Command;

class SampleCommand extends Command {

    public function __construct(){
        parent::__construct('say-hi');
    }

    public function exec(): int {
        $this->println("Hi People!");
        return 0;
    }

}

```

### Running a Command

The class `WebFiori\Cli\Runner` is the class which is used to manage the logic of executing the commands. In order to run a command, an instance of this class must be created and used to register the command and start running the application.

To register a command, the method `Runner::register()` is used. To start the application, the method `Runner::start()` is used.

``` php
// File src/main.php
require_once '../vendor/autoload.php';

use WebFiori\Cli\Runner;
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
use WebFiori\Cli\Command;
use WebFiori\Cli\Option;

class SampleCommand extends Command {

    public function __construct(){
        parent::__construct('say-hi', [
            '--person-name' => [
                Option::OPTIONAL => true
            ]
        ]);
    }

    public function exec(): int {
        $this->println("Hi People!");
        return 0;
    }

}

```

Arguments can be provided as an associative array or array of objects of type `WebFiori\Cli\Argument`. In case of associative array, Index is name of the argument and the value of the index is sub-associative array of options. Each argument can have the following options:
* `optional`: A boolean. if set to true, it means that the argument is optional. Default is false.
* `default`: An optional default value for the argument to use if it is not provided.
* `description`: A description of the argument which will be shown if the command `help` is executed.
* `values`: A set of values that the argument can have. If provided, only the values on the list will be allowed.

The class `WebFiori\Cli\Option` can be used to access the options.

#### Accessing Argument Value

Accessing the value of an argument is performed using the method `Command::getArgValue(string $argName)`. If argument is provided, the method will return its value as `string`. If not provided, `null` is returned.

``` php
<?php
//File 'src/SampleCommand.php'
use WebFiori\Cli\Command;
use WebFiori\Cli\Option;

class SampleCommand extends Command {

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
        
        return 0;
    }

}

```

## Advanced Features

### Interactive Mode

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

**[📖 View Interactive Mode Example](https://github.com/WebFiori/cli/tree/main/examples/03-interactive-mode)**

### Input and Output Streams

WebFiori CLI supports custom input and output streams for advanced use cases:

```php
use WebFiori\Cli\Streams\FileInputStream;
use WebFiori\Cli\Streams\FileOutputStream;

// Read from file instead of stdin
$command->setInputStream(new FileInputStream('input.txt'));

// Write to file instead of stdout
$command->setOutputStream(new FileOutputStream('output.txt'));
```

**[📖 View Streams Example](https://github.com/WebFiori/cli/tree/main/examples/04-custom-streams)**

### ANSI Colors and Formatting

Add colors and formatting to your CLI output:

```php
public function exec(): int {
    $this->println("This is %s text", 'normal');
    $this->println("This is {{bold}}bold{{/bold}} text");
    $this->println("This is {{red}}red{{/red}} text");
    $this->println("This is {{bg-blue}}{{white}}white on blue{{/white}}{{/bg-blue}} text");
    return 0;
}
```

**[📖 View Formatting Example](https://github.com/WebFiori/cli/tree/main/examples/07-ansi-formatting)**

### Progress Bars

Display progress for long-running operations:

```php
use WebFiori\Cli\Progress\ProgressBar;

public function exec(): int {
    $items = range(1, 100);
    
    $this->withProgressBar($items, function($item, $bar) {
        // Process each item
        usleep(50000); // Simulate work
        $bar->setMessage("Processing item {$item}");
    });
    
    return 0;
}
```

**[📖 View Progress Bar Example](https://github.com/WebFiori/cli/tree/main/examples/05-progress-bars)**

### Table Display

Display data in formatted tables:

```php
public function exec(): int {
    $data = [
        ['John Doe', 30, 'New York'],
        ['Jane Smith', 25, 'Los Angeles']
    ];
    $headers = ['Name', 'Age', 'City'];
    
    $this->table($data, $headers);
    
    return 0;
}
```

**[📖 View Table Display Example](https://github.com/WebFiori/cli/tree/main/examples/06-table-display)**

## The `help` Command
One of the commands which comes by default with the library is the `help` command. It can be used to display help instructions for all registered commands. 

> Note: In order to use this command, it must be registered using the method `Runner::register()`. 

### Setting Help Instructions

Help instructions are provided by the developer who created the command during its implementation. Instructions can be set on the constructor of the class that extends the class `WebFiori\Cli\Command` as a description. The description can be set for the command and its arguments.

``` php
<?php
//File 'src/SampleCommand.php'
use WebFiori\Cli\Command;
use WebFiori\Cli\Option;

class GreetingsCommand extends Command {

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
php main.php help --command-name=hello
```

Output of this command will be as follows:

```
hello:         A command to show greetings.
    Supported Arguments:
                --person-name:[Optional] Name of someone to greet.
```

## Unit-Testing Commands

The library provides the helper class `WebFiori\Cli\CommandTestCase` which can be used to write unit tests for different commands. The developer has to only extend the class and use utility methods to write tests. The class is based on PHPUnit.

The class has two methods which can be used to execute tests:

* `CommandTestCase::executeSingleCommand()`: Used to run one command at a time and return its output.
* `CommandTestCase::executeMultiCommand()`: Used to register multiple commands, set default command and/or run one of registered commands.

First method is good to verify the output of one specific command. The second one is useful to simulate the execution of an application with multiple commands.

Both methods support simulating arguments vector and user inputs.


``` php
namespace tests\cli;

use WebFiori\Cli\CommandTestCase;

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

**[📖 View Testing Examples](https://github.com/WebFiori/cli/tree/main/examples/tests)**

## Examples

Explore comprehensive examples to learn different aspects of WebFiori CLI:

### Basic Examples
- **[📁 Basic Command](https://github.com/WebFiori/cli/tree/main/examples/01-basic-command)** - Create your first CLI command
- **[📁 Command with Arguments](https://github.com/WebFiori/cli/tree/main/examples/02-command-with-args)** - Handle command-line arguments
- **[📁 Interactive Mode](https://github.com/WebFiori/cli/tree/main/examples/03-interactive-mode)** - Build interactive CLI applications

### Advanced Examples
- **[📁 Custom Streams](https://github.com/WebFiori/cli/tree/main/examples/04-custom-streams)** - Custom input/output handling
- **[📁 Progress Bars](https://github.com/WebFiori/cli/tree/main/examples/05-progress-bars)** - Visual progress indicators
- **[📁 Table Display](https://github.com/WebFiori/cli/tree/main/examples/06-table-display)** - Format data in tables
- **[📁 ANSI Formatting](https://github.com/WebFiori/cli/tree/main/examples/07-ansi-formatting)** - Colors and text formatting
- **[📁 File Processing](https://github.com/WebFiori/cli/tree/main/examples/08-file-processing)** - File manipulation commands
- **[📁 Database Operations](https://github.com/WebFiori/cli/tree/main/examples/09-database-ops)** - Database CLI commands

### Complete Applications
- **[📁 Multi-Command Application](https://github.com/WebFiori/cli/tree/main/examples/10-multi-command-app)** - Full-featured CLI application
- **[📁 Testing Suite](https://github.com/WebFiori/cli/tree/main/examples/tests)** - Unit testing examples

### Quick Links
- **[📖 All Examples](https://github.com/WebFiori/cli/tree/main/examples)** - Browse all available examples
- **[🧪 Test Examples](https://github.com/WebFiori/cli/tree/main/examples/tests/HelloCommandTest.php)** - See how to test your commands
- **[🚀 Sample App](https://github.com/WebFiori/cli/tree/main/examples/10-multi-command-app/app.php)** - Ready-to-run sample application

---

**Ready to build amazing CLI applications? Start with the [📁 Basic Command Example](https://github.com/WebFiori/cli/tree/main/examples/01-basic-command) and work your way up!**
