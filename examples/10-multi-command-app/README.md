# Multi-Command Application Example

This example demonstrates building a complete, production-ready CLI application with multiple commands, configuration management, and advanced features.

## 🎯 What You'll Learn

- Structuring large CLI applications
- Command organization and discovery
- Configuration management
- Data persistence and storage
- Error handling and logging
- Testing CLI applications
- Documentation and help systems

## 📁 Project Structure

```
10-multi-command-app/
├── commands/           # Command classes
│   ├── UserCommand.php
│   ├── ConfigCommand.php
│   ├── DataCommand.php
│   └── SystemCommand.php
├── config/            # Configuration files
│   ├── app.json
│   └── database.json
├── data/              # Data storage
│   ├── users.json
│   └── logs/
├── tests/             # Unit tests
├── AppManager.php     # Application manager
├── main.php          # Entry point
└── README.md         # This file
```

## 🚀 Running the Application

### Basic Commands
```bash
# Show all available commands
php main.php help

# User management
php main.php user:list
php main.php user:create --name="John Doe" --email="john@example.com"
php main.php user:update --id=1 --name="Jane Doe"
php main.php user:delete --id=1

# Configuration management
php main.php config:show
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
