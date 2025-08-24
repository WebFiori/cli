# Progress Bars Example

This example demonstrates the comprehensive progress bar system in WebFiori CLI, showcasing various styles, formats, and use cases.

## 🎯 What You'll Learn

- Creating and customizing progress bars
- Different progress bar styles and formats
- Real-time progress tracking
- Integration with file operations
- Multi-step progress workflows
- Performance monitoring with progress bars

## 📁 Files

- `ProgressDemoCommand.php` - Comprehensive progress bar demonstrations
- `FileProcessorCommand.php` - File processing with progress tracking
- `DownloadSimulatorCommand.php` - Download simulation with detailed progress
- `BatchProcessorCommand.php` - Batch operations with multiple progress bars
- `main.php` - Application entry point
- `README.md` - This documentation

## 🚀 Running the Examples

### Progress Demo
```bash
# Show all progress bar styles
php main.php progress-demo

# Specific style demonstration
php main.php progress-demo --style=ascii --items=20

# Quick demo with fewer items
php main.php progress-demo --items=10 --delay=50
```

### File Processor
```bash
# Process sample files
php main.php file-processor

# Process with specific directory
php main.php file-processor --directory=./sample-files --pattern="*.txt"
```

### Download Simulator
```bash
# Simulate file downloads
php main.php download-sim

# Custom download simulation
php main.php download-sim --files=5 --size=large --speed=slow
```

### Batch Processor
```bash
# Run batch operations
php main.php batch-processor

# Custom batch size
php main.php batch-processor --batch-size=50 --operations=3
```

## 📖 Code Explanation

### Basic Progress Bar Usage

#### Simple Progress Bar
```php
$progressBar = $this->createProgressBar(100);
$progressBar->start('Processing...');

for ($i = 0; $i < 100; $i++) {
    // Do work
    $progressBar->advance();
    usleep(50000);
}

$progressBar->finish('Complete!');
```

#### Custom Style and Format
```php
$progressBar = $this->createProgressBar(100)
    ->setStyle(ProgressBarStyle::ASCII)
    ->setFormat('[{bar}] {percent}% ({current}/{total}) ETA: {eta}')
    ->setWidth(50);
```

### Advanced Features

#### Progress Bar with Helper Method
```php
$this->withProgressBar($items, function($item, $index) {
    // Process each item
    $this->processItem($item);
}, 'Processing items...');
```

#### Manual Progress Control
```php
$progressBar = $this->createProgressBar(100);
$progressBar->start();

$progressBar->setCurrent(25);  // Jump to 25%
$progressBar->advance(10);     // Advance by 10
$progressBar->finish();
```

#### Multiple Progress Bars
```php
$mainProgress = $this->createProgressBar($totalTasks);
$subProgress = $this->createProgressBar(100);

foreach ($tasks as $task) {
    $subProgress->start("Processing $task");
    // ... sub-task processing
    $subProgress->finish();
    $mainProgress->advance();
}
```

## 🔍 Key Features

### 1. Progress Bar Styles
- **Default**: Unicode block characters (█░)
- **ASCII**: Compatible characters (=->)
- **Dots**: Dot characters (●○)
- **Arrow**: Arrow characters (▶▷)
- **Custom**: User-defined characters

### 2. Format Templates
- **Basic**: `[{bar}] {percent}% ({current}/{total})`
- **ETA**: Includes estimated time remaining
- **Rate**: Shows processing speed
- **Verbose**: All metrics included
- **Memory**: Includes memory usage

### 3. Real-world Applications
- **File processing**: Track file operations
- **Downloads**: Monitor transfer progress
- **Batch operations**: Multi-step workflows
- **Data processing**: Large dataset handling
- **Installation**: Setup progress tracking

## 🎨 Expected Output

### Style Demonstrations
```
Default Style:
[████████████████████░░░░░░░░░░░░░░░░░░░░] 50.0% (50/100)

ASCII Style:
[===========>---------] 55.0% (55/100) ETA: 00:05

Dots Style:
[●●●●●●●●●●○○○○○○○○○○] 50.0% (50/100) 12.5/s

Arrow Style:
[▶▶▶▶▶▶▶▶▷▷▷▷▷▷▷▷▷▷▷▷] 40.0% (40/100)
```

### File Processing Example
```
📁 Processing Files...

Scanning directory: ./sample-files
Found 25 files to process

Processing files: [████████████████████] 100.0% (25/25) Complete!

📊 Processing Summary:
   • Files processed: 25
   • Total size: 2.3 MB
   • Processing time: 00:12
   • Average speed: 2.1 files/sec
```

### Download Simulation
```
🌐 Download Simulator

Downloading file1.zip (10.5 MB)
[████████████████████████████████████████████████████] 100.0% 
Speed: 2.1 MB/s | ETA: 00:00 | Elapsed: 00:05

Downloading file2.pdf (5.2 MB)  
[██████████████████████████░░░░░░░░░░░░░░░░░░░░░░░░░░░░] 52.0%
Speed: 1.8 MB/s | ETA: 00:03 | Elapsed: 00:02

✅ All downloads completed!
Total downloaded: 45.7 MB in 00:23
```

### Batch Processing
```
🔄 Batch Processor

Batch 1/3: Data Validation
[████████████████████████████████████████████████████] 100.0% (100/100)

Batch 2/3: Data Transformation  
[████████████████████████████████████████████████████] 100.0% (100/100)

Batch 3/3: Data Export
[████████████████████████████████████████████████████] 100.0% (100/100)

🎉 All batches completed successfully!
Total items processed: 300
Total time: 00:45
```

## 🔗 Next Steps

After mastering this example, move on to:
- **[10-multi-command-app](../10-multi-command-app/)** - Building complete CLI applications
- **[13-database-cli](../13-database-cli/)** - Database management with progress tracking

## 💡 Try This

Experiment with the code:

1. **Create custom progress styles**: Design your own progress characters
2. **Add sound effects**: Beep on completion (where supported)
3. **Network progress**: Real HTTP download progress
4. **Nested progress**: Progress bars within progress bars

```php
// Example: Custom progress style with emojis
$customStyle = new ProgressBarStyle('🟩', '⬜', '🟨');
$progressBar->setStyle($customStyle);

// Example: Progress with custom format
$progressBar->setFormat('🚀 {message} [{bar}] {percent}% | ⚡ {rate}/s | ⏱️  {eta}');
```
