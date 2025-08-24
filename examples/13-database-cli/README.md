# Database CLI Tool Example

This example demonstrates building a comprehensive database management CLI tool with migrations, seeding, and advanced database operations.

## 🎯 What You'll Learn

- Database connection management
- Migration system implementation
- Data seeding and fixtures
- Query execution and results formatting
- Database schema inspection
- Backup and restore operations
- Performance monitoring and optimization

## 📁 Project Structure

```
13-database-cli/
├── commands/              # Database command classes
│   ├── MigrateCommand.php
│   ├── SeedCommand.php
│   ├── QueryCommand.php
│   └── SchemaCommand.php
├── migrations/            # Database migration files
│   ├── 001_create_users_table.sql
│   ├── 002_create_posts_table.sql
│   └── 003_add_indexes.sql
├── seeds/                 # Database seed files
│   ├── users.json
│   └── posts.json
├── DatabaseManager.php    # Core database functionality
├── main.php              # Entry point
└── README.md             # This file
```

## 🚀 Running the Examples

### Database Connection
```bash
# Test database connection
php main.php db:connect --host=localhost --database=myapp

# Show connection status
php main.php db:status
```

### Migrations
```bash
# Run all pending migrations
php main.php migrate

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
