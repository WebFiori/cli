# WebFiori CLI Examples

This directory contains comprehensive examples demonstrating the features and capabilities of the WebFiori CLI library. The examples are organized from basic to advanced use cases, each with its own README and runnable code.

## 📚 Example Categories

### 🟢 **Basic Examples**
Perfect for getting started with the library.

- **[01-basic-hello-world](01-basic-hello-world/)** - Simple command creation and execution
- **[02-arguments-and-options](02-arguments-and-options/)** - Working with command arguments and options
- **[03-user-input](03-user-input/)** - Reading and validating user input
- **[04-output-formatting](04-output-formatting/)** - ANSI colors, formatting, and styling

### 🟡 **Intermediate Examples**
Building more sophisticated CLI applications.

- **[05-interactive-commands](05-interactive-commands/)** - Creating interactive command experiences
- **[07-progress-bars](07-progress-bars/)** - Visual progress indicators for long operations

### 🔴 **Advanced Examples**
Complex scenarios and advanced features.

- **[10-multi-command-app](10-multi-command-app/)** - Building a complete CLI application
- **[13-database-cli](13-database-cli/)** - Database management CLI tool

## 🚀 Quick Start

Each example is self-contained and can be run independently:

```bash
# Navigate to any example directory
cd examples/01-basic-hello-world

# Run the example
php main.php [command] [options]

# Get help for any example
php main.php help
```

## 📋 Prerequisites

- PHP 8.0 or higher
- Composer (for dependency management)
- Terminal with ANSI support (recommended)

## 🛠️ Installation

1. Clone the repository:
```bash
git clone https://github.com/WebFiori/cli.git
cd cli
```

2. Install dependencies:
```bash
composer install
```

3. Navigate to any example and start exploring:
```bash
cd examples/01-basic-hello-world
php main.php hello --name="World"
```

## 📖 Learning Path

### For Beginners
Start with examples 01-04 to understand the fundamentals:
1. **Basic Hello World** - Command structure and basic output
2. **Arguments & Options** - Parameter handling and validation
3. **User Input** - Interactive input and validation
4. **Output Formatting** - Colors, styles, and visual elements

### For Intermediate Users
Continue with examples 05-07 to build more complex applications:
1. **Interactive Commands** - Menu systems and wizards
2. **Progress Bars** - Visual feedback for long operations

### For Advanced Users
Explore examples 10-13 for real-world applications:
1. **Multi-Command App** - Complete application architecture
2. **Database CLI** - Database management tools

## 🎯 Key Features Demonstrated

| Feature | Examples | Description |
|---------|----------|-------------|
| **Command Creation** | 01, 02, 10 | Basic to advanced command structures |
| **Arguments & Options** | 02, 13 | Parameter handling and validation |
| **User Input** | 03, 05 | Interactive input and validation |
| **Output Formatting** | 04, 07 | Colors, styles, and progress bars |
| **Interactive Workflows** | 05, 10 | Menu systems and wizards |
| **Progress Indicators** | 07, 10, 13 | Visual feedback for operations |
| **Data Management** | 10, 13 | CRUD operations and persistence |
| **Real-world Apps** | 10, 13 | Production-ready CLI tools |

## 🔧 Common Patterns

### Command Structure
```php
class MyCommand extends Command {
    public function __construct() {
        parent::__construct('my-command', [
            '--option' => [
                Option::DESCRIPTION => 'Command option',
                Option::OPTIONAL => true
            ]
        ], 'Command description');
    }
    
    public function exec(): int {
        // Command logic here
        return 0; // Success
    }
}
```

### Runner Setup
```php
$runner = new Runner();
$runner->register(new MyCommand());
$runner->register(new HelpCommand());
exit($runner->start());
```

### Progress Bar Usage
```php
$progressBar = $this->createProgressBar(100);
$progressBar->start('Processing...');

for ($i = 0; $i < 100; $i++) {
    // Do work
    $progressBar->advance();
}

$progressBar->finish('Complete!');
```

### Testing Commands
```php
class MyCommandTest extends CommandTestCase {
    public function testCommand() {
        $output = $this->executeSingleCommand(new MyCommand(), ['my-command']);
        $this->assertEquals(0, $this->getExitCode());
    }
}
```

## 🎨 Example Outputs

### Basic Hello World
```bash
$ php main.php hello --name="WebFiori"
🎉 Hello, WebFiori! Welcome to the CLI world!
You're using the WebFiori CLI library - great choice!
Have a wonderful day!
```

### Arguments & Options
```bash
$ php main.php calc --operation=add --numbers="5,10,15,20"
✅ Performing add on: 5, 10, 15, 20
📊 Result: 50.00
```

### Progress Bars
```bash
$ php main.php progress-demo --style=ascii --items=10
Processing with ascii style... [========================================] 100.0% (10/10)
Complete! ✨ Progress bar demonstration completed!
```

### Multi-Command App
```bash
$ php main.php user --action=list --format=table
👥 User Management - List Users

┌────┬─────────────┬─────────────────────┬─────────┬─────────────┐
│ ID │ Name        │ Email               │ Status  │ Created     │
├────┼─────────────┼─────────────────────┼─────────┼─────────────┤
│ 1  │ John Doe    │ john@example.com    │ Active  │ 2024-01-15  │
│ 2  │ Jane Smith  │ jane@example.com    │ Active  │ 2024-01-16  │
└────┴─────────────┴─────────────────────┴─────────┴─────────────┘

📊 Total: 2 users | Active: 2 | Inactive: 0
```

## 🧪 Testing Examples

Most examples include unit tests that can be run with PHPUnit:

```bash
# Run tests for a specific example
cd examples/10-multi-command-app
php ../../vendor/bin/phpunit tests/

# Run with coverage
php ../../vendor/bin/phpunit --coverage-html coverage/ tests/
```

## 🤝 Contributing

Found an issue or want to add a new example? Contributions are welcome!

1. Fork the repository
2. Create a new example following the existing structure
3. Add comprehensive README documentation
4. Include unit tests where applicable
5. Submit a pull request

### Example Structure Guidelines

Each example should follow this structure:
```
example-name/
├── README.md              # Comprehensive documentation
├── main.php              # Application entry point
├── SomeCommand.php       # Command classes
├── tests/                # Unit tests (optional)
│   └── SomeCommandTest.php
└── data/                 # Sample data files (if needed)
```

### Documentation Requirements

Each example README should include:
- **What You'll Learn** - Key concepts covered
- **Running the Examples** - Command examples
- **Code Explanation** - Key code snippets
- **Expected Output** - Sample outputs
- **Try This** - Extension ideas

## 📄 License

This project is licensed under the MIT License. See the main repository LICENSE file for details.

## 🆘 Support

- **Documentation**: Check individual example READMEs
- **Issues**: Report bugs or request features on GitHub
- **Community**: Join discussions in the WebFiori community

## 🎓 Additional Resources

- **[WebFiori CLI Documentation](https://webfiori.com/docs/cli)**
- **[PHP CLI Best Practices](https://www.php.net/manual/en/features.commandline.php)**
- **[ANSI Escape Codes Reference](https://en.wikipedia.org/wiki/ANSI_escape_code)**
- **[Command Line Interface Guidelines](https://clig.dev/)**

---

**Happy coding with WebFiori CLI!** 🎉

*Start with the basic examples and work your way up to building production-ready CLI applications!*
