# Arguments and Options Example

This example demonstrates comprehensive argument and option handling in WebFiori CLI commands.

## 🎯 What You'll Learn

- Different types of arguments (required, optional, with defaults)
- Argument validation and constraints
- Working with multiple data types
- Argument value processing
- Error handling for invalid arguments

## 📁 Files

- `CalculatorCommand.php` - Mathematical calculator with various argument types
- `UserProfileCommand.php` - User profile creator with validation
- `main.php` - Application entry point
- `README.md` - This documentation

## 🚀 Running the Examples

### Calculator Command
```bash
# Basic addition
php main.php calc --operation=add --numbers="5,10,15"

# Division with precision
php main.php calc --operation=divide --numbers="22,7" --precision=3

# Get help for calculator
php main.php help --command-name=calc
```

### User Profile Command
```bash
# Create a user profile
php main.php profile --name="John Doe" --email="john@example.com" --age=30

# With optional fields
php main.php profile --name="Jane Smith" --email="jane@example.com" --age=25 --role=admin --active

# Get help for profile
php main.php help --command-name=profile
```

## 📖 Code Explanation

### Argument Types Demonstrated

#### Required Arguments
```php
'--name' => [
    Option::DESCRIPTION => 'User full name',
    Option::OPTIONAL => false  // Required argument
]
```

#### Optional Arguments with Defaults
```php
'--precision' => [
    Option::DESCRIPTION => 'Decimal precision for results',
    Option::OPTIONAL => true,
    Option::DEFAULT => '2'
]
```

#### Arguments with Value Constraints
```php
'--operation' => [
    Option::DESCRIPTION => 'Mathematical operation to perform',
    Option::OPTIONAL => false,
    Option::VALUES => ['add', 'subtract', 'multiply', 'divide', 'average']
]
```

#### Boolean Flags
```php
'--active' => [
    Option::DESCRIPTION => 'Mark user as active',
    Option::OPTIONAL => true
    // No default value = boolean flag
]
```

### Validation Patterns

#### Email Validation
```php
private function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
```

#### Number List Processing
```php
private function parseNumbers(string $numbers): array {
    $nums = array_map('trim', explode(',', $numbers));
    return array_map('floatval', array_filter($nums, 'is_numeric'));
}
```

#### Age Range Validation
```php
private function validateAge(int $age): bool {
    return $age >= 13 && $age <= 120;
}
```

## 🔍 Key Features

### 1. Data Type Handling
- **Strings**: Names, emails, descriptions
- **Numbers**: Integers, floats, calculations
- **Booleans**: Flags and switches
- **Arrays**: Comma-separated values

### 2. Validation Strategies
- **Format validation**: Email, phone, etc.
- **Range validation**: Age, scores, etc.
- **Enum validation**: Predefined choices
- **Custom validation**: Business logic

### 3. Error Handling
- **Missing required arguments**
- **Invalid argument values**
- **Type conversion errors**
- **Business rule violations**

## 🎨 Expected Output

### Calculator Examples
```
$ php main.php calc --operation=add --numbers="5,10,15"
✅ Performing addition on: 5, 10, 15
📊 Result: 30.00

$ php main.php calc --operation=divide --numbers="22,7" --precision=4
✅ Performing division on: 22, 7
📊 Result: 3.1429
```

### Profile Examples
```
$ php main.php profile --name="John Doe" --email="john@example.com" --age=30
✅ User Profile Created Successfully!

👤 Name: John Doe
📧 Email: john@example.com
🎂 Age: 30
👔 Role: user
🟢 Status: inactive
```

### Error Examples
```
$ php main.php calc --operation=invalid --numbers="5,10"
❌ Error: Invalid operation 'invalid'. Must be one of: add, subtract, multiply, divide, average

$ php main.php profile --name="John" --email="invalid-email" --age=30
❌ Error: Invalid email format: invalid-email
```

## 🔗 Next Steps

After mastering this example, move on to:
- **[03-user-input](../03-user-input/)** - Interactive input and validation
- **[04-output-formatting](../04-output-formatting/)** - Advanced output styling
- **[05-interactive-commands](../05-interactive-commands/)** - Building interactive workflows

## 💡 Try This

Experiment with the code:

1. **Add new operations**: Implement power, modulo, or factorial
2. **Enhanced validation**: Add phone number or URL validation
3. **Complex data types**: Handle JSON or CSV input
4. **Argument dependencies**: Make some arguments depend on others

```php
// Example: Add power operation
case 'power':
    if (count($numbers) !== 2) {
        $this->error('Power operation requires exactly 2 numbers (base, exponent)');
        return 1;
    }
    $result = pow($numbers[0], $numbers[1]);
    break;
```
