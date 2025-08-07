# Sample Command Line Application

This folder holds a simple command line application with only 3 commands, `help`, `hello` and, `open-file`.

## Application Structure

The application has 3 source code files:
* `app/HelloWorldCommand.php`
* `app/OpenFileCommand.php`
* `app/main.php`


The first two are used to implement two custom commands, `hello` and `open-file`. The last source file acts as the entry point of the application.

In addition to given sources, the folder `tests` contain one file which shows how to write unit tests for commands.

## Running The Application

The first step in running the application is to install any dependencies that are needed. 

To install them, run the command `php composer install` while begin in the root directory of the library. 
After that, navigate to the folder that has the sample application and run `php main.php -i` to start the application in interactive mode.
