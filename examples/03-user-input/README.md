# User Input Example

This example demonstrates interactive user input handling, validation, and different input methods in WebFiori CLI.

## 🎯 What You'll Learn

- Interactive input collection with prompts
- Input validation and custom validators
- Different input types (text, numbers, selections, confirmations)
- Password input handling
- Multi-step interactive workflows
- Error handling and retry mechanisms

## 📁 Files

- `SurveyCommand.php` - Interactive survey with various input types
- `SetupWizardCommand.php` - Multi-step configuration wizard
- `QuizCommand.php` - Interactive quiz with scoring
- `main.php` - Application entry point
- `README.md` - This documentation

## 🚀 Running the Examples

### Survey Command
```bash
# Start interactive survey
php main.php survey

# Survey with pre-filled name
php main.php survey --name="John Doe"
```

### Setup Wizard
```bash
# Run configuration wizard
php main.php setup

# Skip to specific step
php main.php setup --step=database
```

### Quiz Command
```bash
# Start the quiz
php main.php quiz

# Quiz with specific difficulty
php main.php quiz --difficulty=hard
```

## 📖 Code Explanation

### Input Methods Demonstrated

#### Basic Text Input
```php
$name = $this->getInput('Enter your name: ', 'Anonymous');
```

#### Validated Input
```php
$email = $this->getInput('Enter email: ', null, new InputValidator(function($input) {
    return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
}, 'Please enter a valid email address'));
```

#### Numeric Input
```php
$age = $this->readInteger('Enter your age: ', 25);
$score = $this->readFloat('Enter score: ', 0.0);
```

#### Selection Input
```php
$choice = $this->select('Choose your favorite color:', [
    'Red', 'Green', 'Blue', 'Yellow'
], 0); // Default to first option
```

#### Confirmation Input
```php
$confirmed = $this->confirm('Do you want to continue?', true);
```

#### Password Input (Simulated)
```php
$password = $this->getInput('Enter password: ');
// Note: Real password input would hide characters
```

### Custom Validation Examples

#### Email Validation
```php
new InputValidator(function($input) {
    return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
}, 'Invalid email format')
```

#### Range Validation
```php
new InputValidator(function($input) {
    $num = (int)$input;
    return $num >= 1 && $num <= 10;
}, 'Please enter a number between 1 and 10')
```

#### Pattern Validation
```php
new InputValidator(function($input) {
    return preg_match('/^[A-Za-z\s]+$/', $input);
}, 'Only letters and spaces allowed')
```

## 🔍 Key Features

### 1. Input Types
- **Text input**: Names, descriptions, free text
- **Numeric input**: Integers, floats with validation
- **Selection input**: Choose from predefined options
- **Boolean input**: Yes/no confirmations
- **Validated input**: Custom validation rules

### 2. Validation Strategies
- **Built-in validators**: Email, numeric, etc.
- **Custom validators**: Business logic validation
- **Range validation**: Min/max values
- **Pattern matching**: Regex validation
- **Retry mechanisms**: Allow user to correct input

### 3. User Experience
- **Default values**: Sensible defaults for quick input
- **Clear prompts**: Descriptive input requests
- **Error messages**: Helpful validation feedback
- **Progress indication**: Multi-step workflow progress
- **Confirmation steps**: Verify important actions

## 🎨 Expected Output

### Survey Example
```
📋 Welcome to the Interactive Survey!

👤 What's your name? [Anonymous]: John Doe
📧 Enter your email: john@example.com
🎂 How old are you? [25]: 30
🌍 Select your country:
0: United States
1: Canada
2: United Kingdom
3: Australia
Your choice [0]: 1

✅ Thank you for completing the survey!

📊 Survey Results:
   • Name: John Doe
   • Email: john@example.com
   • Age: 30
   • Country: Canada
```

### Setup Wizard Example
```
🔧 Application Setup Wizard

Step 1/4: Basic Configuration
📝 Application name [MyApp]: AwesomeApp
🌐 Environment (dev/staging/prod) [dev]: prod

Step 2/4: Database Configuration
🗄️  Database host [localhost]: db.example.com
👤 Database username: admin
🔑 Database password: ********

✅ Setup completed successfully!
```

### Quiz Example
```
🧠 Welcome to the Knowledge Quiz!

Question 1/5: What is the capital of France?
0: London
1: Berlin
2: Paris
3: Madrid
Your answer: 2
✅ Correct!

Question 2/5: What is 15 + 27?
Enter your answer: 42
✅ Correct!

🎉 Quiz completed!
📊 Final Score: 5/5 (100%)
🏆 Excellent work!
```

## 🔗 Next Steps

After mastering this example, move on to:
- **[04-output-formatting](../04-output-formatting/)** - Advanced output styling
- **[05-interactive-commands](../05-interactive-commands/)** - Complex interactive workflows
- **[07-progress-bars](../07-progress-bars/)** - Visual progress indicators

## 💡 Try This

Experiment with the code:

1. **Add new input types**: Date input, URL validation
2. **Create complex workflows**: Multi-branch decision trees
3. **Add input history**: Remember previous inputs
4. **Implement autocomplete**: Suggest completions for input

```php
// Example: Date input validation
new InputValidator(function($input) {
    $date = DateTime::createFromFormat('Y-m-d', $input);
    return $date && $date->format('Y-m-d') === $input;
}, 'Please enter date in YYYY-MM-DD format')
```
