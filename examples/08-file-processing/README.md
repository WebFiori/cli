# File Processing Example

This example demonstrates file processing capabilities in WebFiori CLI, showcasing text file operations, statistics calculation, and content manipulation.

## 🎯 What You'll Learn

- Reading and processing text files
- File content analysis and statistics
- Text transformation operations
- Error handling for file operations
- Command argument validation
- File existence and accessibility checks

## 📁 Files

- `app.php` - Main file processing command implementation
- `sample.txt` - Sample text file for testing
- `README.md` - This documentation

## 🚀 Running the Example

### Basic Usage
```bash
# Process sample file with default action (count)
php app.php process-file --file=sample.txt

# Show help
php app.php help --command=process-file
```

### File Statistics (Count Action)
```bash
# Count lines, words, and characters
php app.php process-file --file=sample.txt --action=count

# Default action is count (can be omitted)
php app.php process-file --file=sample.txt
```

### Text Transformation
```bash
# Convert to uppercase
php app.php process-file --file=sample.txt --action=uppercase

# Reverse line order
php app.php process-file --file=sample.txt --action=reverse
```

## 📋 Available Options

### Actions (`--action`)
- `count` - Display file statistics (lines, words, characters) - **Default**
- `uppercase` - Convert all text to uppercase
- `reverse` - Reverse the order of lines in the file

### Parameters
- `--file` - Path to the file to process (**Required**)
- `--action` - Action to perform (optional, defaults to `count`)

### Validation Rules
- File path is required
- File must exist and be readable
- Action must be one of: count, uppercase, reverse
- Invalid actions show available options

## 🎨 Example Output

### File Statistics (Count Action)
```bash
php app.php process-file --file=sample.txt --action=count
```
```
File Statistics for: sample.txt
Lines: 5
Words: 14
Characters: 82
```

### Uppercase Transformation
```bash
php app.php process-file --file=sample.txt --action=uppercase
```
```
Uppercase content:
HELLO WORLD
THIS IS A SAMPLE FILE
FOR TESTING FILE PROCESSING
WITH MULTIPLE LINES
```

### Line Reversal
```bash
php app.php process-file --file=sample.txt --action=reverse
```
```
Reversed content:

With multiple lines
For testing file processing
This is a sample file
Hello World
```

### Error Handling Examples

#### File Not Found
```bash
php app.php process-file --file=nonexistent.txt --action=count
```
```
Error: File not found: nonexistent.txt
```

#### Missing Required Argument
```bash
php app.php process-file --action=count
```
```
Error: The following required argument(s) are missing: '--file'
```

#### Invalid Action
```bash
php app.php process-file --file=sample.txt --action=invalid
```
```
Error: The following argument(s) have invalid values: '--action'
Info: Allowed values for the argument '--action':
count
uppercase
reverse
```

## 🧪 Test Scenarios

### 1. Basic File Operations
```bash
# Test all actions on sample file
php app.php process-file --file=sample.txt --action=count
php app.php process-file --file=sample.txt --action=uppercase
php app.php process-file --file=sample.txt --action=reverse
```

### 2. Different File Types
```bash
# Create test files
echo -e "Line 1\nLine 2\nLine 3" > test1.txt
echo "Single line file" > test2.txt
touch empty.txt

# Test with different content
php app.php process-file --file=test1.txt --action=count
php app.php process-file --file=test2.txt --action=count
php app.php process-file --file=empty.txt --action=count
```

### 3. Large File Processing
```bash
# Create large file
for i in {1..100}; do echo "Line $i with some content"; done > large.txt

# Process large file
php app.php process-file --file=large.txt --action=count
```

### 4. Error Cases
```bash
# Test error handling
php app.php process-file --file=nonexistent.txt --action=count
php app.php process-file --action=count
php app.php process-file --file=sample.txt --action=invalid
```

### 5. Edge Cases
```bash
# Test with special files
echo "   " > spaces.txt                    # Only spaces
echo -e "\x00\x01\x02" > binary.txt       # Binary content
mkdir testdir                              # Directory instead of file

php app.php process-file --file=spaces.txt --action=count
php app.php process-file --file=binary.txt --action=count
php app.php process-file --file=testdir --action=count  # Shows warning
```

## 💡 Key Features Demonstrated

### 1. File Operations
- **File Reading**: Safe file content reading with error handling
- **File Validation**: Check file existence and accessibility
- **Content Processing**: Line-by-line and full content processing
- **Statistics Calculation**: Lines, words, and character counting

### 2. Text Processing
- **Case Conversion**: Transform text to uppercase
- **Line Manipulation**: Reverse line order in files
- **Content Analysis**: Word and character counting
- **Encoding Handling**: Process various text encodings

### 3. Error Handling
- **File Not Found**: Clear error messages for missing files
- **Invalid Arguments**: Validation with helpful suggestions
- **Required Parameters**: Check for mandatory arguments
- **File Access Issues**: Handle permission and directory errors

### 4. User Experience
- **Clear Output**: Well-formatted results with labels
- **Help Integration**: Built-in help command support
- **Validation Messages**: Helpful error messages with suggestions
- **Default Values**: Sensible defaults for optional parameters

## 🔧 Technical Implementation

### Core Functionality
```php
class FileProcessCommand extends Command {
    public function __construct() {
        parent::__construct('process-file', [
            '--file' => [
                ArgumentOption::OPTIONAL => false,
                ArgumentOption::DESCRIPTION => 'Path to the file to process'
            ],
            '--action' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => 'count',
                ArgumentOption::VALUES => ['count', 'uppercase', 'reverse'],
                ArgumentOption::DESCRIPTION => 'Action to perform'
            ]
        ], 'Process text files in various ways');
    }
}
```

### File Processing Methods
- `validateFile()`: Check file existence and readability
- `countStatistics()`: Calculate lines, words, characters
- `transformContent()`: Apply text transformations
- `handleErrors()`: Provide meaningful error messages

### Statistics Calculation
- **Lines**: Count newline characters + 1
- **Words**: Split by whitespace and count non-empty elements
- **Characters**: Total byte count including whitespace and newlines

## 🎯 Best Practices Demonstrated

### 1. Input Validation
- Required parameter checking
- File existence validation
- Action value validation with allowed options
- Clear error messages for invalid input

### 2. File Handling
- Safe file reading with error checking
- Proper handling of empty files
- Binary file detection and handling
- Directory vs file differentiation

### 3. User Experience
- Consistent output formatting
- Helpful error messages
- Default parameter values
- Comprehensive help documentation

### 4. Error Recovery
- Graceful handling of missing files
- Clear validation error messages
- Suggestions for valid parameter values
- Non-zero exit codes for errors

## 🔗 Related Examples

- **[03-user-input](../03-user-input/)** - Input validation and handling
- **[04-output-formatting](../04-output-formatting/)** - Text formatting and colors
- **[07-progress-bars](../07-progress-bars/)** - Progress tracking for file operations
- **[10-multi-command-app](../10-multi-command-app/)** - Complete CLI applications
