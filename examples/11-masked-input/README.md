# Masked Input Example

This example demonstrates the **masked input functionality** in WebFiori CLI, which allows secure entry of sensitive data like passwords, PINs, and tokens.

## Features Demonstrated

- **Basic Password Input**: Default asterisk (*) masking with validation
- **Custom Mask Characters**: Use different characters (•, #, X, -) for masking
- **Input Validation**: Enforce security requirements and format validation
- **Default Values**: Optional default values for sensitive fields
- **Confirmation Prompts**: Verify critical inputs by asking twice

## Running the Example

### Basic Usage
```bash
php main.php secure-input
```

### Run Specific Demos
```bash
# Password demo only
php main.php secure-input --demo=password

# PIN demo with custom mask
php main.php secure-input --demo=pin

# Token demo with default value
php main.php secure-input --demo=token

# All demos (default)
php main.php secure-input --demo=all
```

## Code Examples

### Basic Masked Input
```php
// Simple password input with default * masking
$password = $this->getMaskedInput('Enter password: ');
```

### Custom Mask Character
```php
// Use # characters for PIN masking
$pin = $this->getMaskedInput('Enter PIN: ', null, null, '#');
```

### With Validation
```php
$validator = new InputValidator(function($password) {
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[0-9]/', $password);
}, 'Password must be 8+ chars with uppercase and number!');

$password = $this->getMaskedInput('Password: ', null, $validator);
```

### With Default Value
```php
// Provide a default token value
$token = $this->getMaskedInput('API Token: ', 'default-token', null, '•');
```

## Method Signature

```php
public function getMaskedInput(
    string $prompt,                    // The prompt to display
    ?string $default = null,           // Optional default value
    ?InputValidator $validator = null, // Optional input validator
    string $mask = '*'                 // Mask character (default: *)
): ?string
```

## Security Features

### Input Masking
- Characters are masked as you type
- Only mask characters are displayed in terminal
- Actual input is captured securely
- Supports backspace for corrections

### Validation Support
- Enforce minimum length requirements
- Validate character patterns (uppercase, numbers, symbols)
- Custom validation logic
- Automatic retry on validation failure

### Safe Handling
- Input is trimmed automatically
- Empty prompts return null safely
- Works with existing stream abstraction
- Compatible with testing framework

## Use Cases

### 1. User Authentication
```php
$password = $this->getMaskedInput('Login Password: ');
$confirmPassword = $this->getMaskedInput('Confirm Password: ');

if ($password !== $confirmPassword) {
    $this->error('Passwords do not match!');
    return 1;
}
```

### 2. API Configuration
```php
$apiKey = $this->getMaskedInput('API Key: ', null, null, '•');
$secret = $this->getMaskedInput('API Secret: ', null, null, '-');
```

### 3. Database Setup
```php
$dbPassword = $this->getMaskedInput('Database Password: ');

$validator = new InputValidator(function($host) {
    return filter_var($host, FILTER_VALIDATE_IP) || 
           filter_var($host, FILTER_VALIDATE_DOMAIN);
}, 'Invalid host format!');

$dbHost = $this->getInput('Database Host: ', 'localhost', $validator);
```

### 4. Secure Token Entry
```php
$jwtSecret = $this->getMaskedInput('JWT Secret: ', null, 
    new InputValidator(function($secret) {
        return strlen($secret) >= 32;
    }, 'JWT secret must be at least 32 characters!')
);
```

## Interactive Demo Features

The example includes several interactive demonstrations:

1. **Password Demo**: Shows validation with security requirements
2. **PIN Demo**: Demonstrates custom mask characters (#)
3. **Token Demo**: Shows default values with bullet (•) masking
4. **Advanced Demo**: Multiple scenarios including confirmation prompts

## Testing

The masked input functionality is fully testable using the existing `CommandTestCase` framework:

```php
$output = $this->executeSingleCommand($command, [], ['secret123']);
$this->assertContains('Password received: secret123', $output);
```

## Best Practices

1. **Always validate sensitive input** for security requirements
2. **Use appropriate mask characters** for different data types
3. **Implement confirmation prompts** for critical operations
4. **Never log or display** the actual sensitive values
5. **Provide clear error messages** for validation failures

---

**Ready to secure your CLI applications?** Try the different demo modes to see masked input in action!
## Related Examples

### Prerequisites
- **[01-basic-hello-world](../01-basic-hello-world/)** - Basic command structure
- **[03-user-input](../03-user-input/)** - User input fundamentals

### Enhanced Input Methods
- **[02-arguments-and-options](../02-arguments-and-options/)** - Command arguments with validation
- **[05-interactive-commands](../05-interactive-commands/)** - Interactive menus and workflows

### Visual Enhancement
- **[04-output-formatting](../04-output-formatting/)** - Colors and formatting for prompts
- **[06-table-display](../06-table-display/)** - Display collected data in tables
- **[07-progress-bars](../07-progress-bars/)** - Progress indicators for data processing

### Complete Applications
- **[10-multi-command-app](../10-multi-command-app/)** - Full applications with secure authentication
- **[09-database-ops](../09-database-ops/)** - Database operations with secure credentials
- **[08-file-processing](../08-file-processing/)** - File operations with secure paths

### Development Tools
- **[12-command-scaffolding](../12-command-scaffolding/)** - Generate commands with masked input
