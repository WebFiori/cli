# Progress Bars Example

This example demonstrates the comprehensive progress bar system in WebFiori CLI, showcasing various styles, formats, and real-time progress tracking capabilities.

## 🎯 What You'll Learn

- Creating and customizing progress bars with different styles
- Real-time progress tracking and updates
- Progress bar formats and display options
- Performance monitoring with rate calculations
- Integration with long-running operations
- Error handling and validation

## 📁 Files

- `ProgressDemoCommand.php` - Comprehensive progress bar demonstrations
- `main.php` - Application entry point
- `README.md` - This documentation

## 🚀 Running the Example

### Basic Usage
```bash
# Show all progress bar styles
php main.php progress-demo

# Show help
php main.php help --command=progress-demo
```

### Style Demonstrations
```bash
# All styles demonstration
php main.php progress-demo --style=all --items=20 --delay=50

# Individual styles
php main.php progress-demo --style=default --items=10 --delay=100
php main.php progress-demo --style=ascii --items=50 --delay=20
php main.php progress-demo --style=dots --items=15 --delay=80
php main.php progress-demo --style=arrow --items=25 --delay=40
php main.php progress-demo --style=custom --items=12 --delay=150
```

### Format Options
```bash
# Different format templates
php main.php progress-demo --style=dots --format=eta --items=15
php main.php progress-demo --style=arrow --format=rate --items=25
php main.php progress-demo --style=custom --format=verbose --items=12
```

### Performance Testing
```bash
# Quick demo (minimum items)
php main.php progress-demo --style=default --items=10 --delay=50

# Longer demo (more items)
php main.php progress-demo --style=ascii --items=100 --delay=10

# Slow demo (longer delays)
php main.php progress-demo --style=dots --items=20 --delay=200
```

## 📋 Available Options

### Styles (`--style`)
- `default` - Unicode block characters (█░) - Modern terminals
- `ascii` - ASCII characters (=->) - Maximum compatibility
- `dots` - Circular dots (●○) - Clean appearance
- `arrow` - Directional arrows (▶▷) - Visual flow indication
- `custom` - Emoji style (🟩⬜) - Modern and colorful
- `all` - Demonstrate all styles sequentially

### Parameters
- `--items` - Number of items to process (10-1000, default: 50)
- `--delay` - Delay between items in milliseconds (default: 100)
- `--format` - Progress bar format template (eta, rate, verbose)

### Validation Rules
- Items must be between 10 and 1000
- Delay can be any positive integer
- Invalid values show helpful error messages

## 🎨 Example Output

### All Styles Demonstration
```
🎯 Progress Bar Demonstration
=============================

📊 Demo Configuration:
   • Style: All styles
   • Items: 20
   • Delay: 50ms per item
   • Estimated time: 1 seconds

🎨 Default Style (Unicode)
Processing with default style... [████████████████████████████████████████] 100.0% (20/20)
Complete! [████████████████████████████████████████] 100.0% (20/20)

🎨 ASCII Style (Compatible)
Processing with ascii style... [========================================] 100.0% (20/20)
Complete! [========================================] 100.0% (20/20)

🎨 Dots Style (Circular)
Processing with dots style... [●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●] 100.0% (20/20)
Complete! [●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●] 100.0% (20/20)

🎨 Arrow Style (Directional)
Processing with arrow style... [▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶] 100.0% (20/20)
Complete! [▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶] 100.0% (20/20)

🎨 Custom Style (Emoji)
Processing with emoji style... 🚀 {message} [🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩] 100.0% | ⚡ 20/s | ⏱️  00:00
🎉 Emoji processing complete! 🚀 {message} [🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩] 100.0% | ⚡ 20/s | ⏱️  00:00

✨ Progress bar demonstration completed!
```

### Individual Style Examples

#### Default Style (Unicode)
```
Processing with default style... [████████████████████░░░░░░░░░░░░░░░░░░░░] 50.0% (5/10)
Processing with default style... [████████████████████████████████████████] 100.0% (10/10)
Complete! [████████████████████████████████████████] 100.0% (10/10)
```

#### ASCII Style (Compatible)
```
Processing with ascii style... [====================--------------------] 50.0% (25/50)
Processing with ascii style... [========================================] 100.0% (50/50)
Complete! [========================================] 100.0% (50/50)
```

#### Dots Style (Circular)
```
Processing with dots style... [●●●●●●●●●●●●●●●○○○○○○○○○○○○○○○○○○○○○○○○○] 40.0% (6/15) ETA: 00:00
Processing with dots style... [●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●] 100.0% (15/15) ETA: 00:00
Complete! [●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●] 100.0% (15/15) ETA: 00:00
```

#### Arrow Style (Directional)
```
Processing with arrow style... [▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▷▷▷▷▷▷▷▷▷▷▷▷▷▷▷▷▷▷▷▷▷▷▷▷] 40.0% (10/25) 25/s
Processing with arrow style... [▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶] 100.0% (25/25) 25/s
Complete! [▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶▶] 100.0% (25/25) 25/s
```

#### Custom Style (Emoji with Verbose Format)
```
Processing with emoji style... 🚀 {message} [🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜] 50.0% | ⚡ 6.6/s | ⏱️  00:00
Processing with emoji style... 🚀 {message} [🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩] 100.0% | ⚡ 6.6/s | ⏱️  00:00
🎉 Emoji processing complete! 🚀 {message} [🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩🟩] 100.0% | ⚡ 6.6/s | ⏱️  00:00
```

### Error Handling
```
# Invalid item count (too low)
php main.php progress-demo --items=5
Error: Number of items must be between 10 and 1000

# Invalid item count (too high)  
php main.php progress-demo --items=1001
Error: Number of items must be between 10 and 1000
```

## 🧪 Test Scenarios

### 1. All Styles Demo
```bash
php main.php progress-demo --style=all --items=20 --delay=50
# Shows all 5 styles in sequence with consistent parameters
```

### 2. Performance Comparison
```bash
# Fast processing
php main.php progress-demo --style=ascii --items=50 --delay=20

# Medium processing  
php main.php progress-demo --style=default --items=25 --delay=100

# Slow processing
php main.php progress-demo --style=custom --items=12 --delay=150
```

### 3. Format Testing
```bash
# ETA format
php main.php progress-demo --style=dots --format=eta --items=15

# Rate format
php main.php progress-demo --style=arrow --format=rate --items=25

# Verbose format
php main.php progress-demo --style=custom --format=verbose --items=12
```

### 4. Edge Cases
```bash
# Minimum items
php main.php progress-demo --style=default --items=10 --delay=50

# Maximum items (test with caution - takes time)
php main.php progress-demo --style=ascii --items=1000 --delay=1

# Boundary validation
php main.php progress-demo --items=9    # Error: too low
php main.php progress-demo --items=1001 # Error: too high
```

### 5. Style Comparison
```bash
# Unicode vs ASCII compatibility
php main.php progress-demo --style=default --items=20 --delay=50
php main.php progress-demo --style=ascii --items=20 --delay=50

# Visual styles comparison
php main.php progress-demo --style=dots --items=20 --delay=50
php main.php progress-demo --style=arrow --items=20 --delay=50
php main.php progress-demo --style=custom --items=20 --delay=50
```

## 💡 Key Features Demonstrated

### 1. Real-Time Updates
- **Live Progress**: Updates show in real-time as work progresses
- **Percentage Display**: Current completion percentage
- **Item Counters**: Current/total item counts
- **Rate Calculation**: Items processed per second
- **ETA Estimation**: Estimated time to completion

### 2. Visual Styles
- **Unicode Blocks**: Modern terminals with full block characters
- **ASCII Compatible**: Works on all terminal types
- **Dot Indicators**: Clean circular progress indicators
- **Arrow Flow**: Directional progress indication
- **Emoji Style**: Modern colorful progress with emojis

### 3. Format Templates
- **Basic Format**: `[bar] percentage (current/total)`
- **ETA Format**: Includes estimated time remaining
- **Rate Format**: Shows processing speed
- **Verbose Format**: All metrics with emojis and timing

### 4. Performance Metrics
- **Processing Rate**: Items per second calculation
- **Time Tracking**: Elapsed and estimated time
- **Progress Percentage**: Accurate completion percentage
- **Item Counting**: Current and total item tracking

## 🔧 Technical Implementation

### Core Classes Used
- `ProgressDemoCommand`: Main demonstration command
- `ProgressBarFormat`: Format template definitions
- `ProgressBar`: Core progress bar functionality
- `ArgumentOption`: Command argument configuration

### Key Methods
- `demonstrateStyle()`: Individual style demonstrations
- `createProgressBar()`: Progress bar creation and setup
- `simulateWork()`: Work simulation with delays
- `validateParameters()`: Input validation and error handling

### Configuration Options
- Style selection and character definitions
- Format template customization
- Timing and delay controls
- Item count validation and limits

## 🎯 Best Practices Demonstrated

### 1. User Experience
- Clear visual progress indication
- Consistent formatting across styles
- Helpful error messages for invalid input
- Estimated completion times

### 2. Performance
- Efficient real-time updates
- Minimal CPU overhead during updates
- Accurate rate calculations
- Responsive progress tracking

### 3. Compatibility
- ASCII fallback for older terminals
- Unicode support for modern terminals
- Cross-platform character support
- Terminal width adaptation

### 4. Validation
- Input parameter validation
- Helpful error messages
- Boundary checking (10-1000 items)
- Type validation for arguments

## 🔗 Related Examples

- **[04-output-formatting](../04-output-formatting/)** - ANSI colors and formatting
- **[06-table-display](../06-table-display/)** - Data presentation techniques
- **[08-file-processing](../08-file-processing/)** - File operations with progress
- **[10-multi-command-app](../10-multi-command-app/)** - Complete CLI applications
## Related Examples

### Prerequisites
- **[01-basic-hello-world](../01-basic-hello-world/)** - Basic command structure
- **[04-output-formatting](../04-output-formatting/)** - Colors and formatting

### Visual Enhancement
- **[06-table-display](../06-table-display/)** - Structured data display
- **[05-interactive-commands](../05-interactive-commands/)** - Interactive workflows with progress

### Long-Running Operations
- **[08-file-processing](../08-file-processing/)** - File operations with progress tracking
- **[09-database-ops](../09-database-ops/)** - Database operations with progress indicators
- **[03-user-input](../03-user-input/)** - Multi-step processes with progress

### Complete Applications
- **[10-multi-command-app](../10-multi-command-app/)** - Full applications with progress feedback
- **[02-arguments-and-options](../02-arguments-and-options/)** - Commands with progress reporting

### Development Tools
- **[12-command-scaffolding](../12-command-scaffolding/)** - Generate commands with progress bars
