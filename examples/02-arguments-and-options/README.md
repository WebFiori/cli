# Arguments and Options Example

This example demonstrates advanced argument handling, validation, and complex command logic using WebFiori CLI library.

## Features Demonstrated

- Required and optional arguments
- Argument validation with allowed values
- Custom validation logic (email, age ranges)
- Boolean flags
- Default values
- Precision control
- Verbose output modes
- Error handling and validation messages

## Files

- `main.php` - Application entry point and runner setup
- `CalculatorCommand.php` - Mathematical calculator with multiple operations
- `UserProfileCommand.php` - User profile creator with validation

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
    help:        Display CLI Help. To display help for specific command, use the argument "--command" with this command.
    calc:        Performs mathematical calculations on a list of numbers
    profile:     Creates a user profile with validation and formatting
```

## Calculator Command Examples

### Show Calculator Help
```bash
php main.php help --command=calc
```
**Output:**
```
    calc:        Performs mathematical calculations on a list of numbers
    Supported Arguments:
                  --operation: Mathematical operation to perform
                    --numbers: Comma-separated list of numbers (e.g., "1,2,3,4")
                  --precision:[Optional][Default = '2'] Number of decimal places for the result
                    --verbose:[Optional] Show detailed calculation steps
```

### Basic Operations

#### Addition
```bash
php main.php calc --numbers=1,2,3,4,5 --operation=add
```
**Output:**
```
✅ Performing add on: 1, 2, 3, 4, 5
📊 Result: 15.00
```

#### Subtraction
```bash
php main.php calc --numbers=10,3,2 --operation=subtract
```
**Output:**
```
✅ Performing subtract on: 10, 3, 2
📊 Result: 5.00
```

#### Multiplication
```bash
php main.php calc --numbers=2,3,4 --operation=multiply
```
**Output:**
```
✅ Performing multiply on: 2, 3, 4
📊 Result: 24.00
```

#### Division
```bash
php main.php calc --numbers=100,5,2 --operation=divide
```
**Output:**
```
✅ Performing divide on: 100, 5, 2
📊 Result: 10.00
```

#### Average
```bash
php main.php calc --numbers=10,20,30,40,50 --operation=average
```
**Output:**
```
✅ Performing average on: 10, 20, 30, 40, 50
📊 Result: 30.00
```

### Advanced Calculator Features

#### Custom Precision
```bash
php main.php calc --numbers=10,3 --operation=divide --precision=4
```
**Output:**
```
✅ Performing divide on: 10, 3
📊 Result: 3.3333
```

#### Verbose Mode
```bash
php main.php calc --numbers=5,10,15 --operation=add --verbose
```
**Output:**
```
🔢 Operation: Add
📊 Numbers: 5, 10, 15
🎯 Precision: 2 decimal places

✅ Performing add on: 5, 10, 15
📊 Result: 30.00

📈 Statistics:
   • Count: 3
   • Min: 5
   • Max: 15
   • Average: 10.00
```

### Calculator Error Handling

#### Invalid Operation
```bash
php main.php calc --numbers=1,2,3 --operation=invalid
```
**Output:**
```
Error: The following argument(s) have invalid values: '--operation'
Info: Allowed values for the argument '--operation':
add
subtract
multiply
divide
average
```

#### Missing Required Arguments
```bash
php main.php calc --numbers=1,2,3
```
**Output:**
```
Error: The following required argument(s) are missing: '--operation'
```

#### Division by Zero
```bash
php main.php calc --numbers=10,0 --operation=divide
```
**Output:**
```
❌ Calculation error: Division by zero is not allowed
```

## Profile Command Examples

### Show Profile Help
```bash
php main.php help --command=profile
```
**Output:**
```
    profile:     Creates a user profile with validation and formatting
    Supported Arguments:
                       --name: User full name (required)
                      --email: User email address (required)
                        --age: User age (13-120, required)
                       --role:[Optional][Default = 'user'] User role in the system
                 --department:[Optional][Default = 'General'] User department
                     --active:[Optional] Mark user as active (flag)
                     --skills:[Optional] Comma-separated list of skills
                        --bio:[Optional] Short biography (max 200 characters)
```

### Basic Profile Creation
```bash
php main.php profile --name="Ahmed Hassan" --email=ahmed@example.com --age=28
```
**Output:**
```
🔧 Creating User Profile...

✅ User Profile Created Successfully!

👤 Name: Ahmed Hassan
📧 Email: ahmed@example.com
🎂 Age: 28
👔 Role: user
🏢 Department: General
🔴 Status: inactive

💾 Saving profile to database...
✅ Profile saved successfully! User ID: 5404
📊 Profile Summary:
   • User ID: 5404
   • Role: User
   • Skills: 0
   • Status: Inactive
```

### Full Profile with All Options
```bash
php main.php profile --name="Fatima Al-Zahra" --email=fatima@example.com --age=25 --role=admin --department=Engineering --active --skills="PHP,JavaScript,Python" --bio="Senior developer with 5 years experience"
```
**Output:**
```
🔧 Creating User Profile...

✅ User Profile Created Successfully!

👤 Name: Fatima Al-Zahra
📧 Email: fatima@example.com
🎂 Age: 25
👔 Role: admin
🏢 Department: Engineering
🟢 Status: active
🛠️  Skills: PHP, JavaScript, Python
📝 Bio: Senior developer with 5 years experience

💾 Saving profile to database...
✅ Profile saved successfully! User ID: 2958
📊 Profile Summary:
   • User ID: 2958
   • Role: Admin
   • Skills: 3
   • Status: Active
```

### Profile Validation Examples

#### Invalid Email
```bash
php main.php profile --name="Mohammed Ali" --email=invalid-email --age=30
```
**Output:**
```
🔧 Creating User Profile...

❌ Invalid email format: invalid-email
```

#### Invalid Age Range
```bash
php main.php profile --name="Sara Ahmed" --email=sara@example.com --age=150
```
**Output:**
```
🔧 Creating User Profile...

❌ Age must be between 13 and 120, got: 150
```

#### Missing Required Arguments
```bash
php main.php profile --name="Omar Hassan"
```
**Output:**
```
Error: The following required argument(s) are missing: '--email', '--age'
```

## Key Learning Points

1. **Required vs Optional Arguments**: Use `ArgumentOption::OPTIONAL => false` for required fields
2. **Argument Validation**: Use `ArgumentOption::VALUES` array to restrict allowed values
3. **Default Values**: Set defaults with `ArgumentOption::DEFAULT`
4. **Boolean Flags**: Arguments without values act as boolean flags
5. **Custom Validation**: Implement business logic validation in `exec()` method
6. **Error Handling**: Return appropriate exit codes (0 = success, 1+ = error)
7. **User Feedback**: Use `success()`, `error()`, `info()` for colored output
8. **Complex Logic**: Commands can perform multiple operations and validations

## Code Structure Examples

### Calculator Command Structure
```php
class CalculatorCommand extends Command {
    public function __construct() {
        parent::__construct('calc', [
            '--operation' => [
                ArgumentOption::OPTIONAL => false,
                ArgumentOption::VALUES => ['add', 'subtract', 'multiply', 'divide', 'average'],
                ArgumentOption::DESCRIPTION => 'Mathematical operation to perform'
            ],
            '--numbers' => [
                ArgumentOption::OPTIONAL => false,
                ArgumentOption::DESCRIPTION => 'Comma-separated list of numbers'
            ],
            '--precision' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => '2',
                ArgumentOption::DESCRIPTION => 'Number of decimal places'
            ],
            '--verbose' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'Show detailed calculation steps'
            ]
        ], 'Performs mathematical calculations on a list of numbers');
    }
}
```

### Profile Command Structure
```php
class UserProfileCommand extends Command {
    public function __construct() {
        parent::__construct('profile', [
            '--name' => [
                ArgumentOption::OPTIONAL => false,
                ArgumentOption::DESCRIPTION => 'User full name (required)'
            ],
            '--email' => [
                ArgumentOption::OPTIONAL => false,
                ArgumentOption::DESCRIPTION => 'User email address (required)'
            ],
            '--age' => [
                ArgumentOption::OPTIONAL => false,
                ArgumentOption::DESCRIPTION => 'User age (13-120, required)'
            ],
            '--active' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'Mark user as active (flag)'
            ]
        ], 'Creates a user profile with validation and formatting');
    }
}
```

This example demonstrates advanced CLI application development with proper validation, error handling, and user experience design.
