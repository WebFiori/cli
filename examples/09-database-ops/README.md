# Database Operations Example

This example demonstrates comprehensive database management capabilities in WebFiori CLI, showcasing connection management, migrations, data seeding, query execution, and backup operations using MySQL.

## 🎯 What You'll Learn

- Database connection management with MySQL
- Migration system for schema management
- Data seeding with sample records
- Interactive query execution with formatted results
- Database backup and restore operations
- Schema inspection and status monitoring
- Error handling for database operations
- Table display for query results

## 📁 Files

- `main.php` - Main CLI application with database commands
- `DatabaseManager.php` - Core database functionality and connection management
- `README.md` - This documentation

## 🔧 Database Configuration

The example uses MySQL with the following configuration:
- **Host**: localhost:3306
- **Database**: testing_db
- **Username**: root
- **Password**: 123456
- **Driver**: MySQL with PDO

## 🚀 Running the Example

### Basic Usage
```bash
# Show help
php main.php help --command=db

# Test database connection
php main.php db --action=connect
```

### Database Operations
```bash
# Run migrations (create tables)
php main.php db --action=migrate

# Seed database with sample data
php main.php db --action=seed

# Check database status
php main.php db --action=status

# Execute custom queries
php main.php db --action=query --sql="SELECT * FROM users"

# Create backup
php main.php db --action=backup --file=my_backup.sql

# Restore from backup
php main.php db --action=restore --file=my_backup.sql

# Clean up database (drop tables)
php main.php db --action=cleanup
```

## 📋 Available Actions

### Database Actions (`--action`)
- `connect` - Test database connection and show details
- `migrate` - Run database migrations (create tables)
- `seed` - Populate database with sample data
- `query` - Execute custom SQL queries
- `backup` - Create database backup to file
- `restore` - Restore database from backup file
- `status` - Show database status and table information
- `cleanup` - Clean up database (drop all tables)

### Parameters
- `--action` - Database action to perform (**Required**)
- `--sql` - SQL query to execute (required for `query` action)
- `--file` - File path for backup/restore operations (optional)

### Validation Rules
- Action is required and must be valid
- SQL parameter required for query action
- File parameter required for restore action
- Backup creates timestamped files if no filename provided

## 🎨 Example Output

### Database Connection Test
```bash
php main.php db --action=connect
```
```
🔌 Testing database connection...
✅ Database connection successful!
📊 Connection details:
   • Host: localhost:3306
   • Database: testing_db
   • Username: root
```

### Running Migrations
```bash
php main.php db --action=migrate
```
```
🚀 Running database migrations...
   • Running migration 1...
   • Running migration 2...
✅ Migrations completed successfully!
```

### Seeding Database
```bash
php main.php db --action=seed
```
```
🌱 Seeding database with sample data...
✅ Database seeded successfully!
   • Added 3 users
   • Added 4 posts
```

### Database Status
```bash
php main.php db --action=status
```
```
📊 Database Status
==================
📋 Tables: 3
   • posts: 4 records
   • user_profiles: 0 records
   • users: 3 records
```

### Query Execution with Results
```bash
php main.php db --action=query --sql="SELECT * FROM users LIMIT 2"
```
```
🔍 Executing query...
SQL: SELECT * FROM users LIMIT 2
📊 Query results:
┌────────────────────────┬────────────────────────────┬───────────────────────────────┬────────────────────────────────┐
│ Id                     │ Name                       │ Email                         │ Created At                     │
├────────────────────────┼────────────────────────────┼───────────────────────────────┼────────────────────────────────┤
│                      1 │ John Doe                   │ john@example.com              │ 2025-09-27 19:17:26            │
│                      2 │ Jane Smith                 │ jane@example.com              │ 2025-09-27 19:17:26            │
└────────────────────────┴────────────────────────────┴───────────────────────────────┴────────────────────────────────┘
⏱️  Execution time: 3.79ms
```

### Complex Query Results
```bash
php main.php db --action=query --sql="SELECT * FROM posts"
```
```
🔍 Executing query...
SQL: SELECT * FROM posts
📊 Query results:
┌───────────┬──────────────┬─────────────────────┬────────────────────────────────────────────┬────────────────────────┐
│ Id        │ User Id      │ Title               │ Content                                    │ Created At             │
├───────────┼──────────────┼─────────────────────┼────────────────────────────────────────────┼────────────────────────┤
│         1 │            1 │ First Post          │ This is the content of the first post.     │ 2025-09-27 19:17:26    │
│         2 │            1 │ Second Post         │ This is another post by John.              │ 2025-09-27 19:17:26    │
│         3 │            2 │ Jane's Post         │ Hello from Jane!                           │ 2025-09-27 19:17:26    │
│         4 │            3 │ Bob's Thoughts      │ Some thoughts from Bob.                    │ 2025-09-27 19:17:26    │
└───────────┴──────────────┴─────────────────────┴────────────────────────────────────────────┴────────────────────────┘
⏱️  Execution time: 3.26ms
```

### Database Backup
```bash
php main.php db --action=backup --file=test_backup.sql
```
```
💾 Creating database backup...
File: test_backup.sql
✅ Backup created successfully!
   • File: test_backup.sql
   • Size: 2,299 bytes
   • Tables: 3
```

### Database Cleanup
```bash
php main.php db --action=cleanup
```
```
🧹 Cleaning up database...
   • Dropping table: posts
   • Dropping table: users
✅ Database cleanup completed!
```

### Error Handling Examples

#### Missing Required Action
```bash
php main.php db
```
```
Error: The following required argument(s) are missing: '--action'
```

#### Invalid Action
```bash
php main.php db --action=invalid
```
```
Error: The following argument(s) have invalid values: '--action'
Info: Allowed values for the argument '--action':
connect
migrate
seed
query
backup
restore
status
cleanup
```

#### Missing SQL for Query
```bash
php main.php db --action=query
```
```
❌ SQL query is required for query action
Usage: php main.php db --action=query --sql="SELECT * FROM users"
```

## 🧪 Test Scenarios

### 1. Complete Database Workflow
```bash
# Full workflow from setup to cleanup
php main.php db --action=connect
php main.php db --action=migrate
php main.php db --action=seed
php main.php db --action=status
php main.php db --action=backup --file=full_backup.sql
php main.php db --action=cleanup
```

### 2. Query Testing
```bash
# Test different types of queries
php main.php db --action=query --sql="SELECT COUNT(*) as total FROM users"
php main.php db --action=query --sql="SELECT name, email FROM users WHERE id = 1"
php main.php db --action=query --sql="SHOW TABLES"
php main.php db --action=query --sql="DESCRIBE users"
```

### 3. Backup and Restore Cycle
```bash
# Create backup, cleanup, then restore
php main.php db --action=backup --file=cycle_backup.sql
php main.php db --action=cleanup
php main.php db --action=status  # Should show empty/minimal tables
php main.php db --action=restore --file=cycle_backup.sql
php main.php db --action=status  # Should show restored data
```

### 4. Error Handling
```bash
# Test various error conditions
php main.php db --action=query --sql="SELECT * FROM nonexistent_table"
php main.php db --action=restore --file=nonexistent.sql
php main.php db --action=query  # Missing SQL parameter
```

### 5. Performance Testing
```bash
# Test with larger datasets and measure execution time
php main.php db --action=query --sql="SELECT * FROM users ORDER BY created_at DESC"
php main.php db --action=query --sql="SELECT COUNT(*) FROM posts GROUP BY user_id"
```

## 💡 Key Features Demonstrated

### 1. Database Connection Management
- **PDO Integration**: Secure database connections with prepared statements
- **Configuration Management**: Centralized database configuration
- **Connection Testing**: Verify database connectivity and credentials
- **Error Handling**: Graceful handling of connection failures

### 2. Schema Management
- **Migration System**: Automated table creation and schema updates
- **Foreign Key Constraints**: Proper relational database design
- **Index Management**: Performance optimization with database indexes
- **Schema Inspection**: View table structure and relationships

### 3. Data Operations
- **Data Seeding**: Populate tables with sample data for testing
- **Query Execution**: Execute arbitrary SQL queries safely
- **Result Formatting**: Display query results in formatted tables
- **Performance Monitoring**: Track query execution times

### 4. Backup and Recovery
- **Full Database Backup**: Export complete database structure and data
- **Restore Operations**: Rebuild database from backup files
- **File Management**: Timestamped backup files with size information
- **Data Integrity**: Maintain referential integrity during operations

### 5. User Experience
- **Formatted Output**: Uses WebFiori CLI's built-in `table()` method for consistent formatting
- **Progress Indicators**: Visual feedback for long-running operations
- **Error Messages**: Clear, actionable error messages
- **Help Integration**: Built-in help system with command documentation

## 🔧 Technical Implementation

### Core Classes
- `DatabaseCommand`: Main CLI command handling all database operations
- `DatabaseManager`: Core database functionality and connection management  
- `ArgumentOption`: Command argument configuration and validation
- **Built-in `table()` method**: Uses WebFiori CLI's native table formatting for consistent display

### Database Schema
```sql
-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts table with foreign key
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Sample Data
- **Users**: John Doe, Jane Smith, Bob Johnson
- **Posts**: Multiple posts per user with realistic content
- **Relationships**: Posts linked to users via foreign keys

## 🎯 Best Practices Demonstrated

### 1. Database Security
- Prepared statements to prevent SQL injection
- Secure connection configuration
- Parameter validation and sanitization
- Error message sanitization

### 2. Data Integrity
- Foreign key constraints for referential integrity
- Transaction support for complex operations
- Proper error handling and rollback
- Data validation before insertion

### 3. Performance
- Efficient query execution with timing
- Proper indexing for performance
- Connection pooling and management
- Query optimization techniques

### 4. User Experience
- Clear visual feedback for all operations
- Formatted table output for readability
- Comprehensive error messages
- Progress indicators for long operations

### 5. Maintainability
- Modular command structure
- Centralized configuration management
- Comprehensive logging and debugging
- Clean separation of concerns

## 🔗 Related Examples

- **[06-table-display](../06-table-display/)** - Advanced table formatting techniques
- **[07-progress-bars](../07-progress-bars/)** - Progress indicators for long operations
- **[08-file-processing](../08-file-processing/)** - File handling for backup operations
- **[10-multi-command-app](../10-multi-command-app/)** - Complete CLI application architecture

## 📚 Further Reading

- [WebFiori CLI Documentation](https://webfiori.com/docs/cli)
- [PHP PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Database Design Best Practices](https://www.mysqltutorial.org/mysql-database-design/)
- [SQL Security Guidelines](https://owasp.org/www-project-cheat-sheets/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)

# Run specific migration
php main.php migrate --file=001_create_users_table.sql

# Rollback last migration
php main.php migrate:rollback

# Show migration status
php main.php migrate:status
```

### Data Seeding
```bash
# Seed all tables
php main.php seed

# Seed specific table
php main.php seed --table=users

# Seed with custom data
php main.php seed --file=custom_data.json
```

### Query Operations
```bash
# Execute SQL query
php main.php query --sql="SELECT * FROM users LIMIT 10"

# Execute query from file
php main.php query --file=reports/monthly_stats.sql

# Interactive query mode
php main.php query --interactive
```

### Schema Operations
```bash
# Show database schema
php main.php schema

# Describe specific table
php main.php schema:table --name=users

# Generate schema documentation
php main.php schema:docs --output=schema.md
```

### Backup & Restore
```bash
# Create database backup
php main.php backup --output=backup_2024-01-20.sql

# Restore from backup
php main.php restore --file=backup_2024-01-20.sql

# List available backups
php main.php backup:list
```

## 📖 Key Features

### 1. Migration System
- **Version control**: Track database schema changes
- **Rollback support**: Undo migrations safely
- **Dependency management**: Handle migration dependencies
- **Batch operations**: Run multiple migrations
- **Status tracking**: Monitor migration state

### 2. Data Management
- **Seeding**: Populate tables with test data
- **Fixtures**: Reusable data sets
- **Import/Export**: Data transfer utilities
- **Validation**: Data integrity checks
- **Relationships**: Handle foreign key constraints

### 3. Query Interface
- **Interactive mode**: Real-time query execution
- **Result formatting**: Multiple output formats
- **Query history**: Track executed queries
- **Performance metrics**: Query execution stats
- **Syntax highlighting**: Enhanced readability

### 4. Schema Management
- **Inspection**: Analyze database structure
- **Documentation**: Generate schema docs
- **Comparison**: Compare schema versions
- **Optimization**: Index and performance suggestions
- **Visualization**: Schema relationship diagrams

## 🎨 Expected Output

### Migration Status
```
📊 Migration Status
==================

┌─────────────────────────────┬─────────┬─────────────────────┐
│ Migration                   │ Status  │ Executed At         │
├─────────────────────────────┼─────────┼─────────────────────┤
│ 001_create_users_table.sql  │ ✅ Done │ 2024-01-15 10:30:00 │
│ 002_create_posts_table.sql  │ ✅ Done │ 2024-01-15 10:30:15 │
│ 003_add_indexes.sql         │ ⏳ Pending │ -                   │
└─────────────────────────────┴─────────┴─────────────────────┘

📈 Summary: 2 completed, 1 pending
```

### Query Results
```
🔍 Query Results
===============

Query: SELECT id, name, email, created_at FROM users LIMIT 5
Execution time: 0.023s
Rows returned: 5

┌────┬─────────────┬─────────────────────┬─────────────────────┐
│ ID │ Name        │ Email               │ Created At          │
├────┼─────────────┼─────────────────────┼─────────────────────┤
│ 1  │ John Doe    │ john@example.com    │ 2024-01-15 10:30:00 │
│ 2  │ Jane Smith  │ jane@example.com    │ 2024-01-15 11:15:30 │
│ 3  │ Bob Johnson │ bob@example.com     │ 2024-01-15 12:45:15 │
│ 4  │ Alice Brown │ alice@example.com   │ 2024-01-15 14:20:45 │
│ 5  │ Charlie Lee │ charlie@example.com │ 2024-01-15 15:10:20 │
└────┴─────────────┴─────────────────────┴─────────────────────┘

💡 Query completed successfully
```

### Schema Information
```
🗄️  Database Schema: myapp
==========================

📊 Tables Overview:
┌─────────────┬──────────┬─────────────┬─────────────────────┐
│ Table       │ Columns  │ Rows        │ Size                │
├─────────────┼──────────┼─────────────┼─────────────────────┤
│ users       │ 8        │ 1,234       │ 2.3 MB              │
│ posts       │ 12       │ 5,678       │ 15.7 MB             │
│ comments    │ 6        │ 12,345      │ 8.9 MB              │
│ categories  │ 4        │ 25          │ 4.2 KB              │
└─────────────┴──────────┴─────────────┴─────────────────────┘

🔗 Relationships:
   • users → posts (1:many)
   • posts → comments (1:many)
   • categories → posts (1:many)

📈 Total: 4 tables, 19,282 rows, 26.9 MB
```

### Backup Progress
```
💾 Creating Database Backup
===========================

Analyzing database structure...
[████████████████████████████████████████████████████] 100.0%

Exporting table data:
  • users: [████████████████████████████████████████████████████] 1,234 rows
  • posts: [████████████████████████████████████████████████████] 5,678 rows
  • comments: [████████████████████████████████████████████████████] 12,345 rows
  • categories: [████████████████████████████████████████████████████] 25 rows

✅ Backup completed successfully!

📋 Backup Summary:
   • File: backup_2024-01-20_14-30-15.sql
   • Size: 45.2 MB
   • Tables: 4
   • Total Rows: 19,282
   • Duration: 00:02:15
   • Compression: gzip (87% reduction)
```

## 🔗 Next Steps

After mastering this example, explore:
- **Real database integration**: Connect to MySQL, PostgreSQL, SQLite
- **ORM integration**: Use with Eloquent, Doctrine, etc.
- **Cloud database support**: AWS RDS, Google Cloud SQL
- **Advanced features**: Replication, clustering, performance tuning

## 💡 Try This

Extend the database CLI:

1. **Add more database types**: Support MongoDB, Redis, etc.
2. **Implement connection pooling**: Manage multiple connections
3. **Add query optimization**: Analyze and suggest improvements
4. **Create data visualization**: Generate charts from query results
5. **Add replication support**: Master-slave configuration

```php
// Example: Add query optimization
class QueryOptimizer {
    public function analyze(string $query): array {
        // Analyze query performance
        return [
            'execution_time' => 0.045,
            'rows_examined' => 1000,
            'suggestions' => ['Add index on user_id column']
        ];
    }
}
```
## Related Examples

### Prerequisites
- **[01-basic-hello-world](../01-basic-hello-world/)** - Basic command structure
- **[02-arguments-and-options](../02-arguments-and-options/)** - Database connection arguments

### Enhanced Features
- **[06-table-display](../06-table-display/)** - Display query results in tables
- **[07-progress-bars](../07-progress-bars/)** - Progress for long database operations
- **[05-interactive-commands](../05-interactive-commands/)** - Database management menus

### User Interaction
- **[03-user-input](../03-user-input/)** - Interactive database configuration
- **[11-masked-input](../11-masked-input/)** - Secure database password input
- **[04-output-formatting](../04-output-formatting/)** - Formatted database status

### Data Processing
- **[08-file-processing](../08-file-processing/)** - Import/export database data
- **[10-multi-command-app](../10-multi-command-app/)** - Complete database CLI applications

### Development Tools
- **[12-command-scaffolding](../12-command-scaffolding/)** - Generate database operation commands
