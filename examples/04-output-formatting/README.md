# Output Formatting Example

This example demonstrates comprehensive output formatting and ANSI styling techniques using WebFiori CLI library.

## Features Demonstrated

- ANSI color support (basic, light, background colors)
- Text styling (bold, underlined, combinations)
- Message types with icons (success, error, warning, info)
- Table formatting (simple, styled, aligned)
- Progress indicators (bars, percentages, multi-step)
- Layout techniques (boxes, columns, lists)
- Animations (spinners, bouncing, loading dots)
- Color control and section filtering

## Files

- `main.php` - Application entry point and runner setup
- `FormattingDemoCommand.php` - Comprehensive formatting demonstration

## Usage Examples

### General Help
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
    help:            Display CLI Help. To display help for specific command, use the argument "--command" with this command.
    format-demo:     Demonstrates various output formatting techniques and ANSI styling
```

### Show Format Demo Help
```bash
php main.php help --command=format-demo
```
**Output:**
```
    format-demo:     Demonstrates various output formatting techniques and ANSI styling
    Supported Arguments:
                    --section:[Optional] Show specific section only
                  --no-colors:[Optional] Disable color output
```

## Full Formatting Demonstration

### Complete Demo
```bash
php main.php format-demo
```
**Output:**
```
🎨 WebFiori CLI Formatting Demonstration
========================================

🌈 Color Demonstration

Basic Foreground Colors:
  black text
  red text
  green text
  yellow text
  blue text
  magenta text
  cyan text
  white text

Light Foreground Colors:
  light-red text
  light-green text
  light-yellow text
  light-blue text
  light-magenta text
  light-cyan text

Background Colors:
  Text with red background
  Text with green background
  Text with yellow background
  Text with blue background
  Text with magenta background
  Text with cyan background

Color Combinations:
  Error style
  Success style
  Warning style
  Info style

────────────────────────────────────────────────────────────

✨ Text Styling Demonstration

  Bold text
  Underlined text
  Bold red text
  Underlined blue text
  Bold text with background

Message Types:
✅ Success message
❌ Error message
⚠️  Warning message
ℹ️  Info message

────────────────────────────────────────────────────────────

📊 Table Demonstration

Simple Table:
| Name         | Age          | City         | 
|--------------|--------------|--------------|
| Ahmed Hassan | 30           | Cairo        | 
| Fatima Ali   | 25           | Dubai        | 
| Mohammed Omar| 35           | Riyadh       | 

Styled Table:
┌─────────────┬─────────┬────────────┐
│ Name        │ Age     │ Department │
├─────────────┼─────────┼────────────┤
│ Sara Ahmed  │ 28      │ Engineering │
│ Omar Khalil │ 32      │ Marketing  │
│ Layla Hassan│ 29      │ Design     │
└─────────────┴─────────┴────────────┘

Data Table with Alignment:
┌─────────────────┬──────────────┬──────────┬──────────────┐
│ Product         │ Price        │ Stock    │ Status       │
├─────────────────┼──────────────┼──────────┼──────────────┤
│ Laptop          │ $1,299.99    │ 15       │ In Stock     │
│ Mouse           │ $29.99       │ 150      │ In Stock     │
│ Keyboard        │ $89.99       │ 0        │ Out of Stock │
│ Monitor         │ $399.99      │ 8        │ Low Stock    │
└─────────────────┴──────────────┴──────────┴──────────────┘

────────────────────────────────────────────────────────────

📈 Progress Indicators

Simple Progress Bar:
[████████████████████] Complete!

Percentage Progress:
Progress: [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% Done!

Multi-step Progress:
Step 1/5: Initializing............. ✅
Step 2/5: Loading data............. ✅
Step 3/5: Processing............. ✅
Step 4/5: Validating............. ✅
Step 5/5: Finalizing............. ✅
✅ All steps completed!

────────────────────────────────────────────────────────────

📐 Layout Demonstration

Bordered Box:
┌────────────────────────────────────────┐
│ This is content inside a bordered box! │
│ It can contain multiple lines          │
│ and various formatting.                │
└────────────────────────────────────────┘

Two-Column Layout:
Left Column               │ Right Column
• Item 1                │ → Feature A
• Item 2                │ → Feature B
• Item 3                │ → Feature C
• Item 4                │ → Feature D

Formatted Lists:
Bulleted List:
  • First item
  • Second item
  • Third item with longer text
  • Fourth item

Numbered List:
  1. First item
  2. Second item
  3. Third item with longer text
  4. Fourth item

Checklist:
  ✅ Setup environment
  ✅ Write code
  ⬜ Test application
  ⬜ Deploy to production

────────────────────────────────────────────────────────────

🎬 Animation Demonstration

Spinner Animation:
⠋ Processing... → ✅ Processing complete!

Bouncing Animation:
● (bounces left to right and back)

Loading Dots:
Loading... → Loading complete! ✨

✨ Formatting demonstration completed!
💡 Tip: Use --section=<name> to view specific sections
```

## Section-Specific Demonstrations

### Colors Section
```bash
php main.php format-demo --section=colors
```
**Output:**
```
🎨 WebFiori CLI Formatting Demonstration
========================================

🌈 Color Demonstration

Basic Foreground Colors:
  black text
  red text
  green text
  yellow text
  blue text
  magenta text
  cyan text
  white text

Light Foreground Colors:
  light-red text
  light-green text
  light-yellow text
  light-blue text
  light-magenta text
  light-cyan text

Background Colors:
  Text with red background
  Text with green background
  Text with yellow background
  Text with blue background
  Text with magenta background
  Text with cyan background

Color Combinations:
  Error style
  Success style
  Warning style
  Info style

✨ Formatting demonstration completed!
💡 Tip: Use --section=<name> to view specific sections
```

### Styles Section
```bash
php main.php format-demo --section=styles
```
**Output:**
```
🎨 WebFiori CLI Formatting Demonstration
========================================

✨ Text Styling Demonstration

  Bold text
  Underlined text
  Bold red text
  Underlined blue text
  Bold text with background

Message Types:
✅ Success message
❌ Error message
⚠️  Warning message
ℹ️  Info message

✨ Formatting demonstration completed!
💡 Tip: Use --section=<name> to view specific sections
```

### Tables Section
```bash
php main.php format-demo --section=tables
```
**Output:**
```
🎨 WebFiori CLI Formatting Demonstration
========================================

📊 Table Demonstration

Simple Table:
| Name         | Age          | City         | 
|--------------|--------------|--------------|
| Ahmed Hassan | 30           | Cairo        | 
| Fatima Ali   | 25           | Dubai        | 
| Mohammed Omar| 35           | Riyadh       | 

Styled Table:
┌─────────────┬─────────┬────────────┐
│ Name        │ Age     │ Department │
├─────────────┼─────────┼────────────┤
│ Sara Ahmed  │ 28      │ Engineering │
│ Omar Khalil │ 32      │ Marketing  │
│ Layla Hassan│ 29      │ Design     │
└─────────────┴─────────┴────────────┘

Data Table with Alignment:
┌─────────────────┬──────────────┬──────────┬──────────────┐
│ Product         │ Price        │ Stock    │ Status       │
├─────────────────┼──────────────┼──────────┼──────────────┤
│ Laptop          │ $1,299.99    │ 15       │ In Stock     │
│ Mouse           │ $29.99       │ 150      │ In Stock     │
│ Keyboard        │ $89.99       │ 0        │ Out of Stock │
│ Monitor         │ $399.99      │ 8        │ Low Stock    │
└─────────────────┴──────────────┴──────────┴──────────────┘

✨ Formatting demonstration completed!
💡 Tip: Use --section=<name> to view specific sections
```

### Progress Section
```bash
php main.php format-demo --section=progress
```
**Output:**
```
🎨 WebFiori CLI Formatting Demonstration
========================================

📈 Progress Indicators

Simple Progress Bar:
[████████████████████] Complete!

Percentage Progress:
Progress: [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% Done!

Multi-step Progress:
Step 1/5: Initializing............. ✅
Step 2/5: Loading data............. ✅
Step 3/5: Processing............. ✅
Step 4/5: Validating............. ✅
Step 5/5: Finalizing............. ✅
✅ All steps completed!

✨ Formatting demonstration completed!
💡 Tip: Use --section=<name> to view specific sections
```

### Layouts Section
```bash
php main.php format-demo --section=layouts
```
**Output:**
```
🎨 WebFiori CLI Formatting Demonstration
========================================

📐 Layout Demonstration

Bordered Box:
┌────────────────────────────────────────┐
│ This is content inside a bordered box! │
│ It can contain multiple lines          │
│ and various formatting.                │
└────────────────────────────────────────┘

Two-Column Layout:
Left Column               │ Right Column
• Item 1                │ → Feature A
• Item 2                │ → Feature B
• Item 3                │ → Feature C
• Item 4                │ → Feature D

Formatted Lists:
Bulleted List:
  • First item
  • Second item
  • Third item with longer text
  • Fourth item

Numbered List:
  1. First item
  2. Second item
  3. Third item with longer text
  4. Fourth item

Checklist:
  ✅ Setup environment
  ✅ Write code
  ⬜ Test application
  ⬜ Deploy to production

✨ Formatting demonstration completed!
💡 Tip: Use --section=<name> to view specific sections
```

### Animations Section
```bash
php main.php format-demo --section=animations
```
**Output:**
```
🎨 WebFiori CLI Formatting Demonstration
========================================

🎬 Animation Demonstration

Spinner Animation:
⠋ Processing... → ✅ Processing complete!

Bouncing Animation:
● (bounces left to right and back)

Loading Dots:
Loading... → Loading complete! ✨

✨ Formatting demonstration completed!
💡 Tip: Use --section=<name> to view specific sections
```

## Advanced Options

### Disable Colors
```bash
php main.php format-demo --section=colors --no-colors
```
**Output:**
```
⚠️  Color output disabled

🎨 WebFiori CLI Formatting Demonstration
========================================

🌈 Color Demonstration

Colors disabled - showing plain text versions

Basic Foreground Colors:
  black text
  red text
  green text
  yellow text
  blue text
  magenta text
  cyan text
  white text

[... continues with plain text versions ...]

✨ Formatting demonstration completed!
💡 Tip: Use --section=<name> to view specific sections
```

## Error Handling Examples

### Invalid Section
```bash
php main.php format-demo --section=invalid
```
**Output:**
```
Error: The following argument(s) have invalid values: '--section'
Info: Allowed values for the argument '--section':
colors
styles
tables
progress
layouts
animations
```

### Invalid Command
```bash
php main.php invalid
```
**Output:**
```
Error: The command 'invalid' is not supported.
```

## Key Learning Points

1. **ANSI Colors**: 8 basic + 6 light foreground colors, 6 background colors
2. **Text Styling**: Bold, underlined, and combination formatting
3. **Message Types**: Consistent styling for success, error, warning, info
4. **Table Formatting**: Simple markdown, Unicode box-drawing, data alignment
5. **Progress Indicators**: Visual feedback for long-running operations
6. **Layout Techniques**: Boxes, columns, lists for structured output
7. **Animations**: Dynamic visual elements for better user experience
8. **Color Control**: Ability to disable colors for plain text environments
9. **Section Filtering**: View specific formatting categories
10. **Unicode Support**: Emojis, box-drawing characters, special symbols

## Code Structure Examples

### Format Demo Command Structure
```php
class FormattingDemoCommand extends Command {
    public function __construct() {
        parent::__construct('format-demo', [
            '--section' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::VALUES => ['colors', 'styles', 'tables', 'progress', 'layouts', 'animations'],
                ArgumentOption::DESCRIPTION => 'Show specific section only'
            ],
            '--no-colors' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'Disable color output'
            ]
        ], 'Demonstrates various output formatting techniques and ANSI styling');
    }

    public function exec(): int {
        $section = $this->getArgValue('--section');
        $noColors = $this->isArgProvided('--no-colors');
        
        if ($noColors) {
            $this->warning('⚠️  Color output disabled');
            $this->println();
        }
        
        $this->showHeader();
        
        if ($section) {
            $this->showSection($section, $noColors);
        } else {
            $this->showAllSections($noColors);
        }
        
        $this->showFooter();
        return 0;
    }
}
```

### Animation Implementation
```php
private function showSpinnerAnimation(): void {
    $frames = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
    
    for ($i = 0; $i < 30; $i++) {
        $frame = $frames[$i % count($frames)];
        $this->prints("\r$frame Processing...");
        usleep(100000); // 0.1 seconds
    }
    
    $this->println("\r✅ Processing complete!");
}
```

This example demonstrates professional CLI output formatting suitable for creating visually appealing and user-friendly command-line applications.

## Related Examples

### Prerequisites
- **[01-basic-hello-world](../01-basic-hello-world/)** - Basic command and output concepts

### Enhanced Output Features
- **[06-table-display](../06-table-display/)** - Structured data in formatted tables
- **[07-progress-bars](../07-progress-bars/)** - Professional progress indicators
- **[05-interactive-commands](../05-interactive-commands/)** - Interactive menus with formatting

### Input with Formatting
- **[03-user-input](../03-user-input/)** - User input with formatted prompts
- **[11-masked-input](../11-masked-input/)** - Secure input with visual feedback

### Complete Applications
- **[10-multi-command-app](../10-multi-command-app/)** - Full applications with consistent formatting
- **[09-database-ops](../09-database-ops/)** - Database operations with formatted output
- **[08-file-processing](../08-file-processing/)** - File operations with status formatting

### Development Tools
- **[12-command-scaffolding](../12-command-scaffolding/)** - Generate commands with formatting templates
