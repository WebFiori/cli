<?php

require_once '../../vendor/autoload.php';

use WebFiori\Cli\Command;
use WebFiori\Cli\Table\TableOptions;
use WebFiori\Cli\Table\TableStyle;
use WebFiori\Cli\Table\TableTheme;

/**
 * Comprehensive example demonstrating all aspects of table usage in WebFiori CLI
 */
class TableUsageCommand extends Command {
    public function __construct() {
        parent::__construct('table-usage', [], 'Comprehensive demonstration of WebFiori CLI table features');
    }

    public function exec(): int {
        $this->println('📊 WebFiori CLI Table Usage - Complete Guide', ['bold' => true, 'color' => 'cyan']);
        $this->println('===============================================');
        $this->println('');

        // Section 1: Basic Table Usage
        $this->info('1. Basic Table Usage');
        $this->println('====================');
        $this->println('');

        $basicData = [
            ['Alice Johnson', 'Manager', 'Active'],
            ['Bob Smith', 'Developer', 'Active'],
            ['Carol Davis', 'Designer', 'Inactive']
        ];

        $this->println('Simple table with basic data:');
        $this->table($basicData, ['Name', 'Role', 'Status']);
        $this->println('');

        // Section 2: Command Integration
        $this->info('2. Command Integration');
        $this->println('======================');
        $this->println('');

        $this->println('Using $this->table() method in commands:');
        $this->table([
            ['Method Chaining', 'Supported'],
            ['Error Handling', 'Built-in'],
            ['Auto-loading', 'Automatic']
        ], ['Feature', 'Status'], [
            TableOptions::STYLE => TableStyle::SIMPLE,
            TableOptions::TITLE => 'Command Integration Features'
        ]);
        $this->println('');

        // Section 3: Data Formatting
        $this->info('3. Data Formatting');
        $this->println('==================');
        $this->println('');

        $simpleSalesData = [
            ['Q1 2024', '$125,000', 'Excellent'],
            ['Q2 2024', '$98,000', 'Good'],
            ['Q3 2024', '$156,000', 'Excellent'],
            ['Q4 2024', '$87,000', 'Fair']
        ];

        $this->println('Advanced data formatting with pre-formatted data:');
        $this->table($simpleSalesData, ['Quarter', 'Revenue', 'Performance'], [
            TableOptions::STYLE => TableStyle::BORDERED,
            TableOptions::THEME => TableTheme::PROFESSIONAL,
            TableOptions::TITLE => 'Quarterly Sales Report'
        ]);
        $this->println('');

        // Section 4: System Status Example
        $this->info('4. System Status Dashboard');
        $this->println('==========================');
        $this->println('');

        $serviceStatusData = [
            ['Web Server', 'Running'],
            ['Database', 'Running'],
            ['Cache Server', 'Stopped']
        ];

        $this->println('System monitoring dashboard:');
        $this->table($serviceStatusData, ['Service', 'Status']);
        $this->println('');

        // Section 5: Style Showcase
        $this->info('5. Table Styles Showcase');
        $this->println('========================');
        $this->println('');

        $showcaseData = [
            ['Coffee', '$3.50', 'Hot'],
            ['Tea', '$2.75', 'Hot'],
            ['Juice', '$4.25', 'Cold']
        ];

        $styles = [
            TableStyle::BORDERED => 'Bordered Style (Unicode)',
            TableStyle::SIMPLE => 'Simple Style (ASCII)',
            TableStyle::MINIMAL => 'Minimal Style (Clean)',
            TableStyle::COMPACT => 'Compact Style (Space-efficient)'
        ];

        foreach ($styles as $style => $description) {
            $this->println($description.':');
            $this->table($showcaseData, ['Item', 'Price', 'Temperature'], [
                TableOptions::STYLE => $style,
                TableOptions::WIDTH => 60
            ]);
            $this->println('');
        }

        // Section 6: Theme Showcase
        $this->info('6. Color Themes Showcase');
        $this->println('========================');
        $this->println('');

        $this->println('Default Theme:');
        $this->table([
            ['Active', '25'],
            ['Inactive', '3']
        ], ['Status', 'Count'], [
            TableOptions::STYLE => TableStyle::BORDERED,
            TableOptions::TITLE => 'Default Theme Example'
        ]);
        $this->println('');

        $this->println('Professional Theme:');
        $this->table([
            ['Active', '25'],
            ['Inactive', '3']
        ], ['Status', 'Count'], [
            TableOptions::STYLE => TableStyle::BORDERED,
            TableOptions::THEME => TableTheme::PROFESSIONAL,
            TableOptions::TITLE => 'Professional Theme Example'
        ]);
        $this->println('');

        // Section 7: User Management Example
        $this->info('7. User Management Example');
        $this->println('==========================');
        $this->println('');

        $users = [
            [1, 'Alice Johnson', 'alice@example.com', 'Admin', 'Active', '$1,250.75'],
            [2, 'Bob Smith', 'bob@example.com', 'User', 'Active', '$890.50'],
            [3, 'Carol Davis', 'carol@example.com', 'Manager', 'Inactive', '$2,100.00'],
            [4, 'David Wilson', 'david@example.com', 'User', 'Pending', '$750.25']
        ];

        $this->println('Complete user management table:');
        $this->table($users, ['ID', 'Name', 'Email', 'Role', 'Status', 'Balance'], [
            TableOptions::STYLE => TableStyle::BORDERED,
            TableOptions::THEME => TableTheme::PROFESSIONAL,
            TableOptions::TITLE => 'User Management Dashboard',
            TableOptions::COLUMNS => [
                'ID' => ['align' => 'center'],
                'Balance' => ['align' => 'right']
            ]
        ]);
        $this->println('');

        // Section 8: Constants Usage
        $this->info('8. Using Constants for Type Safety');
        $this->println('===================================');
        $this->println('');

        $this->println('Available TableOptions constants:');
        $options = [
            ['STYLE', 'Table visual style'],
            ['THEME', 'Color theme'],
            ['TITLE', 'Table title'],
            ['WIDTH', 'Maximum width'],
            ['COLUMNS', 'Column configuration'],
            ['COLORIZE', 'Color rules']
        ];

        $this->table($options, ['Constant', 'Description'], [
            TableOptions::STYLE => TableStyle::MINIMAL,
            TableOptions::TITLE => 'TableOptions Constants'
        ]);
        $this->println('');

        $this->println('Available TableStyle constants:');
        $styleConstants = [
            ['BORDERED', 'Unicode box-drawing characters'],
            ['SIMPLE', 'ASCII characters for compatibility'],
            ['MINIMAL', 'Clean look with minimal borders'],
            ['COMPACT', 'Space-efficient layout'],
            ['MARKDOWN', 'Markdown-compatible format']
        ];

        $this->table($styleConstants, ['Constant', 'Description'], [
            TableOptions::STYLE => TableStyle::MINIMAL,
            TableOptions::TITLE => 'TableStyle Constants'
        ]);
        $this->println('');

        $this->println('Available TableTheme constants:');
        $themeConstants = [
            ['DEFAULT', 'Standard theme with basic colors'],
            ['DARK', 'Optimized for dark terminals'],
            ['PROFESSIONAL', 'Business-appropriate styling'],
            ['COLORFUL', 'Vibrant colors and styling']
        ];

        $this->table($themeConstants, ['Constant', 'Description'], [
            TableOptions::STYLE => TableStyle::MINIMAL,
            TableOptions::TITLE => 'TableTheme Constants'
        ]);
        $this->println('');

        // Section 9: Error Handling
        $this->info('9. Error Handling');
        $this->println('=================');
        $this->println('');

        $this->println('Testing empty data handling:');
        $this->table([], ['Name', 'Status']);
        $this->println('');

        // Section 10: Best Practices Summary
        $this->info('10. Best Practices Summary');
        $this->println('==========================');
        $this->println('');

        $bestPractices = [
            ['Use Constants', 'Always use TableOptions, TableStyle, and TableTheme constants'],
            ['Format Data', 'Use column formatters for currency, dates, and percentages'],
            ['Colorize Status', 'Apply colors to status columns for better visibility'],
            ['Responsive Design', 'Let tables adapt to terminal width automatically'],
            ['Error Handling', 'Table system handles edge cases gracefully'],
            ['Reusable Config', 'Create configuration templates for consistency']
        ];

        $this->table($bestPractices, ['Practice', 'Description'], [
            TableOptions::STYLE => TableStyle::BORDERED,
            TableOptions::THEME => TableTheme::PROFESSIONAL,
            TableOptions::TITLE => 'WebFiori CLI Table Best Practices'
        ]);
        $this->println('');

        $this->success('✅ Complete table usage demonstration finished!');
        $this->println('');

        $this->info('💡 Key Takeaways:');
        $this->println('  • Use $this->table() method in any Command class');
        $this->println('  • Leverage constants for type safety and IDE support');
        $this->println('  • Apply formatters and colorization for professional output');
        $this->println('  • Choose appropriate styles and themes for your use case');
        $this->println('  • Tables automatically handle responsive design and errors');
        $this->println('  • Create reusable configurations for consistency');

        return 0;
    }
}
