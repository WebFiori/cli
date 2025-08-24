# 📊 Example 16: Complete Table Usage Guide

This comprehensive example demonstrates all aspects of using tables in WebFiori CLI applications, from basic table creation to advanced styling and configuration.

## 🎯 What This Example Demonstrates

### Two Commands Available

#### 1. `basic-table` - Quick Start Guide
- **Simple table creation** - Get started in 30 seconds
- **Progressive examples** - From simplest to real-world usage
- **Essential features** - Title, styles, colors, themes
- **Quick tips** - Best practices for immediate use

#### 2. `table-usage` - Comprehensive Guide
- **Complete feature coverage** - All table capabilities
- **Advanced configuration** - Professional styling and formatting
- **Real-world examples** - System monitoring, user management, reports
- **Best practices** - Professional development guidelines

### Core Table Features
- **Basic Table Creation** - Simple data display with headers
- **Command Integration** - Using `$this->table()` method in commands
- **Data Formatting** - Currency, dates, percentages, and custom formatting
- **Status Colorization** - Conditional color application based on data values
- **Column Configuration** - Width, alignment, and custom formatters

### Styling and Themes
- **Table Styles** - All available styles (bordered, simple, minimal, etc.)
- **Color Themes** - Professional themes for different environments
- **Responsive Design** - Tables that adapt to terminal width
- **Custom Styling** - Advanced configuration options

### Advanced Features
- **TableOptions Constants** - Type-safe configuration keys
- **TableStyle Constants** - Clean style name constants (no STYLE_ prefix)
- **TableTheme Constants** - Clean theme name constants (no THEME_ prefix)
- **Helper Methods** - Validation and utility functions
- **Error Handling** - Graceful handling of edge cases

## 🚀 Quick Start

### Run the Basic Example
```bash
php main.php basic-table
```

This command shows:
1. **Simplest Table** - Just data and headers
2. **Table with Title** - Adding a title
3. **Different Style** - Using ASCII style
4. **Adding Colors** - Status colorization
5. **Professional Theme** - Business styling
6. **Real-World Example** - Employee directory

### Run the Comprehensive Guide
```bash
php main.php table-usage
```

This command covers:
1. **Basic Table Usage** - Simple data display
2. **Command Integration** - Method chaining and integration
3. **Data Formatting** - Custom formatters and alignment
4. **System Status Dashboard** - Real-world monitoring example
5. **Style Showcase** - All 10 table styles demonstrated
6. **Theme Showcase** - All 7 color themes demonstrated
7. **User Management** - Complete CRUD-style table
8. **Constants Usage** - Type-safe configuration
9. **Error Handling** - Edge case management
10. **Best Practices** - Professional development guidelines

## 💡 Basic Usage Examples

### Simplest Possible Table
```php
use WebFiori\Cli\Command;

class MyCommand extends Command {
    public function exec(): int {
        $data = [
            ['John Doe', 'Active'],
            ['Jane Smith', 'Inactive']
        ];
        
        // Just data and headers - that's it!
        $this->table($data, ['Name', 'Status']);
        
        return 0;
    }
}
```

### Adding a Title
```php
$this->table($data, ['Name', 'Status'], [
    TableOptions::TITLE => 'User Status'
]);
```

### Changing Style
```php
$this->table($data, ['Name', 'Status'], [
    TableOptions::STYLE => TableStyle::SIMPLE,
    TableOptions::TITLE => 'User Status (ASCII)'
]);
```

### Adding Colors
```php
$this->table($data, ['Name', 'Status'], [
    TableOptions::COLORIZE => [
        'Status' => function($value) {
            return $value === 'Active' 
                ? ['color' => 'green', 'bold' => true]
                : ['color' => 'red'];
        }
    ]
]);
```

## 📋 Configuration Options

### TableOptions Constants
| Constant | Description | Example Values |
|----------|-------------|----------------|
| `STYLE` | Table visual style | `TableStyle::BORDERED` |
| `THEME` | Color theme | `TableTheme::PROFESSIONAL` |
| `TITLE` | Table title | `'User Report'` |
| `WIDTH` | Maximum width | `120` |
| `SHOW_HEADERS` | Show/hide headers | `true` |
| `COLUMNS` | Column configuration | `['Name' => ['align' => 'left']]` |
| `COLORIZE` | Column colorization | `['Status' => $colorFunction]` |

### TableStyle Constants (Clean)
| Constant | Description | Visual Style |
|----------|-------------|--------------|
| `BORDERED` | Unicode box-drawing | `┌─┐│└─┘` |
| `SIMPLE` | ASCII characters | `+-+|+-+` |
| `MINIMAL` | Clean minimal borders | `───` |
| `COMPACT` | Space-efficient | `│───` |
| `MARKDOWN` | Markdown-compatible | `|---|` |

### TableTheme Constants (Clean)
| Constant | Description | Use Case |
|----------|-------------|----------|
| `DEFAULT` | Standard colors | General purpose |
| `DARK` | Dark terminal optimized | Dark backgrounds |
| `LIGHT` | Light terminal optimized | Light backgrounds |
| `PROFESSIONAL` | Business styling | Reports and presentations |
| `COLORFUL` | Vibrant colors | Status dashboards |

## 💡 Best Practices

### 1. Use Constants for Type Safety
```php
// ✅ Good - Type-safe with IDE support
use WebFiori\Cli\Table\TableStyle;
use WebFiori\Cli\Table\TableTheme;

$config = [
    TableOptions::STYLE => TableStyle::BORDERED,
    TableOptions::THEME => TableTheme::PROFESSIONAL
];

// ❌ Avoid - Prone to typos
$config = [
    'style' => 'borded',  // Typo!
    'theme' => 'professional'
];
```

### 2. Start Simple, Add Features Gradually
```php
// Step 1: Basic table
$this->table($data, $headers);

// Step 2: Add title
$this->table($data, $headers, [
    TableOptions::TITLE => 'My Report'
]);

// Step 3: Add styling
$this->table($data, $headers, [
    TableOptions::TITLE => 'My Report',
    TableOptions::STYLE => TableStyle::PROFESSIONAL
]);

// Step 4: Add colors
$this->table($data, $headers, [
    TableOptions::TITLE => 'My Report',
    TableOptions::STYLE => TableStyle::PROFESSIONAL,
    TableOptions::COLORIZE => [
        'Status' => fn($v) => $v === 'Active' ? ['color' => 'green'] : ['color' => 'red']
    ]
]);
```

### 3. Create Reusable Configurations
```php
class TableConfigurations {
    public static function getReportStyle(): array {
        return [
            TableOptions::STYLE => TableStyle::BORDERED,
            TableOptions::THEME => TableTheme::PROFESSIONAL,
            TableOptions::SHOW_HEADERS => true
        ];
    }
    
    public static function getStatusStyle(): array {
        return [
            TableOptions::STYLE => TableStyle::SIMPLE,
            TableOptions::THEME => TableTheme::COLORFUL
        ];
    }
}
```

## 🔧 Common Use Cases

### 1. User Management
```php
$users = [
    ['Alice Johnson', 'alice@example.com', 'Admin', 'Active'],
    ['Bob Smith', 'bob@example.com', 'User', 'Inactive']
];

$this->table($users, ['Name', 'Email', 'Role', 'Status'], [
    TableOptions::STYLE => TableStyle::BORDERED,
    TableOptions::THEME => TableTheme::PROFESSIONAL,
    TableOptions::TITLE => 'User Directory'
]);
```

### 2. System Status
```php
$services = [
    ['Web Server', 'nginx', 'Running', '99.9%'],
    ['Database', 'MySQL', 'Running', '99.8%'],
    ['Cache', 'Redis', 'Stopped', '0%']
];

$this->table($services, ['Service', 'Type', 'Status', 'Uptime'], [
    TableOptions::STYLE => TableStyle::SIMPLE,
    TableOptions::THEME => TableTheme::COLORFUL,
    TableOptions::COLORIZE => [
        'Status' => function($value) {
            return match(strtolower($value)) {
                'running' => ['color' => 'green', 'bold' => true],
                'stopped' => ['color' => 'red', 'bold' => true],
                default => []
            };
        }
    ]
]);
```

## 🎨 Learning Path

### Beginner (5 minutes)
1. Run `php main.php basic-table`
2. Try the simplest example: `$this->table($data, $headers)`
3. Add a title and change the style

### Intermediate (15 minutes)
1. Add status colorization
2. Try different themes
3. Format columns with alignment

### Advanced (30 minutes)
1. Run `php main.php table-usage`
2. Study the comprehensive examples
3. Implement custom formatters and complex colorization

## 🔍 Error Handling

The table system includes comprehensive error handling:

- **Missing table classes**: Graceful fallback with error message
- **Empty data**: Informative message instead of empty table
- **Invalid options**: Uses sensible defaults
- **Malformed data**: Handles edge cases gracefully

## 📚 Additional Resources

- **TableOptions Class**: Complete list of configuration options
- **TableStyle Class**: All available table styles
- **TableTheme Class**: All available color themes
- **Helper Methods**: Validation and utility functions

---

This example provides everything you need to create professional, beautiful tables in your WebFiori CLI applications, from basic usage to advanced features!
