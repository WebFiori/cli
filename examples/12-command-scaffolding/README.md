# Command Scaffolding Tools

This example demonstrates the **command scaffolding functionality** in WebFiori CLI, which allows developers to quickly generate new command classes with proper structure, documentation, and templates.

## Features

- **Multiple Templates**: Basic, Interactive, CRUD, and File Processor templates
- **Smart Naming**: Automatic class name generation from command names
- **Namespace Support**: Generate commands with custom namespaces
- **Argument Generation**: Automatically create command arguments
- **Validation**: Input validation for command and class names
- **Overwrite Protection**: Confirmation prompts for existing files

## Running the Example

### Basic Command Generation
```bash
# Generate a basic command
php main.php make:command --name=hello-world

# Generate with custom class name
php main.php make:command --name=user:create --class=CreateUserCommand

# Generate with namespace
php main.php make:command --name=process-data --namespace="App\\Commands"
```

### Template-Based Generation
```bash
# Interactive command template
php main.php make:command --name=setup-wizard --template=interactive

# CRUD operations template
php main.php make:command --name=user-manager --template=crud

# File processor template
php main.php make:command --name=file-converter --template=file-processor
```

### Advanced Options
```bash
# Generate with arguments
php main.php make:command --name=backup-db --args="database,output-path,compress"

# Custom output directory
php main.php make:command --name=deploy --path=src/Commands

# All options combined
php main.php make:command \
  --name=api:sync \
  --class=ApiSyncCommand \
  --namespace="MyApp\\Commands" \
  --template=interactive \
  --args="endpoint,token,timeout" \
  --path=app/Commands
```

## Available Templates

### 1. Basic Template
Simple command structure with minimal boilerplate:
```php
class HelloWorldCommand extends Command {
    public function __construct() {
        parent::__construct('hello-world', [], 'Description');
    }
    
    public function exec(): int {
        $this->println('🚀 Executing hello-world command...');
        // TODO: Implement your command logic here
        $this->success('✅ Command completed successfully!');
        return 0;
    }
}
```

### 2. Interactive Template
Command with user input and validation:
```php
public function exec(): int {
    $name = $this->getInput('Enter name: ');
    $email = $this->getInput('Enter email: ', null, new InputValidator(function($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }, 'Please enter a valid email address!'));
    
    if ($this->confirm('Proceed with the operation?')) {
        $this->processData($name, $email);
        $this->success('✅ Operation completed successfully!');
    }
    
    return 0;
}
```

### 3. CRUD Template
Full CRUD operations structure:
```php
public function exec(): int {
    $action = $this->getArgValue('--action') ?? 'list';
    
    switch ($action) {
        case 'create': return $this->createRecord();
        case 'read': return $this->showRecord();
        case 'update': return $this->updateRecord();
        case 'delete': return $this->deleteRecord();
        case 'list':
        default: return $this->listRecords();
    }
}
```

### 4. File Processor Template
File processing with error handling:
```php
public function exec(): int {
    $inputFile = $this->getArgValue('--input');
    
    if (!$inputFile || !file_exists($inputFile)) {
        $this->error('Input file is required and must exist!');
        return 1;
    }
    
    try {
        $this->processFile($inputFile, $this->getArgValue('--output'));
        $this->success('✅ File processed successfully!');
        return 0;
    } catch (\Exception $e) {
        $this->error('❌ Error: ' . $e->getMessage());
        return 1;
    }
}
```

## Generated Command Structure

All generated commands include:

### Proper Documentation
```php
/**
 * CommandName - Generated CLI command.
 * 
 * Description for command-name command
 */
class CommandNameCommand extends Command {
```

### Constructor with Arguments
```php
public function __construct() {
    parent::__construct('command-name', [
        '--input-file' => [
            ArgumentOption::DESCRIPTION => 'Description for input file',
            ArgumentOption::OPTIONAL => true
        ]
    ], 'Command description');
}
```

### Structured Exec Method
```php
public function exec(): int {
    // Template-specific implementation
    return 0;
}
```

### Additional Helper Methods
Template-specific helper methods for common operations.

## Smart Naming Conventions

The scaffolding tool automatically converts command names to proper class names:

| Command Name | Generated Class Name |
|--------------|---------------------|
| `hello-world` | `HelloWorldCommand` |
| `user:create` | `UserCreateCommand` |
| `api_sync` | `ApiSyncCommand` |
| `process-data-file` | `ProcessDataFileCommand` |

## Validation Features

### Command Name Validation
- Must start with a letter
- Can contain lowercase letters, numbers, hyphens, colons, underscores
- Examples: `hello`, `user:create`, `process-data`

### Class Name Validation
- Must be valid PHP class name (PascalCase)
- Automatically generated if not provided
- Examples: `HelloCommand`, `UserCreateCommand`

### File Overwrite Protection
```bash
$ php main.php make:command --name=existing-command
File /path/to/ExistingCommand.php already exists. Overwrite? (y/n): n
❌ Failed to generate command: File already exists and overwrite was declined.
```

## Integration with Your Application

After generating commands, integrate them into your application:

### 1. Register the Command
```php
use App\Commands\GeneratedCommand;

$runner = new Runner();
$runner->register(new GeneratedCommand());
```

### 2. Implement Logic
Edit the generated `exec()` method to add your specific functionality.

### 3. Add Tests
Create unit tests for your generated commands using `CommandTestCase`.

## Best Practices

### Naming Conventions
- Use kebab-case for command names: `user-create`, `data-export`
- Use namespaces for organization: `user:create`, `db:migrate`
- Keep names descriptive but concise

### Template Selection
- **Basic**: Simple commands with minimal logic
- **Interactive**: Commands requiring user input
- **CRUD**: Data management commands
- **File Processor**: File manipulation commands

### Organization
```
app/
├── Commands/
│   ├── User/
│   │   ├── CreateUserCommand.php
│   │   └── DeleteUserCommand.php
│   ├── Database/
│   │   ├── MigrateCommand.php
│   │   └── SeedCommand.php
│   └── File/
│       ├── ProcessCommand.php
│       └── ConvertCommand.php
```

## Example Workflow

### 1. Generate User Management Commands
```bash
# Create user command
php main.php make:command --name=user:create --template=interactive --namespace="App\\Commands\\User"

# List users command  
php main.php make:command --name=user:list --template=crud --namespace="App\\Commands\\User"

# Delete user command
php main.php make:command --name=user:delete --namespace="App\\Commands\\User"
```

### 2. Generate File Processing Commands
```bash
# CSV processor
php main.php make:command --name=csv:process --template=file-processor --args="input,output,delimiter"

# Image converter
php main.php make:command --name=image:convert --template=file-processor --args="input,format,quality"
```

### 3. Generate API Commands
```bash
# API sync command
php main.php make:command --name=api:sync --template=interactive --args="endpoint,token"

# Data export command
php main.php make:command --name=data:export --args="format,output,filter"
```

---

**Ready to boost your development speed?** Use the scaffolding tools to generate well-structured commands in seconds!
## Related Examples

### Generated Command Examples
- **[01-basic-hello-world](../01-basic-hello-world/)** - Basic template structure
- **[02-arguments-and-options](../02-arguments-and-options/)** - Commands with arguments (CRUD template)
- **[03-user-input](../03-user-input/)** - Interactive commands (Interactive template)
- **[08-file-processing](../08-file-processing/)** - File operations (File Processor template)

### Enhanced Features for Generated Commands
- **[04-output-formatting](../04-output-formatting/)** - Add formatting to generated commands
- **[05-interactive-commands](../05-interactive-commands/)** - Interactive workflows
- **[06-table-display](../06-table-display/)** - Data display in generated commands
- **[07-progress-bars](../07-progress-bars/)** - Progress indicators
- **[11-masked-input](../11-masked-input/)** - Secure input in generated commands

### Complete Applications
- **[10-multi-command-app](../10-multi-command-app/)** - Applications built with scaffolded commands
- **[09-database-ops](../09-database-ops/)** - Database commands (perfect for CRUD template)

### Development Workflow
Use this scaffolding tool to quickly generate commands for any of the above examples!
