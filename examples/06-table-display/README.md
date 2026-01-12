# 📊 Table Display Example

A comprehensive demonstration of the WebFiori CLI Table feature, showcasing professional tabular data display capabilities with various styling options, data formatting, and responsive design.

## 🎯 What This Example Demonstrates

### Core Table Features
- **Multiple table styles** (bordered, simple, minimal, compact, markdown)
- **Column configuration** (width, alignment, formatting)
- **Data type handling** (currency, dates, percentages, booleans)
- **Color themes** (default, dark, light, colorful, professional, minimal)
- **Status-based colorization** (active=green, error=red, warning=yellow)
- **Responsive design** that adapts to terminal width
- **Data export capabilities** (JSON, CSV, arrays)

### Real-World Use Cases
- **User Management** - Display user accounts with status indicators
- **Product Catalogs** - Show inventory with pricing and stock levels
- **Service Monitoring** - System health dashboards with metrics
- **Data Export** - Various output formats for integration
- **Report Generation** - Professional data presentation

## 🚀 Running the Example

### Basic Usage
```bash
# Run all demonstrations
php main.php table-demo

# Show help
php main.php help --command=table-demo
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

# Run all demos
php main.php table-demo --demo=all
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

### Demo Types (`--demo`)
- `users` - User management system with status indicators
- `products` - Product catalog with pricing and inventory
- `services` - Service monitoring dashboard
- `styles` - Showcase of different table styles
- `themes` - Color theme demonstrations
- `export` - Data export format examples
- `all` - Run all demonstrations (default)

### Table Styles (`--style`)
- `bordered` - Unicode box-drawing characters (default)
- `simple` - ASCII characters for maximum compatibility
- `minimal` - Clean look with reduced borders
- `compact` - Space-efficient layout
- `markdown` - Markdown-compatible format

### Color Themes (`--theme`)
- `default` - Standard theme with basic colors
- `dark` - Optimized for dark terminals
- `light` - Optimized for light terminals
- `colorful` - Vibrant colors and styling
- `professional` - Business-appropriate styling
- `minimal` - No colors, just formatting

### Width Control (`--width`)
- `0` - Auto-detect terminal width (default)
- `80` - Fixed 80 character width
- `120` - Fixed 120 character width
- Any positive integer for custom width

## 🎨 Example Output

### User Management Table (Bordered Style)
```
👥 User Management System
                                         User Management Dashboard                                          
┌──────┬─────────────────┬───────────────────────────┬────────────┬──────────────┬──────────┬──────────────┐
│  ID  │ Name            │ Email                     │   Status   │   Created    │   Role   │      Balance │
├──────┼─────────────────┼───────────────────────────┼────────────┼──────────────┼──────────┼──────────────┤
│  1   │ John Doe        │ john.doe@example.com      │   Active   │ Jan 15, 2024 │  Admin   │     1,250.75 │
│  2   │ Jane Smith      │ jane.smith@example.com    │  Inactive  │ Jan 16, 2024 │   User   │       890.50 │
│  3   │ Bob Johnson     │ bob.johnson@example.com   │   Active   │ Jan 17, 2024 │ Manager  │     2,100.00 │
│  4   │ Alice Brown     │ alice.brown@example.com   │  Pending   │ Jan 18, 2024 │   User   │       750.25 │
│  5   │ Charlie Davis   │ charlie.davis@example.com │   Active   │ Jan 19, 2024 │  Admin   │     1,800.80 │
└──────┴─────────────────┴───────────────────────────┴────────────┴──────────────┴──────────┴──────────────┘
```

### Product Catalog (Compact Style)
```
🛍️ Product Catalog
                               Product Inventory                               
  SKU    │Product Name         │     Price │ Stock │  Category   │Featured  │Rating  
────────────────────────────────────────────────────────────────────────────────────
 LAP001  │MacBook Pro 16"      │ $2,499.99 │    15 │Electronics  │ ⭐ Yes  │★ 4.8 
 MOU002  │Wireless Mouse       │    $29.99 │   Out │Accessories  │ ⭐ Yes  │★ 4.2 
 KEY003  │Mechanical Keyboard  │   $149.99 │    25 │Accessories  │ ⭐ Yes  │★ 4.6 
 MON004  │4K Monitor 27"       │   $399.99 │     8 │Electronics  │     No   │★ 4.4 
 HDD005  │External SSD 1TB     │   $199.99 │    50 │  Storage    │ ⭐ Yes  │★ 4.7 
```

### Service Status Monitor (Markdown Style)
```
🔧 Service Status Monitor
                                  System Health Dashboard                                   
------------------------------------------------------------------------------------
| Service        |   Version    |   Status   |   Uptime |   Response |   Memory |  Health  |
|----------------|--------------|------------|----------|------------|----------|----------|
| Web Server     |  nginx/1.20  |  Running   |    99.9% |       45ms |    2.1GB |   ✅    |
| Database       |  MySQL 8.0   |  Running   |    99.8% |       12ms |    4.5GB |   ✅    |
| Cache Server   |  Redis 6.2   |  Stopped   |       0% |        N/A |      0MB |   ❌    |
| API Gateway    |   Kong 3.0   |  Running   |    99.7% |       78ms |    512MB |   ✅    |
| Message Queue  |   RabbitMQ   |  Warning   |    95.2% |      156ms |    1.2GB |  ⚠️  |
| Load Balancer  |   HAProxy    |  Running   |     100% |        5ms |    128MB |   ✅    |
------------------------------------------------------------------------------------
```

### Style Variations Showcase
```
🎨 Table Style Variations

Style: Bordered (Unicode box-drawing characters)
┌────────────────────┬────────────────┬────────────────────┐
│ Item               │ Price          │ Temperature        │
├────────────────────┼────────────────┼────────────────────┤
│ Coffee             │ $3.50          │ Hot                │
│ Tea                │ $2.75          │ Hot                │
│ Juice              │ $4.25          │ Cold               │
└────────────────────┴────────────────┴────────────────────┘

Style: Simple (ASCII characters for compatibility)
+--------------------+----------------+--------------------+
| Item               | Price          | Temperature        |
+--------------------+----------------+--------------------+
| Coffee             | $3.50          | Hot                |
| Tea                | $2.75          | Hot                |
| Juice              | $4.25          | Cold               |
+--------------------+----------------+--------------------+

Style: Minimal (Clean look with minimal borders)
 Item                  Price            Temperature         
──────────────────────────────────────────────────────────
 Coffee                $3.50            Hot                 
 Tea                   $2.75            Hot                 
 Juice                 $4.25            Cold                
```

## 🧪 Test Scenarios

### 1. All Demos
```bash
php main.php table-demo --demo=all
# Shows: users, products, services, styles, themes, export
```

### 2. Style Combinations
```bash
php main.php table-demo --demo=users --style=minimal --theme=dark
php main.php table-demo --demo=products --style=compact --theme=colorful
php main.php table-demo --demo=services --style=markdown --theme=professional
```

### 3. Width Testing
```bash
php main.php table-demo --demo=users --width=80
php main.php table-demo --demo=products --width=120
php main.php table-demo --demo=services --width=100
```

### 4. Individual Demos
```bash
php main.php table-demo --demo=users
php main.php table-demo --demo=products  
php main.php table-demo --demo=services
php main.php table-demo --demo=styles
php main.php table-demo --demo=themes
php main.php table-demo --demo=export
```

### 5. Help and Documentation
```bash
php main.php help --command=table-demo
php main.php table-demo --help
```

## 💡 Key Features Demonstrated

### 1. Data Formatting
- **Currency**: `$2,499.99` with proper formatting
- **Dates**: `Jan 15, 2024` human-readable format
- **Percentages**: `99.9%` with decimal precision
- **Status Indicators**: Color-coded status values
- **Boolean Values**: `⭐ Yes` / `No` with icons

### 2. Visual Enhancements
- **Color Coding**: Green=Active, Red=Inactive, Yellow=Warning
- **Icons and Emojis**: ✅❌⚠️⭐★ for visual clarity
- **Column Alignment**: Left, right, center alignment
- **Text Truncation**: Long emails and names handled gracefully

### 3. Responsive Design
- **Auto-width Detection**: Adapts to terminal size
- **Column Prioritization**: Important columns stay visible
- **Overflow Handling**: Graceful text truncation
- **Mobile-friendly**: Works on narrow terminals

### 4. Export Capabilities
- **JSON Format**: Structured data export
- **CSV Format**: Spreadsheet compatibility
- **Array Format**: PHP data structures
- **Associative Arrays**: Key-value pair export

## 🔧 Technical Implementation

### Core Classes Used
- `TableDemoCommand`: Main command class
- `TableBuilder`: Table construction and configuration
- `TableTheme`: Color theme management
- `Column`: Individual column configuration
- `TableData`: Data handling and export

### Key Methods
- `createUsersTable()`: User management demo
- `createProductsTable()`: Product catalog demo
- `createServicesTable()`: Service monitoring demo
- `demonstrateStyles()`: Style variations showcase
- `demonstrateThemes()`: Color theme examples

### Configuration Options
- Column width and alignment control
- Status-based colorization rules
- Data formatting functions
- Theme and style selection
- Responsive width management

## 🎯 Best Practices Demonstrated

### 1. User Experience
- Clear visual hierarchy with headers and colors
- Consistent data formatting across columns
- Meaningful status indicators and icons
- Responsive design for different screen sizes

### 2. Data Presentation
- Appropriate column widths for content
- Status-based color coding for quick scanning
- Currency and date formatting for readability
- Truncation handling for long text

### 3. Performance
- Efficient rendering for large datasets
- Memory-conscious data handling
- Fast column width calculations
- Optimized ANSI color usage

### 4. Accessibility
- High contrast color options
- ASCII fallbacks for compatibility
- Clear visual separation between elements
- Support for different terminal capabilities

## 🔗 Related Examples

- **[04-output-formatting](../04-output-formatting/)** - ANSI colors and formatting
- **[05-interactive-commands](../05-interactive-commands/)** - Interactive menu systems
- **[08-file-processing](../08-file-processing/)** - File data processing
- **[10-multi-command-app](../10-multi-command-app/)** - Complete CLI applications

## Related Examples

### Prerequisites
- **[01-basic-hello-world](../01-basic-hello-world/)** - Basic command structure
- **[04-output-formatting](../04-output-formatting/)** - Colors and formatting basics

### Enhanced Display Features
- **[07-progress-bars](../07-progress-bars/)** - Visual progress indicators
- **[05-interactive-commands](../05-interactive-commands/)** - Interactive menus with tables

### Data Sources
- **[08-file-processing](../08-file-processing/)** - Process files and display results in tables
- **[09-database-ops](../09-database-ops/)** - Database queries with table output
- **[03-user-input](../03-user-input/)** - Collect data and display in tables

### Complete Applications
- **[10-multi-command-app](../10-multi-command-app/)** - Full applications with data display
- **[02-arguments-and-options](../02-arguments-and-options/)** - Commands with formatted output

### Development Tools
- **[12-command-scaffolding](../12-command-scaffolding/)** - Generate commands with table display
