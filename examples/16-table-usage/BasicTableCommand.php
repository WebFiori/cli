<?php

require_once '../../vendor/autoload.php';

use WebFiori\CLI\Command;
use WebFiori\CLI\Table\TableOptions;
use WebFiori\CLI\Table\TableStyle;
use WebFiori\CLI\Table\TableTheme;

/**
 * Basic table usage command demonstrating simple table creation
 */
class BasicTableCommand extends Command {
    public function __construct() {
        parent::__construct('basic-table', [], 'Basic table usage demonstration');
    }

    public function exec(): int {
        $this->println('🚀 Basic Table Usage', ['bold' => true, 'color' => 'cyan']);
        $this->println('====================');
        $this->println('');

        // Example 1: Simplest possible table
        $this->info('1. Simplest Table');
        $this->println('');

        $data = [
            ['Alice', 'Active'],
            ['Bob', 'Inactive'],
            ['Carol', 'Active']
        ];

        $this->println('Just data and headers:');
        $this->table($data, ['Name', 'Status']);
        $this->println('');

        // Example 2: With title
        $this->info('2. Table with Title');
        $this->println('');

        $this->println('Adding a title:');
        $this->table($data, ['Name', 'Status'], [
            TableOptions::TITLE => 'User Status'
        ]);
        $this->println('');

        // Example 3: Different style
        $this->info('3. Different Style');
        $this->println('');

        $this->println('Using simple ASCII style:');
        $this->table($data, ['Name', 'Status'], [
            TableOptions::STYLE => TableStyle::SIMPLE,
            TableOptions::TITLE => 'User Status (ASCII)'
        ]);
        $this->println('');

        // Example 4: With colors
        $this->info('4. Adding Colors');
        $this->println('');

        $this->println('Colorizing the Status column:');
        $this->table($data, ['Name', 'Status'], [
            TableOptions::STYLE => TableStyle::BORDERED,
            TableOptions::TITLE => 'User Status (Colored)',
            TableOptions::COLORIZE => [
                'Status' => function ($value) {
                    if ($value === 'Active') {
                        return ['color' => 'green', 'bold' => true];
                    } else {
                        return ['color' => 'red'];
                    }
                }
            ]
        ]);
        $this->println('');

        // Example 5: Professional theme
        $this->info('5. Professional Theme');
        $this->println('');

        $this->println('Using professional theme:');
        $this->table($data, ['Name', 'Status'], [
            TableOptions::STYLE => TableStyle::BORDERED,
            TableOptions::THEME => TableTheme::PROFESSIONAL,
            TableOptions::TITLE => 'User Status (Professional)'
        ]);
        $this->println('');

        // Example 6: Real-world data
        $this->info('6. Real-World Example');
        $this->println('');

        $employees = [
            ['John Doe', 'Manager', '$75,000', 'Full-time'],
            ['Jane Smith', 'Developer', '$65,000', 'Full-time'],
            ['Mike Johnson', 'Designer', '$55,000', 'Part-time'],
            ['Sarah Wilson', 'Analyst', '$60,000', 'Full-time']
        ];

        $this->println('Employee directory with formatting:');
        $this->table($employees, ['Name', 'Position', 'Salary', 'Type'], [
            TableOptions::STYLE => TableStyle::BORDERED,
            TableOptions::THEME => TableTheme::PROFESSIONAL,
            TableOptions::TITLE => 'Employee Directory',
            TableOptions::COLUMNS => [
                'Salary' => ['align' => 'right']
            ],
            TableOptions::COLORIZE => [
                'Type' => function ($value) {
                    return $value === 'Full-time' 
                        ? ['color' => 'green'] 
                        : ['color' => 'yellow'];
                }
            ]
        ]);
        $this->println('');

        $this->success('✅ Basic table usage examples completed!');
        $this->println('');

        $this->info('💡 Quick Tips:');
        $this->println('  • Start with: $this->table($data, $headers)');
        $this->println('  • Add title: [TableOptions::TITLE => "My Table"]');
        $this->println('  • Change style: [TableOptions::STYLE => TableStyle::SIMPLE]');
        $this->println('  • Add colors: [TableOptions::COLORIZE => [...]]');
        $this->println('  • Use professional theme for business reports');
        $this->println('');
        $this->println('Run "table-usage" command for comprehensive examples!');

        return 0;
    }
}
