<?php

/**
 * Simple Table Example - Quick start guide for WebFiori CLI Table feature
 */

require_once '../../vendor/autoload.php';

// Include table classes
require_once '../../WebFiori/Cli/Table/TableStyle.php';
require_once '../../WebFiori/Cli/Table/Column.php';
require_once '../../WebFiori/Cli/Table/TableData.php';
require_once '../../WebFiori/Cli/Table/ColumnCalculator.php';
require_once '../../WebFiori/Cli/Table/TableFormatter.php';
require_once '../../WebFiori/Cli/Table/TableTheme.php';
require_once '../../WebFiori/Cli/Table/TableRenderer.php';
require_once '../../WebFiori/Cli/Table/TableBuilder.php';

use WebFiori\Cli\Table\TableBuilder;

echo "🚀 WebFiori CLI Table - Simple Usage Examples\n";
echo "==============================================\n\n";

// Example 1: Basic table
echo "Example 1: Basic Table\n";
echo "----------------------\n";

$basicTable = TableBuilder::create()
    ->setHeaders(['Name', 'Age', 'City'])
    ->addRow(['John Doe', 30, 'New York'])
    ->addRow(['Jane Smith', 25, 'Los Angeles'])
    ->addRow(['Bob Johnson', 35, 'Chicago']);

echo $basicTable->render()."\n\n";

// Example 2: Formatted table with colors
echo "Example 2: Formatted Table with Colors\n";
echo "--------------------------------------\n";

$formattedTable = TableBuilder::create()
    ->setHeaders(['Product', 'Price', 'Status'])
    ->addRow(['Laptop', 1299.99, 'Available'])
    ->addRow(['Mouse', 29.99, 'Out of Stock'])
    ->addRow(['Keyboard', 89.99, 'Available'])
    ->configureColumn('Price', [
        'align' => 'right',
        'formatter' => fn($value) => '$'.number_format($value, 2)
    ])
    ->colorizeColumn('Status', function ($value) {
        return match ($value) {
            'Available' => ['color' => 'green', 'bold' => true],
            'Out of Stock' => ['color' => 'red', 'bold' => true],
            default => []
        };
    });

echo $formattedTable->render()."\n\n";

echo "✨ Simple examples completed successfully!\n";
