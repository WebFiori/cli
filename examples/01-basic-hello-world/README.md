# Basic Hello World Example

This example demonstrates the most basic CLI command creation using WebFiori CLI library.

## Features Demonstrated

- Creating a simple command class
- Adding optional arguments with default values
- Basic output formatting with emojis
- Help system integration
- Error handling

## Files

- `main.php` - Application entry point and runner setup
- `HelloCommand.php` - The hello command implementation

## Usage Examples

### 1. Show General Help
```bash
php main.php
# or
php main.php help
```
**Output:**
```
Usage:
    command [arg1 arg2="val" arg3...]

Global Arguments:
    --ansi:[Optional] Force the use of ANSI output.
Available Commands:
    help:      Display CLI Help. To display help for specific command, use the argument "--command" with this command.
    hello:     A simple greeting command that says hello to someone
```

### 2. Show Command-Specific Help
```bash
php main.php help --command=hello
```
**Output:**
```
    hello:     A simple greeting command that says hello to someone
    Supported Arguments:
                       --name:[Optional][Default = 'World'] The name to greet (default: World)
```

### 3. Basic Hello (Default Name)
```bash
php main.php hello
```
**Output:**
```
Hello, World! 👋
Have a wonderful day!
```

### 4. Hello with Custom Name
```bash
php main.php hello --name=Ahmed
```
**Output:**
```
Hello, Ahmed! 👋
Have a wonderful day!
```

### 5. Hello with Multi-word Name
```bash
php main.php hello --name="Fatima Al-Zahra"
```
**Output:**
```
Hello, Fatima Al-Zahra! 👋
Have a wonderful day!
```

### 6. Using Global ANSI Flag
```bash
php main.php hello --name=Mohammed --ansi
```
**Output:**
```
Hello, Mohammed! 👋
Have a wonderful day!
```

### 7. Error Handling - Invalid Command
```bash
php main.php invalid
```
**Output:**
```
Error: The command 'invalid' is not supported.
```

## Key Learning Points

1. **Command Structure**: Commands extend `WebFiori\Cli\Command` and implement `exec()` method
2. **Arguments**: Optional arguments defined in constructor with default values
3. **Output**: Use `println()` for formatted output with emoji support
4. **Help Integration**: Commands automatically integrate with help system
5. **Error Handling**: Invalid commands show appropriate error messages
6. **Global Arguments**: `--ansi` flag works with all commands

## Code Structure

```php
class HelloCommand extends Command {
    public function __construct() {
        parent::__construct('hello', [
            '--name' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => 'World',
                ArgumentOption::DESCRIPTION => 'The name to greet (default: World)'
            ]
        ], 'A simple greeting command that says hello to someone');
    }

    public function exec(): int {
        $name = $this->getArgValue('--name');
        $this->println("Hello, %s! 👋", $name);
        $this->println("Have a wonderful day!");
        return 0;
    }
}
```

This example serves as the foundation for understanding WebFiori CLI basics before moving to more advanced features.
