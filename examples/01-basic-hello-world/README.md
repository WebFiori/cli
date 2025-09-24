# Basic Hello World Example

This example demonstrates the most fundamental concepts of creating a CLI command with the WebFiori CLI library.

## 🎯 What You'll Learn

- How to create a basic command class
- How to set up a CLI runner
- How to handle simple command execution
- Basic output methods

## 📁 Files

- `HelloCommand.php` - A simple greeting command
- `main.php` - Application entry point
- `README.md` - This documentation

## 🚀 Running the Example

```bash
# Basic greeting
php main.php hello

# Greeting with a name
php main.php hello --name="Alice"

# Get help
php main.php help
php main.php help --command-name=hello
```

## 📖 Code Explanation

### HelloCommand.php

The `HelloCommand` class extends the base `Command` class and demonstrates:

- **Command naming**: Using `hello` as the command name
- **Arguments**: Optional `--name` parameter with default value
- **Output**: Using `println()` for formatted output
- **Return codes**: Returning 0 for success

### main.php

The main application file shows:

- **Runner setup**: Creating and configuring the CLI runner
- **Command registration**: Adding commands to the runner
- **Help command**: Including built-in help functionality
- **Execution**: Starting the CLI application

## 🔍 Key Concepts

### Command Structure
```php
class HelloCommand extends Command {
    public function __construct() {
        parent::__construct(
            'hello',                    // Command name
            ['--name' => [...]],       // Arguments
            'A simple greeting command' // Description
        );
    }
    
    public function exec(): int {
        // Command logic
        return 0; // Success
    }
}
```

### Argument Definition
```php
'--name' => [
    Option::DESCRIPTION => 'Name to greet',
    Option::OPTIONAL => true,
    Option::DEFAULT => 'World'
]
```

### Output Methods
- `println()` - Print with newline
- `prints()` - Print without newline
- `success()` - Success message with green color
- `error()` - Error message with red color
- `info()` - Info message with blue color
- `warning()` - Warning message with yellow color

## 🎨 Expected Output

```
$ php main.php hello
Hello, World!

$ php main.php hello --name="Alice"
Hello, Alice!

$ php main.php help
Usage:
    command [arg1 arg2="val" arg3...]

Available Commands:
    help:          Display CLI Help
    hello:         A simple greeting command
```

## 🔗 Next Steps

After mastering this example, move on to:
- **[02-arguments-and-options](../02-arguments-and-options/)** - Learn about complex argument handling
- **[03-user-input](../03-user-input/)** - Discover interactive input methods
- **[04-output-formatting](../04-output-formatting/)** - Explore advanced output formatting

## 💡 Try This

Experiment with the code:

1. **Add more arguments**: Try adding `--greeting` option
2. **Change colors**: Use different output methods
3. **Add validation**: Ensure name is not empty
4. **Multiple greetings**: Support different languages

```php
// Example enhancement
if ($name === 'WebFiori') {
    $this->success("Hello, $name! Welcome to the CLI world!");
} else {
    $this->println("Hello, $name!");
}
```
