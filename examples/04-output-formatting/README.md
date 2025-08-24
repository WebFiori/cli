# Output Formatting Example

This example demonstrates advanced output formatting, ANSI colors, styling, and visual elements in WebFiori CLI.

## 🎯 What You'll Learn

- ANSI color codes and text styling
- Creating tables and formatted layouts
- Progress bars and visual indicators
- Custom formatting functions
- Terminal cursor manipulation
- Creating beautiful CLI interfaces

## 📁 Files

- `FormattingDemoCommand.php` - Comprehensive formatting demonstrations
- `TableCommand.php` - Table creation and formatting
- `DashboardCommand.php` - Real-time dashboard simulation
- `main.php` - Application entry point
- `README.md` - This documentation

## 🚀 Running the Examples

### Formatting Demo
```bash
# Show all formatting options
php main.php format-demo

# Show specific sections
php main.php format-demo --section=colors
php main.php format-demo --section=tables
php main.php format-demo --section=progress
```

### Table Command
```bash
# Display sample data table
php main.php table

# Custom table with data
php main.php table --data=users
php main.php table --data=sales --format=compact
```

### Dashboard Command
```bash
# Show real-time dashboard
php main.php dashboard

# Dashboard with specific refresh rate
php main.php dashboard --refresh=2
```

## 📖 Code Explanation

### ANSI Color Codes

#### Basic Colors
```php
// Foreground colors
$this->prints("Red text", ['color' => 'red']);
$this->prints("Green text", ['color' => 'green']);
$this->prints("Blue text", ['color' => 'blue']);

// Background colors
$this->prints("Text with background", ['bg-color' => 'yellow']);
```

#### Text Styles
```php
// Bold text
$this->prints("Bold text", ['bold' => true]);

// Underlined text
$this->prints("Underlined text", ['underline' => true]);

// Blinking text (if supported)
$this->prints("Blinking text", ['blink' => true]);
```

### Table Formatting

#### Simple Table
```php
private function createTable(array $headers, array $rows): void {
    $this->printTableHeader($headers);
    foreach ($rows as $row) {
        $this->printTableRow($row);
    }
}
```

#### Styled Table
```php
private function printStyledTable(array $data): void {
    // Header with background
    $this->prints("┌", ['color' => 'blue']);
    // ... table drawing logic
}
```

### Progress Indicators

#### Simple Progress Bar
```php
private function showProgress(int $total): void {
    for ($i = 0; $i <= $total; $i++) {
        $percent = ($i / $total) * 100;
        $bar = str_repeat('█', (int)($percent / 5));
        $empty = str_repeat('░', 20 - (int)($percent / 5));
        
        $this->prints("\r[$bar$empty] " . number_format($percent, 1) . "%");
        usleep(100000);
    }
}
```

#### Spinner Animation
```php
private function showSpinner(int $duration): void {
    $chars = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
    $start = time();
    
    while (time() - $start < $duration) {
        foreach ($chars as $char) {
            $this->prints("\r$char Processing...");
            usleep(100000);
        }
    }
}
```

## 🔍 Key Features

### 1. Color System
- **16 basic colors**: Standard ANSI colors
- **256 colors**: Extended color palette
- **RGB colors**: True color support (where available)
- **Background colors**: Text highlighting
- **Color combinations**: Foreground + background

### 2. Text Styling
- **Bold**: Emphasized text
- **Italic**: Slanted text (limited support)
- **Underline**: Underlined text
- **Strikethrough**: Crossed-out text
- **Reverse**: Inverted colors
- **Dim**: Faded text

### 3. Layout Elements
- **Tables**: Structured data display
- **Boxes**: Bordered content areas
- **Lists**: Bulleted and numbered lists
- **Columns**: Multi-column layouts
- **Separators**: Visual dividers

### 4. Interactive Elements
- **Progress bars**: Task completion indicators
- **Spinners**: Loading animations
- **Counters**: Real-time value updates
- **Meters**: Gauge-style indicators
- **Status indicators**: Success/error/warning states

## 🎨 Expected Output

### Color Demo
```
🎨 Color Demonstration:
   Red text in red
   Green text in green
   Blue text in blue
   Yellow background text
   Bold red text
   Underlined blue text
```

### Table Example
```
┌─────────────┬─────────┬────────────┬─────────┐
│ Name        │ Age     │ Department │ Salary  │
├─────────────┼─────────┼────────────┼─────────┤
│ John Doe    │ 30      │ IT         │ $75,000 │
│ Jane Smith  │ 28      │ Marketing  │ $65,000 │
│ Bob Johnson │ 35      │ Sales      │ $80,000 │
└─────────────┴─────────┴────────────┴─────────┘
```

### Progress Bar Example
```
Processing files...
[████████████████████] 100.0% (50/50) Complete!

⠋ Loading data...
⠙ Loading data...
⠹ Loading data...
✅ Data loaded successfully!
```

### Dashboard Example
```
╔══════════════════════════════════════════════════════════╗
║                    System Dashboard                      ║
╠══════════════════════════════════════════════════════════╣
║ CPU Usage:    [████████░░] 80%                          ║
║ Memory:       [██████░░░░] 60%                          ║
║ Disk Space:   [███░░░░░░░] 30%                          ║
║ Network:      [██████████] 100%                         ║
║                                                          ║
║ Active Users: 1,234                                     ║
║ Requests/sec: 45                                        ║
║ Uptime:       2d 14h 32m                                ║
╚══════════════════════════════════════════════════════════╝
```

## 🔗 Next Steps

After mastering this example, move on to:
- **[05-interactive-commands](../05-interactive-commands/)** - Complex interactive workflows
- **[07-progress-bars](../07-progress-bars/)** - Advanced progress indicators
- **[10-multi-command-app](../10-multi-command-app/)** - Building complete CLI applications

## 💡 Try This

Experiment with the code:

1. **Create custom themes**: Define color schemes for different contexts
2. **Add animations**: Create smooth transitions and effects
3. **Build charts**: ASCII bar charts and graphs
4. **Design layouts**: Complex multi-panel interfaces

```php
// Example: Custom color theme
private function applyTheme(string $theme): array {
    return match($theme) {
        'dark' => ['bg-color' => 'black', 'color' => 'white'],
        'ocean' => ['bg-color' => 'blue', 'color' => 'cyan'],
        'forest' => ['bg-color' => 'green', 'color' => 'light-green'],
        default => []
    };
}
```
