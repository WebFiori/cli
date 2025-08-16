# 📊 Example 15: Table Display

A comprehensive demonstration of the WebFiori CLI Table feature, showcasing professional tabular data display capabilities with various styling options, data formatting, and responsive design.

## 🎯 What This Example Demonstrates

### Core Table Features
- **Multiple table styles** (bordered, simple, minimal, compact, markdown)
- **Column configuration** (width, alignment, formatting)
- **Data type handling** (currency, dates, percentages, booleans)
- **Color themes** (default, dark, colorful, professional)
- **Status-based colorization** (active=green, error=red, etc.)
- **Responsive design** that adapts to terminal width

### Real-World Use Cases
- **User Management** - Display user accounts with status indicators
- **Product Catalogs** - Show inventory with pricing and stock levels
- **Service Monitoring** - System health dashboards with metrics
- **Data Export** - Various output formats for integration

## 🚀 Running the Example

### Basic Usage
```bash
# Run all demonstrations
php main.php table-demo

# Show help
php main.php help --command-name=table-demo
```

### Specific Demonstrations
```bash
# User management table
php main.php table-demo --demo=users

# Product catalog
php main.php table-demo --demo=products

# Service status monitoring
php main.php table-demo --demo=services

# Table style variations
php main.php table-demo --demo=styles

# Color theme showcase
php main.php table-demo --demo=themes

# Data export capabilities
php main.php table-demo --demo=export
```

### Customization Options
```bash
# Use different table style
php main.php table-demo --demo=users --style=simple

# Apply color theme
php main.php table-demo --demo=products --theme=colorful

# Set custom width
php main.php table-demo --demo=services --width=100

# Combine options
php main.php table-demo --demo=users --style=bordered --theme=professional --width=120
```

## 📋 Available Options

### Demo Types
- `users` - User management system with status indicators
- `products` - Product catalog with pricing and inventory
- `services` - Service monitoring dashboard
- `styles` - Showcase of different table styles
- `themes` - Color theme demonstrations
- `export` - Data export format examples
- `all` - Run all demonstrations (default)

### Table Styles
- `bordered` - Unicode box-drawing characters (default)
- `simple` - ASCII characters for maximum compatibility
- `minimal` - Clean look with reduced borders
- `compact` - Space-efficient layout
- `markdown` - Markdown-compatible format

### Color Themes
- `default` - Standard theme with basic colors
- `dark` - Optimized for dark terminals
- `light` - Optimized for light terminals
- `colorful` - Vibrant colors and styling
- `professional` - Business-appropriate styling
- `minimal` - No colors, just formatting

## 🎨 Example Output

### User Management Table
```
User Management Dashboard
┌────┬───────────────┬─────────────────────────┬──────────┬────────────┬────────┬────────────┐
│ ID │ Name          │ Email                   │ Status   │ Created    │ Role   │ Balance    │
├────┼───────────────┼─────────────────────────┼──────────┼────────────┼────────┼────────────┤
│ 1  │ John Doe      │ john.doe@example.com    │ Active   │ Jan 15, 24 │ Admin  │ $1,250.75  │
│ 2  │ Jane Smith    │ jane.smith@example.com  │ Inactive │ Jan 16, 24 │ User   │ $890.50    │
│ 3  │ Bob Johnson   │ bob.johnson@example.com │ Active   │ Jan 17, 24 │ Manager│ $2,100.00  │
└────┴───────────────┴─────────────────────────┴──────────┴────────────┴────────┴────────────┘
```

### Service Status Monitor
```
System Health Dashboard
┌──────────────┬────────────┬──────────┬────────┬──────────┬────────┬────────┐
│ Service      │ Version    │ Status   │ Uptime │ Response │ Memory │ Health │
├──────────────┼────────────┼──────────┼────────┼──────────┼────────┼────────┤
│ Web Server   │ nginx/1.20 │ Running  │  99.9% │     45ms │ 2.1GB  │   ✅   │
│ Database     │ MySQL 8.0  │ Running  │  99.8% │     12ms │ 4.5GB  │   ✅   │
│ Cache Server │ Redis 6.2  │ Stopped  │     0% │      N/A │   0MB  │   ❌   │
└──────────────┴────────────┴──────────┴────────┴──────────┴────────┴────────┘
```

## 💡 Key Features Demonstrated

### 1. Column Configuration
```php
->configureColumn('Price', [
    'width' => 10,
    'align' => 'right',
    'formatter' => fn($value) => '$' . number_format($value, 2)
])
```

### 2. Status-Based Colorization
```php
->colorizeColumn('Status', function($value) {
    return match(strtolower($value)) {
        'active' => ['color' => 'green', 'bold' => true],
        'inactive' => ['color' => 'red', 'bold' => true],
        'pending' => ['color' => 'yellow', 'bold' => true],
        default => []
    };
})
```

### 3. Data Formatting
```php
->configureColumn('Created', [
    'formatter' => fn($date) => date('M j, Y', strtotime($date))
])
```

### 4. Responsive Design
```php
->setMaxWidth($terminalWidth)
->configureColumn('Email', ['truncate' => true])
```

## 🔧 Integration Examples

### In a CLI Command
```php
use WebFiori\Cli\CLICommand;
use WebFiori\Cli\Table\TableBuilder;

class ListUsersCommand extends CLICommand {
    public function exec(): int {
        $users = $this->getUsersFromDatabase();
        
        $table = TableBuilder::create()
            ->setHeaders(['ID', 'Name', 'Email', 'Status'])
            ->setData($users)
            ->colorizeColumn('Status', function($value) {
                return match(strtolower($value)) {
                    'active' => ['color' => 'green'],
                    'inactive' => ['color' => 'red'],
                    default => []
                };
            });
        
        echo $table->render();
        return 0;
    }
}
```

### With Database Results
```php
// Fetch data from database
$results = $pdo->query("SELECT id, name, email, status FROM users")->fetchAll();

// Display in table
$table = TableBuilder::create()
    ->setHeaders(['ID', 'Name', 'Email', 'Status'])
    ->setData($results)
    ->setMaxWidth(100);

echo $table->render();
```

### Export Data
```php
use WebFiori\Cli\Table\TableData;

$data = new TableData($headers, $rows);

// Export to JSON
file_put_contents('users.json', $data->toJson(true));

// Export to CSV
file_put_contents('users.csv', $data->toCsv(true));
```

## 🎯 Best Practices Shown

### 1. Responsive Design
- Auto-detect terminal width
- Configure column truncation for long content
- Use appropriate column widths

### 2. User Experience
- Clear status indicators with colors
- Consistent data formatting
- Meaningful column headers

### 3. Performance
- Efficient rendering for large datasets
- Memory-conscious data handling
- Fast column width calculations

### 4. Accessibility
- High contrast color options
- ASCII fallbacks for compatibility
- Clear visual hierarchy

## 🔗 Related Examples

After mastering this example, explore:
- **[10-multi-command-app](../10-multi-command-app/)** - Complete CLI application architecture
- **[04-output-formatting](../04-output-formatting/)** - ANSI colors and formatting
- **[13-database-cli](../13-database-cli/)** - Database management tools

## 📚 Additional Resources

- **Table Documentation**: `WebFiori/Cli/Table/README.md`
- **WebFiori CLI Guide**: Main project documentation
- **ANSI Color Reference**: Terminal color codes and compatibility

---

This example demonstrates the full power of the WebFiori CLI Table feature, showing how to create professional, responsive, and visually appealing data displays for command-line applications.
