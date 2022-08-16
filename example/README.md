# Sample Command Line Application

Here you can find a simple command line application with only 3 commands, `help`, `hello` and, `open-file`.

## Application Structure

The application has 3 source code files:
* `HelloWorldCommand.php`
* `OpenFileCommand.php`
* `app.php`

The first two are used to implement two custom commands, the `hello` and the `open-file` command. The last source file will act as the entry point of the application.

## Running The Application

The first step in running the application is to install any dependecies that are needed. To install them, run the command `php composer install` while begin in the root directory of the library. After that, navigate to the folder that has the sample application and run `php app.php -i` to start the application in intractive mode.s
