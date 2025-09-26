# User Input Example

This example demonstrates comprehensive user input handling and validation techniques using WebFiori CLI library.

## Features Demonstrated

- Interactive input collection with defaults
- Input validation and error handling
- Email format validation
- Age range validation (13-120)
- Country selection from numbered lists
- Programming language selection with y/N prompts
- Experience level selection
- Survey summary and statistics
- Pre-filled values and quick mode options

## Files

- `main.php` - Application entry point and runner setup
- `SurveyCommand.php` - Interactive survey with comprehensive input handling
- `SimpleCommand.php` - Non-interactive demo survey

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
    help:              Display CLI Help. To display help for specific command, use the argument "--command" with this command.
    survey:            Interactive survey demonstrating various input methods
    simple-survey:     A simple survey without interactive input
```

## Simple Survey Command

### Show Simple Survey Help
```bash
php main.php help --command=simple-survey
```
**Output:**
```
    simple-survey:     A simple survey without interactive input
```

### Run Simple Survey Demo
```bash
php main.php simple-survey
```
**Output:**
```
📋 Simple Survey Demo
====================

✅ Survey completed! Here's your data:

Name: John Doe
Email: john@example.com
Age: 30
Country: Canada
Languages: PHP, Python
Experience: Advanced
```

## Interactive Survey Command

### Show Survey Help
```bash
php main.php help --command=survey
```
**Output:**
```
    survey:            Interactive survey demonstrating various input methods
    Supported Arguments:
                       --name:[Optional] Pre-fill your name (optional)
                      --quick:[Optional] Use quick mode with minimal questions
```

### Basic Interactive Survey
```bash
php main.php survey
```
**Sample Output:**
```
📋 Welcome to the Interactive Survey!
=====================================

📝 Basic Information
-------------------
👤 What's your name? Enter = 'Anonymous'
📧 Enter your email:
🎂 How old are you? Enter = '25'

🎯 Preferences
-------------
🌍 Select your country:
0: United States
1: Canada
2: United Kingdom
3: Australia
4: Germany
5: France
6: Japan
7: Other
Enter number (0-7) Enter = '0'

💻 Programming experience:
Do you know PHP? (y/N) Enter = 'n'
Do you know JavaScript? (y/N) Enter = 'n'
Do you know Python? (y/N) Enter = 'n'
Do you know Java? (y/N) Enter = 'n'
Do you know C++? (y/N) Enter = 'n'
Do you know Go? (y/N) Enter = 'n'
Do you know Rust? (y/N) Enter = 'n'

📈 Your programming experience level:
0: Beginner
1: Intermediate
2: Advanced
3: Expert
Enter number (0-3) Enter = '1'

📋 Additional Details
--------------------
🎨 What's your favorite color? Enter = 'Blue'
⭐ Rate your satisfaction with CLI tools (1-10): Enter = '7'
💬 Any additional feedback? (optional): Enter = ''
📧 Subscribe to our newsletter?(y/N)

📊 Survey Summary
================
👤 Name: Anonymous
📧 Email: user@example.com
🎂 Age: 25
🌍 Country: United States
📈 Experience: Intermediate
💻 Languages: None specified
🎨 Favorite Color: Blue
⭐ Satisfaction: 7/10 ⭐⭐⭐⭐⭐⭐⭐☆☆☆
📧 Newsletter: No

Submit this survey?(Y/n)
📤 Submitting survey...
...
✅ Thank you for completing the survey!
📋 Survey ID: SRV-20250926-1234

📈 Quick Stats:
   • Questions answered: 9
   • Languages known: 0
   • Completion time: ~5 minutes
```

### Survey with Pre-filled Name
```bash
php main.php survey --name="Ahmed Hassan"
```
**Sample Output:**
```
📋 Welcome to the Interactive Survey!
=====================================

📝 Basic Information
-------------------
👤 What's your name? Enter = 'Ahmed Hassan'
📧 Enter your email:
🎂 How old are you? Enter = '25'

[... continues with survey flow ...]

📊 Survey Summary
================
👤 Name: Ahmed Hassan
📧 Email: ahmed@example.com
🎂 Age: 25
🌍 Country: Canada
📈 Experience: Advanced
💻 Languages: PHP, JavaScript, Python
🎨 Favorite Color: Blue
⭐ Satisfaction: 9/10 ⭐⭐⭐⭐⭐⭐⭐⭐⭐☆
📧 Newsletter: No

✅ Thank you for completing the survey!
📋 Survey ID: SRV-20250926-3555

📈 Quick Stats:
   • Questions answered: 9
   • Languages known: 3
   • Completion time: ~3 minutes
🎉 Great to hear you're satisfied with CLI tools!
```

### Quick Mode Survey
```bash
php main.php survey --quick
```
**Sample Output:**
```
📋 Welcome to the Interactive Survey!
=====================================

⚡ Running in quick mode - fewer questions!

📝 Basic Information
-------------------
👤 What's your name? Enter = 'Anonymous'
📧 Enter your email:
🎂 How old are you? Enter = '25'

🎯 Preferences
-------------
🌍 Select your country:
[... country selection ...]

💻 Programming experience:
[... language selection ...]

📈 Your programming experience level:
[... experience selection ...]

📊 Survey Summary
================
👤 Name: Anonymous
📧 Email: user@example.com
🎂 Age: 25
🌍 Country: United States
📈 Experience: Intermediate
💻 Languages: None specified

✅ Thank you for completing the survey!
📋 Survey ID: SRV-20250926-1364

📈 Quick Stats:
   • Questions answered: 6
   • Languages known: 0
   • Completion time: ~5 minutes
```

### Combined Options
```bash
php main.php survey --name="Fatima Al-Zahra" --quick
```
**Sample Output:**
```
📋 Welcome to the Interactive Survey!
=====================================

⚡ Running in quick mode - fewer questions!

📝 Basic Information
-------------------
👤 What's your name? Enter = 'Fatima Al-Zahra'
[... continues with quick survey flow ...]

📊 Survey Summary
================
👤 Name: Fatima Al-Zahra
📧 Email: fatima@example.com
🎂 Age: 25
🌍 Country: United States
📈 Experience: Intermediate
💻 Languages: None specified

✅ Thank you for completing the survey!
📋 Survey ID: SRV-20250926-1871

📈 Quick Stats:
   • Questions answered: 6
   • Languages known: 0
   • Completion time: ~3 minutes
```

## Error Handling Examples

### Invalid Command
```bash
php main.php invalid
```
**Output:**
```
Error: The command 'invalid' is not supported.
```

### Input Validation
The survey includes several validation mechanisms:

- **Email validation**: Prompts for valid email format
- **Age validation**: Ensures age is between 13-120
- **Country selection**: Validates numeric input within range
- **Experience level**: Validates numeric input for experience level

## Key Learning Points

1. **Interactive Input**: Use `getInput()` for collecting user data with defaults
2. **Input Validation**: Implement custom validation logic for business rules
3. **User Experience**: Provide clear prompts, defaults, and error messages
4. **Data Collection**: Structure complex surveys with multiple sections
5. **Conditional Logic**: Use flags like `--quick` to modify behavior
6. **Pre-filled Data**: Use command arguments to pre-populate fields
7. **Summary Display**: Format collected data in readable summaries
8. **Progress Feedback**: Show completion statistics and survey IDs

## Code Structure Examples

### Survey Command Structure
```php
class SurveyCommand extends Command {
    public function __construct() {
        parent::__construct('survey', [
            '--name' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'Pre-fill your name (optional)'
            ],
            '--quick' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'Use quick mode with minimal questions'
            ]
        ], 'Interactive survey demonstrating various input methods');
    }

    public function exec(): int {
        $this->println('📋 Welcome to the Interactive Survey!');
        
        // Collect basic information
        $this->collectBasicInfo();
        
        // Collect preferences
        $this->collectPreferences();
        
        // Show summary and submit
        $this->showSummaryAndSubmit();
        
        return 0;
    }
}
```

### Input Collection with Validation
```php
private function collectBasicInfo() {
    // Name with pre-fill option
    $preFillName = $this->getArgValue('--name');
    $name = $this->getInput('👤 What\'s your name?', $preFillName ?? 'Anonymous');
    
    // Email with validation
    do {
        $email = $this->getInput('📧 Enter your email:');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Please enter a valid email address');
        }
    } while (!filter_var($email, FILTER_VALIDATE_EMAIL));
    
    // Age with validation
    $age = $this->getInput('🎂 How old are you?', '25');
    $age = is_numeric($age) ? (int)$age : 25;
}
```

## Technical Notes

- **Interactive Limitations**: The survey works best in interactive mode; piped input may cause issues with the underlying input handling system
- **Alternative Approach**: The `simple-survey` command provides a non-interactive demonstration
- **Input Validation**: Multiple validation layers ensure data quality
- **User Experience**: Rich formatting with emojis and clear section divisions

This example demonstrates advanced user input handling suitable for complex CLI applications requiring data collection and validation.
