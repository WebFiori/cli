# Multi-Command Application Example

This example demonstrates building a complete, production-ready CLI application with comprehensive user management, data persistence, export functionality, and advanced CLI features using WebFiori CLI.

## 🎯 What You'll Learn

- Building complex multi-command CLI applications
- User management system with CRUD operations
- Data persistence with JSON file storage
- Export functionality (JSON, CSV formats)
- Interactive user input and validation
- Search and filtering capabilities
- Batch operations and file processing
- Error handling and logging systems
- Configuration management
- Interactive mode for continuous operations

## 📁 Project Structure

```
10-multi-command-app/
├── commands/           # Command classes
│   └── UserCommand.php # Complete user management system
├── config/            # Configuration files
│   └── app.json       # Application configuration
├── data/              # Data storage and logs
│   ├── users.json     # User data persistence
│   └── logs/          # Application logs
│       └── app.log    # Main application log
├── AppManager.php     # Core application manager
├── main.php          # Application entry point
└── README.md         # This documentation
```

## 🚀 Running the Application

### Basic Usage
```bash
# Show all available commands
php main.php help

# Show specific command help
php main.php help --command=user

# Start interactive mode
php main.php -i
```

### User Management Operations
```bash
# List all users
php main.php user --action=list

# Create new user
php main.php user --action=create --name="John Doe" --email="john@example.com" --status=active

# Update existing user
php main.php user --action=update --id=1 --name="Jane Doe" --status=inactive

# Delete user (with confirmation)
php main.php user --action=delete --id=1

# Search users
php main.php user --action=search --search="john"

# Export users
php main.php user --action=export --format=json --file=users.json
php main.php user --action=export --format=csv --file=users.csv
```

## 📋 Available Commands

### User Command (`user`)
Complete user management system with the following actions:

#### Actions (`--action`)
- `list` - Display all users in formatted table
- `create` - Create new user with validation
- `update` - Update existing user by ID
- `delete` - Delete user with confirmation prompt
- `search` - Search users by name or email
- `export` - Export users to file (JSON/CSV)

#### Parameters
- `--action` - Action to perform (**Required**)
- `--id` - User ID for update/delete operations
- `--name` - User full name
- `--email` - User email address (validated)
- `--status` - User status (active/inactive)
- `--format` - Output format (table/json/csv) - Default: table
- `--search` - Search term for filtering
- `--limit` - Maximum number of results - Default: 50
- `--batch` - Enable batch mode for bulk operations
- `--file` - File path for batch operations or export

#### Validation Rules
- Email must be valid email format
- Status must be 'active' or 'inactive'
- ID must exist for update/delete operations
- Name and email required for create operations

## 🎨 Example Output

### User List (Table Format)
```bash
php main.php user --action=list
```
```
Info: 👥 User Management - List Users

┌──────────┬─────────────────┬────────────────────────────┬────────────┬───────────────────────┬───────────────────────┐
│ Id       │ Name            │ Email                      │ Status     │ Created At            │ Updated At            │
├──────────┼─────────────────┼────────────────────────────┼────────────┼───────────────────────┼───────────────────────┤
│        1 │ John Doe        │ john.doe@example.com       │ active     │ 2024-01-15 10:30:00   │ 2024-01-15 10:30:00   │
│        2 │ Jane Smith      │ jane.smith@example.com     │ active     │ 2024-01-16 14:20:00   │ 2024-01-16 14:20:00   │
│        3 │ Bob Johnson     │ bob.johnson@example.com    │ inactive   │ 2024-01-17 09:15:00   │ 2024-01-17 09:15:00   │
└──────────┴─────────────────┴────────────────────────────┴────────────┴───────────────────────┴───────────────────────┘

Info: 📊 Total: 3 users | Active: 2 | Inactive: 1
```

### User Creation
```bash
php main.php user --action=create --name="Alice Brown" --email="alice@example.com" --status=active
```
```
Success: ✅ User created successfully!

Info: 👤 User Information:
   • ID: 4
   • Name: Alice Brown
   • Email: alice@example.com
   • Status: Active
   • Created: 2025-09-27 19:19:41
   • Updated: 2025-09-27 19:19:41
```

### User Update
```bash
php main.php user --action=update --id=4 --name="Alice Cooper" --status=inactive
```
```
Info: Updating user: Alice Brown (alice@example.com)
Success: ✅ User updated successfully!

Info: 👤 User Information:
   • ID: 4
   • Name: Alice Cooper
   • Email: alice@example.com
   • Status: Inactive
   • Created: 2025-09-27 19:19:41
   • Updated: 2025-09-27 19:19:51
```

### User Search
```bash
php main.php user --action=search --search="john"
```
```
Info: 🔍 Search Results for: 'john'

┌──────────┬─────────────────┬────────────────────────────┬────────────┬───────────────────────┬───────────────────────┐
│ Id       │ Name            │ Email                      │ Status     │ Created At            │ Updated At            │
├──────────┼─────────────────┼────────────────────────────┼────────────┼───────────────────────┼───────────────────────┤
│        1 │ John Doe        │ john.doe@example.com       │ active     │ 2024-01-15 10:30:00   │ 2024-01-15 10:30:00   │
│        3 │ Bob Johnson     │ bob.johnson@example.com    │ inactive   │ 2024-01-17 09:15:00   │ 2024-01-17 09:15:00   │
└──────────┴─────────────────┴────────────────────────────┴────────────┴───────────────────────┴───────────────────────┘
Info: Found 2 user(s) matching 'john'
```

### User Export (JSON)
```bash
php main.php user --action=export --format=json --file=users_export.json
```
```
Info: 📤 Exporting 4 users to users_export.json
Success: ✅ Export completed successfully!
Info: 📋 Export Summary:
   • Format: JSON
   • Records: 4
   • File Size: 881.0 B
   • Location: users_export.json
```

### User Export (CSV)
```bash
php main.php user --action=export --format=csv --file=users_export.csv
```
```
Info: 📤 Exporting 4 users to users_export.csv
Success: ✅ Export completed successfully!
Info: 📋 Export Summary:
   • Format: CSV
   • Records: 4
   • File Size: 422.0 B
   • Location: users_export.csv
```

### User Deletion (with Confirmation)
```bash
php main.php user --action=delete --id=4
```
```
Warning: ⚠️  You are about to delete user: Alice Cooper (alice@example.com)
Are you sure you want to delete this user?(y/N)
Success: ✅ User deleted successfully!
```

### JSON Output Format
```bash
php main.php user --action=list --format=json
```
```
Info: 👥 User Management - List Users

[
    {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "status": "active",
        "created_at": "2024-01-15 10:30:00",
        "updated_at": "2024-01-15 10:30:00"
    },
    {
        "id": 2,
        "name": "Jane Smith",
        "email": "jane.smith@example.com",
        "status": "active",
        "created_at": "2024-01-16 14:20:00",
        "updated_at": "2024-01-16 14:20:00"
    }
]

Info: 📊 Total: 2 users | Active: 2 | Inactive: 0
```

### Interactive Mode
```bash
php main.php -i
```
```
>> Running in interactive mode.
>> Type command name or 'exit' to close.
>> user --action=list
Info: 👥 User Management - List Users
[Table output...]
>> exit
```

### Error Handling Examples

#### Missing Required Action
```bash
php main.php user
```
```
Error: The following required argument(s) are missing: '--action'
```

#### Invalid Action
```bash
php main.php user --action=invalid
```
```
Error: The following argument(s) have invalid values: '--action'
Info: Allowed values for the argument '--action':
list
create
update
delete
search
export
```

#### User Not Found
```bash
php main.php user --action=update --id=999 --name="Test"
```
```
Error: User with ID 999 not found.
```

#### Validation Error
```bash
php main.php user --action=create --name="Test User"
```
```
Enter user email:
Error: Validation failed:
  • Field email must be a valid email address
```

## 🧪 Test Scenarios

### 1. Complete User Lifecycle
```bash
# Create, update, search, and delete user
php main.php user --action=create --name="Test User" --email="test@example.com" --status=active
php main.php user --action=update --id=4 --name="Updated User" --status=inactive
php main.php user --action=search --search="updated"
php main.php user --action=delete --id=4
```

### 2. Export and Format Testing
```bash
# Test different export formats
php main.php user --action=export --format=json --file=test.json
php main.php user --action=export --format=csv --file=test.csv
php main.php user --action=list --format=json
php main.php user --action=list --format=table
```

### 3. Search and Filter Testing
```bash
# Test search functionality
php main.php user --action=search --search="john"
php main.php user --action=search --search="@example.com"
php main.php user --action=search --search="active"
```

### 4. Interactive Mode Testing
```bash
# Test interactive mode
echo -e "user --action=list\nuser --action=create --name='Interactive User' --email='interactive@example.com'\nexit" | php main.php -i
```

### 5. Error Handling Testing
```bash
# Test various error conditions
php main.php user --action=update --id=999
php main.php user --action=create --name="Test"
php main.php user --action=delete --id=999
php main.php user --action=invalid
```

### 6. Batch Operations Testing
```bash
# Test batch file processing
echo '[{"name":"Batch User 1","email":"batch1@example.com","status":"active"}]' > batch.json
php main.php user --action=create --batch --file=batch.json
```

## 💡 Key Features Demonstrated

### 1. Application Architecture
- **Multi-Command Structure**: Organized command classes with clear separation
- **Configuration Management**: Centralized app configuration and settings
- **Data Persistence**: JSON-based data storage with automatic backup
- **Logging System**: Comprehensive application logging with timestamps

### 2. User Management System
- **CRUD Operations**: Complete Create, Read, Update, Delete functionality
- **Data Validation**: Email validation, status validation, required field checks
- **Search Functionality**: Search by name, email, or status
- **Confirmation Prompts**: Safety confirmations for destructive operations

### 3. Export and Import
- **Multiple Formats**: JSON and CSV export capabilities
- **File Management**: Automatic file naming and size reporting
- **Batch Operations**: Bulk user creation from JSON files
- **Data Integrity**: Validation during import/export operations

### 4. User Experience
- **Formatted Output**: Uses WebFiori CLI's built-in `table()` method for consistent, professional table formatting
- **Interactive Input**: Prompts for missing required information
- **Progress Feedback**: Clear success/error messages with emojis
- **Help System**: Comprehensive help documentation for all commands

### 5. Advanced CLI Features
- **Interactive Mode**: Continuous command execution without restart
- **Format Options**: Multiple output formats (table, JSON, CSV)
- **Search and Filter**: Advanced filtering capabilities
- **Logging**: Application activity logging for debugging and monitoring

## 🔧 Technical Implementation

### Core Classes
- `UserCommand`: Complete user management command with all CRUD operations
- `AppManager`: Application lifecycle management, logging, and configuration
- `Runner`: WebFiori CLI runner with command registration and execution
- **Built-in `table()` method**: Uses WebFiori CLI's native table formatting for consistent, professional display

### Data Storage
- **JSON Files**: User data stored in `data/users.json`
- **Automatic Backup**: Data persistence with atomic writes
- **Schema Validation**: Consistent data structure enforcement
- **Migration Support**: Data format versioning and upgrades

### User Data Structure
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "status": "active",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
}
```

### Export Formats
- **JSON**: Structured data with full field information
- **CSV**: Comma-separated values with headers
- **Table**: Formatted console output with borders and alignment

## 🎯 Best Practices Demonstrated

### 1. Command Organization
- Single responsibility principle for commands
- Clear command naming and structure
- Comprehensive argument validation
- Consistent error handling patterns

### 2. Data Management
- Atomic file operations for data integrity
- Backup and recovery mechanisms
- Data validation and sanitization
- Consistent data format and structure

### 3. User Experience
- Clear and informative output messages
- Confirmation prompts for destructive actions
- Multiple output format options
- Comprehensive help and documentation

### 4. Error Handling
- Graceful error recovery
- Informative error messages
- Input validation and sanitization
- Logging for debugging and monitoring

### 5. Code Quality
- Modular and maintainable code structure
- Comprehensive documentation
- Consistent coding standards
- Testable command implementations

## 🔗 Related Examples

- **[01-basic-command](../01-basic-command/)** - Simple command creation
- **[02-command-with-args](../02-command-with-args/)** - Argument handling
- **[06-table-display](../06-table-display/)** - Advanced table formatting
- **[08-file-processing](../08-file-processing/)** - File operations and processing

## 📚 Further Reading

- [WebFiori CLI Documentation](https://webfiori.com/docs/cli)
- [Command Design Patterns](https://refactoring.guru/design-patterns/command)
- [CLI Application Best Practices](https://clig.dev/)
- [JSON Data Management](https://www.json.org/json-en.html)
- [CSV File Format Specification](https://tools.ietf.org/html/rfc4180)
php main.php config:set --key="app.debug" --value="true"
php main.php config:get --key="app.name"

# Data operations
php main.php data:export --format=json
php main.php data:import --file="backup.json"
php main.php data:backup --destination="./backups/"

# System operations
php main.php system:status
php main.php system:cleanup
php main.php system:info
```

### Advanced Usage
```bash
# Batch operations
php main.php user:create --batch --file="users.csv"

# Interactive mode
php main.php -i

# Verbose output
php main.php user:list --verbose

# Different output formats
php main.php user:list --format=table
php main.php user:list --format=json
php main.php user:list --format=csv
```

## 📖 Application Architecture

### Command Organization

#### User Management Commands
- `user:list` - List all users with filtering
- `user:create` - Create new users
- `user:update` - Update existing users
- `user:delete` - Delete users
- `user:search` - Search users by criteria

#### Configuration Commands
- `config:show` - Display current configuration
- `config:set` - Set configuration values
- `config:get` - Get specific configuration values
- `config:reset` - Reset to default configuration

#### Data Management Commands
- `data:export` - Export data in various formats
- `data:import` - Import data from files
- `data:backup` - Create data backups
- `data:restore` - Restore from backups
- `data:validate` - Validate data integrity

#### System Commands
- `system:status` - Show system status
- `system:info` - Display system information
- `system:cleanup` - Clean temporary files
- `system:logs` - View application logs

### Core Components

#### AppManager Class
```php
class AppManager {
    private array $config;
    private string $dataPath;
    private Logger $logger;
    
    public function getConfig(string $key = null);
    public function setConfig(string $key, $value);
    public function loadData(string $type): array;
    public function saveData(string $type, array $data);
    public function log(string $level, string $message);
}
```

#### Base Command Class
```php
abstract class BaseCommand extends Command {
    protected AppManager $app;
    
    protected function getApp(): AppManager;
    protected function formatOutput(array $data, string $format);
    protected function validateInput(array $rules, array $data);
    protected function showProgress(callable $task, string $message);
}
```

## 🔍 Key Features

### 1. Configuration Management
- **JSON-based config**: Structured configuration files
- **Environment support**: Different configs per environment
- **Runtime modification**: Change config via CLI
- **Validation**: Config value validation
- **Defaults**: Fallback to default values

### 2. Data Persistence
- **JSON storage**: Simple file-based storage
- **CRUD operations**: Create, Read, Update, Delete
- **Data validation**: Input validation and sanitization
- **Backup/Restore**: Data backup and recovery
- **Migration**: Data structure migrations

### 3. User Management
- **User CRUD**: Complete user lifecycle management
- **Search/Filter**: Advanced user searching
- **Batch operations**: Bulk user operations
- **Data export**: Export users in multiple formats
- **Validation**: Email, phone, and data validation

### 4. Output Formatting
- **Multiple formats**: JSON, CSV, Table, XML
- **Colored output**: ANSI color support
- **Progress bars**: Long operation progress
- **Pagination**: Large dataset handling
- **Sorting**: Configurable data sorting

### 5. Error Handling
- **Graceful errors**: User-friendly error messages
- **Logging**: Comprehensive error logging
- **Recovery**: Automatic error recovery
- **Validation**: Input validation with helpful messages
- **Exit codes**: Proper exit code handling

## 🎨 Expected Output

### User List (Table Format)
```
👥 User Management - List Users

┌────┬─────────────┬─────────────────────┬─────────────┬─────────────┐
│ ID │ Name        │ Email               │ Status      │ Created     │
├────┼─────────────┼─────────────────────┼─────────────┼─────────────┤
│ 1  │ John Doe    │ john@example.com    │ Active      │ 2024-01-15  │
│ 2  │ Jane Smith  │ jane@example.com    │ Active      │ 2024-01-16  │
│ 3  │ Bob Johnson │ bob@example.com     │ Inactive    │ 2024-01-17  │
└────┴─────────────┴─────────────────────┴─────────────┴─────────────┘

📊 Total: 3 users | Active: 2 | Inactive: 1
```

### Configuration Display
```
⚙️  Application Configuration

📱 Application Settings:
   • Name: MyApp
   • Version: 1.0.0
   • Environment: development
   • Debug: enabled

🗄️  Database Settings:
   • Type: json
   • Path: ./data/
   • Backup: enabled

🔧 System Settings:
   • Log Level: info
   • Max Users: 1000
   • Auto Backup: daily
```

### System Status
```
🖥️  System Status Dashboard

📊 Application Health:
   ✅ Configuration: OK
   ✅ Data Storage: OK
   ✅ Permissions: OK
   ⚠️  Disk Space: 85% used

📈 Statistics:
   • Total Users: 156
   • Active Sessions: 12
   • Uptime: 2d 14h 32m
   • Memory Usage: 45.2 MB

🗂️  Storage Information:
   • Data Size: 2.3 MB
   • Backup Size: 1.8 MB
   • Log Size: 512 KB
   • Free Space: 1.2 GB
```

### Data Export Progress
```
📤 Exporting Data

Preparing export...
[████████████████████████████████████████████████████] 100.0% (156/156)

✅ Export completed successfully!

📋 Export Summary:
   • Format: JSON
   • Records: 156 users
   • File Size: 45.2 KB
   • Location: ./exports/users_2024-01-20_14-30-15.json
   • Duration: 00:02
```

## 🧪 Testing

The application includes comprehensive unit tests:

```bash
# Run all tests
php vendor/bin/phpunit tests/

# Run specific test suite
php vendor/bin/phpunit tests/UserCommandTest.php

# Run with coverage
php vendor/bin/phpunit --coverage-html coverage/
```

### Test Structure
```
tests/
├── UserCommandTest.php
├── ConfigCommandTest.php
├── DataCommandTest.php
├── SystemCommandTest.php
└── AppManagerTest.php
```

## 🔗 Next Steps

After mastering this example, explore:
- **[13-database-cli](../13-database-cli/)** - Database management tools
- **Real database integration**: Connect to MySQL, PostgreSQL, SQLite
- **API integration**: Connect to external APIs
- **Web interface**: Add web-based management

## 💡 Try This

Extend the application:

1. **Add authentication**: User login and permissions
2. **Database integration**: Replace JSON with SQL database
3. **API integration**: Connect to external APIs
4. **Plugin system**: Add plugin support
5. **Web interface**: Add web-based management

```php
// Example: Add role-based permissions
class User {
    public function hasPermission(string $permission): bool {
        return in_array($permission, $this->permissions);
    }
}

// Example: Add API integration
class ApiClient {
    public function syncUsers(): array {
        // Sync with external API
    }
}
```
