# WebFiori CLI Table Feature

A comprehensive tabular data display system for CLI applications with advanced formatting, styling, and responsive design capabilities.

## 🎯 Overview

The WebFiori CLI Table feature provides a powerful and flexible way to display tabular data in command-line applications. It offers:

- **Multiple table styles** (bordered, simple, minimal, compact, markdown)
- **Intelligent column sizing** with responsive design
- **Advanced data formatting** (currency, dates, numbers, booleans)
- **Color themes and customization**
- **Export capabilities** (JSON, CSV, arrays)
- **Professional table rendering** with Unicode support

## 🏗️ Architecture

The table system consists of 8 core classes:

### Core Classes

1. **TableBuilder** - Main interface for creating and configuring tables
2. **TableRenderer** - Handles the actual rendering logic
3. **TableStyle** - Defines visual styling (borders, characters, spacing)
4. **Column** - Represents individual column configuration
5. **TableData** - Data container and processor
6. **TableFormatter** - Content-specific formatting logic
7. **ColumnCalculator** - Advanced width calculation algorithms
8. **TableTheme** - Higher-level theming system

## 🚀 Quick Start

### Basic Usage

```php
use WebFiori\Cli\Table\TableBuilder;

// Create a simple table
$table = TableBuilder::create()
    ->setHeaders(['Name', 'Email', 'Status'])
    ->addRow(['John Doe', 'john@example.com', 'Active'])
    ->addRow(['Jane Smith', 'jane@example.com', 'Inactive']);

echo $table->render();
```

### With Data Array

```php
$data = [
    ['John Doe', 'john@example.com', 'Active'],
    ['Jane Smith', 'jane@example.com', 'Inactive'],
    ['Bob Johnson', 'bob@example.com', 'Active']
];

$table = TableBuilder::create()
    ->setHeaders(['Name', 'Email', 'Status'])
    ->addRows($data);

echo $table->render();
```

## 🎨 Styling Options

### Available Styles

```php
// Different table styles
$table->useStyle('bordered');   // Default Unicode borders
$table->useStyle('simple');     // ASCII characters
$table->useStyle('minimal');    // Minimal borders
$table->useStyle('compact');    // Space-efficient
$table->useStyle('markdown');   // Markdown-compatible
```

### Custom Styles

```php
use WebFiori\Cli\Table\TableStyle;

$customStyle = TableStyle::custom([
    'topLeft' => '╔',
    'topRight' => '╗',
    'horizontal' => '═',
    'vertical' => '║',
    'showBorders' => true
]);

$table->setStyle($customStyle);
```

## ⚙️ Column Configuration

### Basic Configuration

```php
$table->configureColumn('Name', [
    'width' => 20,
    'align' => 'left',
    'truncate' => true
]);

$table->configureColumn('Balance', [
    'width' => 12,
    'align' => 'right',
    'formatter' => fn($value) => '$' . number_format($value, 2)
]);
```

### Advanced Column Types

```php
use WebFiori\Cli\Table\Column;

// Numeric column
$table->configureColumn('Price', [
    'width' => 10,
    'align' => 'right',
    'formatter' => Column::createColumnFormatter('currency', [
        'symbol' => '$',
        'decimals' => 2
    ])
]);

// Date column
$table->configureColumn('Created', [
    'formatter' => Column::createColumnFormatter('date', [
        'format' => 'M j, Y'
    ])
]);

// Boolean column
$table->configureColumn('Active', [
    'formatter' => Column::createColumnFormatter('boolean', [
        'true_text' => '✅ Yes',
        'false_text' => '❌ No'
    ])
]);
```

## 🌈 Color and Themes

### Status-Based Colorization

```php
$table->colorizeColumn('Status', function($value) {
    return match(strtolower($value)) {
        'active' => ['color' => 'green', 'bold' => true],
        'inactive' => ['color' => 'red'],
        'pending' => ['color' => 'yellow'],
        default => []
    };
});
```

### Predefined Themes

```php
use WebFiori\Cli\Table\TableTheme;

$table->setTheme(TableTheme::dark());        // Dark theme
$table->setTheme(TableTheme::colorful());    // Colorful theme
$table->setTheme(TableTheme::professional()); // Professional theme
$table->setTheme(TableTheme::minimal());     // No colors
```

### Custom Themes

```php
$customTheme = TableTheme::custom([
    'headerColors' => ['color' => 'blue', 'bold' => true],
    'alternatingRowColors' => [
        [],
        ['background' => 'light-blue']
    ],
    'useAlternatingRows' => true
]);

$table->setTheme($customTheme);
```

## 📊 Data Formatting

### Built-in Formatters

```php
use WebFiori\Cli\Table\TableFormatter;

$formatter = new TableFormatter();

// Currency formatting
$formatter->formatCurrency(1250.75, '$', 2); // "$1,250.75"

// Percentage formatting
$formatter->formatPercentage(85.5, 1); // "85.5%"

// File size formatting
$formatter->formatFileSize(1048576); // "1.00 MB"

// Duration formatting
$formatter->formatDuration(3665); // "1h 1m 5s"
```

### Custom Formatters

```php
$table->configureColumn('Status', [
    'formatter' => function($value) {
        return match(strtolower($value)) {
            'active' => '🟢 Active',
            'inactive' => '🔴 Inactive',
            'pending' => '🟡 Pending',
            default => $value
        };
    }
]);
```

## 📱 Responsive Design

### Terminal Width Awareness

```php
// Auto-detect terminal width
$table->setAutoWidth(true);

// Set maximum width
$table->setMaxWidth(120);

// Responsive column configuration
$table->configureColumn('Description', [
    'minWidth' => 10,
    'maxWidth' => 50,
    'truncate' => true
]);
```

## 💾 Data Export

### Export Formats

```php
use WebFiori\Cli\Table\TableData;

$data = new TableData($headers, $rows);

// Export to JSON
$json = $data->toJson(true); // Pretty printed

// Export to CSV
$csv = $data->toCsv(true); // Include headers

// Export to array
$array = $data->toArray(true); // Include headers

// Export to associative array
$assoc = $data->toAssociativeArray();
```

## 🔧 Advanced Features

### Data Filtering and Sorting

```php
use WebFiori\Cli\Table\TableData;

$data = new TableData($headers, $rows);

// Filter data
$filtered = $data->filterRows(fn($row) => $row[2] === 'Active');

// Sort by column
$sorted = $data->sortByColumn(0, true); // Sort by first column, ascending

// Limit results
$limited = $data->limit(10, 0); // First 10 rows
```

### Statistics and Analysis

```php
$data = new TableData($headers, $rows);

// Get column statistics
$stats = $data->getColumnStatistics(0);
// Returns: count, non_empty, unique, min, max, avg (for numeric)

// Get column type
$type = $data->getColumnType(0); // 'string', 'integer', 'float', 'date', 'boolean'

// Get unique values
$unique = $data->getUniqueValues(0);
```

### Large Dataset Handling

```php
// For large datasets, use pagination
$pageSize = 20;
$page = 1;
$offset = ($page - 1) * $pageSize;

$paginatedData = $data->limit($pageSize, $offset);

$table = TableBuilder::create()
    ->setData($paginatedData->toArray())
    ->setTitle("Page $page of " . ceil($data->getRowCount() / $pageSize));
```

## 🎯 Best Practices

### Performance Optimization

1. **Use appropriate column widths** to avoid unnecessary calculations
2. **Limit data size** for large datasets using pagination
3. **Cache formatted values** when displaying the same data multiple times
4. **Use minimal styles** for better performance in resource-constrained environments

### Accessibility

1. **Use high contrast themes** for better visibility
2. **Provide meaningful column headers**
3. **Use consistent formatting** across similar data types
4. **Consider ASCII fallbacks** for terminals without Unicode support

### User Experience

1. **Show loading indicators** for large datasets
2. **Provide clear empty state messages**
3. **Use consistent color coding** for status indicators
4. **Implement responsive design** for different terminal sizes

## 📚 Examples

### Complete User Management Table

```php
use WebFiori\Cli\Table\TableBuilder;
use WebFiori\Cli\Table\TableTheme;

$users = [
    ['John Doe', 'john@example.com', 'Active', '2024-01-15', 1250.75, 'Admin'],
    ['Jane Smith', 'jane@example.com', 'Inactive', '2024-01-16', 890.50, 'User'],
    ['Bob Johnson', 'bob@example.com', 'Active', '2024-01-17', 2100.00, 'Manager']
];

$table = TableBuilder::create()
    ->setHeaders(['Name', 'Email', 'Status', 'Created', 'Balance', 'Role'])
    ->addRows($users)
    ->setTitle('User Management System')
    ->setTheme(TableTheme::professional())
    ->configureColumn('Name', ['width' => 15])
    ->configureColumn('Email', ['width' => 25, 'truncate' => true])
    ->configureColumn('Status', ['width' => 10, 'align' => 'center'])
    ->configureColumn('Created', [
        'width' => 12,
        'formatter' => fn($date) => date('M j, Y', strtotime($date))
    ])
    ->configureColumn('Balance', [
        'width' => 12,
        'align' => 'right',
        'formatter' => fn($value) => '$' . number_format($value, 2)
    ])
    ->configureColumn('Role', ['width' => 10, 'align' => 'center'])
    ->colorizeColumn('Status', function($value) {
        return match(strtolower($value)) {
            'active' => ['color' => 'green', 'bold' => true],
            'inactive' => ['color' => 'red'],
            default => []
        };
    });

echo $table->render();
```

### System Status Dashboard

```php
$services = [
    ['Web Server', 'Active', '99.9%', '45ms', '✅'],
    ['Database', 'Active', '99.8%', '12ms', '✅'],
    ['Cache Server', 'Inactive', '0%', 'N/A', '❌'],
    ['API Gateway', 'Active', '99.7%', '78ms', '✅']
];

$table = TableBuilder::create()
    ->setHeaders(['Service', 'Status', 'Uptime', 'Response Time', 'Health'])
    ->addRows($services)
    ->setTitle('System Status Dashboard')
    ->useStyle('bordered')
    ->configureColumn('Service', ['width' => 15])
    ->configureColumn('Status', ['width' => 10, 'align' => 'center'])
    ->configureColumn('Uptime', ['width' => 8, 'align' => 'right'])
    ->configureColumn('Response Time', ['width' => 15, 'align' => 'right'])
    ->configureColumn('Health', ['width' => 8, 'align' => 'center'])
    ->colorizeColumn('Status', function($value) {
        return match(strtolower($value)) {
            'active' => ['color' => 'green', 'bold' => true],
            'inactive' => ['color' => 'red', 'bold' => true],
            default => []
        };
    });

echo $table->render();
```

## 🔍 Troubleshooting

### Common Issues

1. **Unicode characters not displaying**: Use ASCII fallback styles
2. **Column width issues**: Set explicit widths or adjust terminal size
3. **Color not showing**: Check terminal color support
4. **Performance issues**: Limit data size and use simpler styles

### Debug Mode

```php
// Enable debug information
$table->setTitle('Debug: ' . $table->getColumnCount() . ' columns, ' . $table->getRowCount() . ' rows');
```

## 🚀 Integration with WebFiori CLI

The table feature integrates seamlessly with existing WebFiori CLI commands:

```php
use WebFiori\Cli\CLICommand;
use WebFiori\Cli\Table\TableBuilder;

class ListUsersCommand extends CLICommand {
    
    public function exec(): int {
        $users = $this->getUsersFromDatabase();
        
        $table = TableBuilder::create()
            ->setHeaders(['ID', 'Name', 'Email', 'Status'])
            ->setData($users)
            ->setMaxWidth($this->getTerminalWidth());
        
        $this->println($table->render());
        
        return 0;
    }
}
```

## 📈 Future Enhancements

Planned features for future versions:

- **Interactive tables** with sorting and filtering
- **Nested tables** and hierarchical data display
- **Chart integration** (bar charts, sparklines)
- **Export to more formats** (HTML, PDF)
- **Advanced themes** with gradient colors
- **Plugin system** for custom renderers

---

**WebFiori CLI Table Feature** - Professional tabular data display for command-line applications.
