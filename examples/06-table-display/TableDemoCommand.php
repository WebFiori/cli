<?php

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

use WebFiori\Cli\ArgumentOption;
use WebFiori\Cli\Command;
use WebFiori\Cli\Table\Column;
use WebFiori\Cli\Table\TableBuilder;
use WebFiori\Cli\Table\TableTheme;

/**
 * TableDemoCommand - Demonstrates the WebFiori CLI Table feature.
 * 
 * This command showcases various table display capabilities including:
 * - Different table styles
 * - Column configuration and formatting
 * - Data type handling
 * - Color themes and status indicators
 * - Responsive design
 * - Export capabilities
 */
class TableDemoCommand extends Command {
    public function __construct() {
        parent::__construct('table-demo', [
            '--demo' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'Specific demo to run (users, products, services, styles, themes, export)',
                ArgumentOption::VALUES => ['users', 'products', 'services', 'styles', 'themes', 'export', 'all']
            ],
            '--style' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'Table style to use',
                ArgumentOption::VALUES => ['bordered', 'simple', 'minimal', 'compact', 'markdown'],
                ArgumentOption::DEFAULT => 'bordered'
            ],
            '--theme' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'Color theme to use',
                ArgumentOption::VALUES => ['default', 'dark', 'light', 'colorful', 'professional', 'minimal'],
                ArgumentOption::DEFAULT => 'default'
            ],
            '--width' => [
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DESCRIPTION => 'Maximum table width (default: auto-detect)',
                ArgumentOption::DEFAULT => '0'
            ]
        ], 'Demonstrates WebFiori CLI Table display capabilities with various examples');
    }

    public function exec(): int {
        $this->println('🎯 WebFiori CLI Table Feature Demonstration', ['bold' => true, 'color' => 'light-cyan']);
        $this->println('============================================');
        $this->println('');

        $demo = $this->getArgValue('--demo') ?? 'all';
        $style = $this->getArgValue('--style') ?? 'bordered';
        $theme = $this->getArgValue('--theme') ?? 'default';
        $width = (int)($this->getArgValue('--width') ?? '0');

        if ($width === 0) {
            $width = $this->getTerminalWidth();
        }

        $this->println("Configuration:", ['color' => 'yellow']);
        $this->println("  • Demo: $demo");
        $this->println("  • Style: $style");
        $this->println("  • Theme: $theme");
        $this->println("  • Width: {$width} characters");
        $this->println('');

        try {
            switch ($demo) {
                case 'users':
                    $this->demoUserManagement($style, $theme, $width);
                    break;
                case 'products':
                    $this->demoProductCatalog($style, $theme, $width);
                    break;
                case 'services':
                    $this->demoServiceStatus($style, $theme, $width);
                    break;
                case 'styles':
                    $this->demoTableStyles($width);
                    break;
                case 'themes':
                    $this->demoColorThemes($width);
                    break;
                case 'export':
                    $this->demoDataExport($style, $theme, $width);
                    break;
                case 'all':
                default:
                    $this->runAllDemos($style, $theme, $width);
                    break;
            }

            $this->println('');
            $this->success('✨ Table demonstration completed successfully!');
            $this->println('');
            $this->info('💡 Tips:');
            $this->println('  • Use --demo=<name> to run specific demonstrations');
            $this->println('  • Try different --style and --theme combinations');
            $this->println('  • Adjust --width for different terminal sizes');

            return 0;
        } catch (Exception $e) {
            $this->error('Demo failed: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Demonstrate color themes.
     */
    private function demoColorThemes(int $width): void {
        $this->println('🌈 Color Theme Showcase', ['bold' => true, 'color' => 'light-magenta']);
        $this->println('-----------------------');

        $data = [
            ['Active', 25, '83.3%'],
            ['Inactive', 3, '10.0%'],
            ['Pending', 2, '6.7%']
        ];

        $themes = [
            'default' => 'Standard theme with basic colors',
            'dark' => 'Dark theme for dark terminals',
            'colorful' => 'Vibrant colors and styling',
            'professional' => 'Business-appropriate styling'
        ];

        foreach ($themes as $themeName => $description) {
            $this->println("Theme: ".ucfirst($themeName)." ($description)", ['color' => 'yellow']);

            $table = TableBuilder::create()
                ->setHeaders(['Status', 'Count', 'Percentage'])
                ->addRows($data)
                ->setTheme(TableTheme::create($themeName))
                ->setMaxWidth(min($width, 50))
                ->configureColumn('Count', ['align' => 'right'])
                ->configureColumn('Percentage', [
                    'align' => 'right',
                    'formatter' => fn($value) => str_replace('%', '', $value).'%'
                ])
                ->colorizeColumn('Status', function ($value) {
                    return match (strtolower($value)) {
                        'active' => ['color' => 'green', 'bold' => true],
                        'inactive' => ['color' => 'red'],
                        'pending' => ['color' => 'yellow'],
                        default => []
                    };
                });

            echo $table->render();
            $this->println('');
        }

        $this->info('Themes automatically adapt to terminal capabilities.');
    }

    /**
     * Demonstrate data export capabilities.
     */
    private function demoDataExport(string $style, string $theme, int $width): void {
        $this->println('💾 Data Export Capabilities', ['bold' => true, 'color' => 'light-green']);
        $this->println('---------------------------');

        $exportData = [
            ['1', 'Ahmed Hassan', 'ahmed.hassan@example.com', 'Active'],
            ['2', 'Sarah Johnson', 'sarah.johnson@example.com', 'Inactive'],
            ['3', 'Omar Al-Rashid', 'omar.alrashid@example.com', 'Active']
        ];

        $table = TableBuilder::create()
            ->setHeaders(['ID', 'Name', 'Email', 'Status'])
            ->addRows($exportData)
            ->setTitle('Sample Export Data')
            ->useStyle($style)
            ->setTheme(TableTheme::create($theme))
            ->setMaxWidth($width);

        echo $table->render();

        $this->println('');
        $this->info('Export formats available:');
        $this->println('  • JSON format (structured data)');
        $this->println('  • CSV format (spreadsheet compatible)');
        $this->println('  • Array format (PHP arrays)');
        $this->println('  • Associative arrays (key-value pairs)');
        $this->println('');
        $this->println('Note: In a real application, you would access the TableData');
        $this->println('object to export data in various formats.');
    }

    /**
     * Demonstrate product catalog table.
     */
    private function demoProductCatalog(string $style, string $theme, int $width): void {
        $this->println('🛍️ Product Catalog', ['bold' => true, 'color' => 'blue']);
        $this->println('------------------');

        $products = [
            ['LAP001', 'MacBook Pro 16"', 2499.99, 15, 'Electronics', true, 4.8],
            ['MOU002', 'Wireless Mouse', 29.99, 0, 'Accessories', true, 4.2],
            ['KEY003', 'Mechanical Keyboard', 149.99, 25, 'Accessories', true, 4.6],
            ['MON004', '4K Monitor 27"', 399.99, 8, 'Electronics', false, 4.4],
            ['HDD005', 'External SSD 1TB', 199.99, 50, 'Storage', true, 4.7]
        ];

        $table = TableBuilder::create()
            ->setHeaders(['SKU', 'Product Name', 'Price', 'Stock', 'Category', 'Featured', 'Rating'])
            ->addRows($products)
            ->setTitle('Product Inventory')
            ->useStyle($style)
            ->setTheme(TableTheme::create($theme))
            ->setMaxWidth($width)
            ->configureColumn('SKU', ['width' => 8, 'align' => 'center'])
            ->configureColumn('Product Name', ['width' => 20, 'truncate' => true])
            ->configureColumn('Price', [
                'width' => 10,
                'align' => 'right',
                'formatter' => fn($value) => '$'.number_format($value, 2)
            ])
            ->configureColumn('Stock', [
                'width' => 6,
                'align' => 'right',
                'formatter' => fn($value) => $value > 0 ? (string)$value : 'Out'
            ])
            ->configureColumn('Category', ['width' => 12, 'align' => 'center'])
            ->configureColumn('Featured', [
                'width' => 9,
                'align' => 'center',
                'formatter' => fn($value) => $value ? '⭐ Yes' : '   No'
            ])
            ->configureColumn('Rating', [
                'width' => 7,
                'align' => 'center',
                'formatter' => fn($value) => '★ '.number_format($value, 1)
            ])
            ->colorizeColumn('Stock', function ($value) {
                if ($value === 'Out' || $value === 0) {
                    return ['color' => 'red', 'bold' => true];
                } elseif (is_numeric($value) && $value < 10) {
                    return ['color' => 'yellow'];
                }

                return ['color' => 'green'];
            });

        echo $table->render();

        $this->println('');
        $this->info('Features demonstrated:');
        $this->println('  • Currency formatting');
        $this->println('  • Stock level indicators with colors');
        $this->println('  • Boolean formatting with icons');
        $this->println('  • Rating display with stars');
        $this->println('  • Product name truncation');
    }

    /**
     * Demonstrate service status monitoring.
     */
    private function demoServiceStatus(string $style, string $theme, int $width): void {
        $this->println('🔧 Service Status Monitor', ['bold' => true, 'color' => 'magenta']);
        $this->println('-------------------------');

        $services = [
            ['Web Server', 'nginx/1.20', 'Running', '99.9%', '45ms', '2.1GB', '✅'],
            ['Database', 'MySQL 8.0', 'Running', '99.8%', '12ms', '4.5GB', '✅'],
            ['Cache Server', 'Redis 6.2', 'Stopped', '0%', 'N/A', '0MB', '❌'],
            ['API Gateway', 'Kong 3.0', 'Running', '99.7%', '78ms', '512MB', '✅'],
            ['Message Queue', 'RabbitMQ', 'Warning', '95.2%', '156ms', '1.2GB', '⚠️'],
            ['Load Balancer', 'HAProxy', 'Running', '100%', '5ms', '128MB', '✅']
        ];

        $table = TableBuilder::create()
            ->setHeaders(['Service', 'Version', 'Status', 'Uptime', 'Response', 'Memory', 'Health'])
            ->addRows($services)
            ->setTitle('System Health Dashboard')
            ->useStyle($style)
            ->setTheme(TableTheme::create($theme))
            ->setMaxWidth($width)
            ->configureColumn('Service', ['width' => 14, 'align' => 'left'])
            ->configureColumn('Version', ['width' => 12, 'align' => 'center'])
            ->configureColumn('Status', ['width' => 10, 'align' => 'center'])
            ->configureColumn('Uptime', ['width' => 8, 'align' => 'right'])
            ->configureColumn('Response', ['width' => 10, 'align' => 'right'])
            ->configureColumn('Memory', ['width' => 8, 'align' => 'right'])
            ->configureColumn('Health', ['width' => 8, 'align' => 'center'])
            ->colorizeColumn('Status', function ($value) {
                return match (strtolower($value)) {
                    'running' => ['color' => 'green', 'bold' => true],
                    'stopped' => ['color' => 'red', 'bold' => true],
                    'warning' => ['color' => 'yellow', 'bold' => true],
                    default => []
                };
            })
            ->colorizeColumn('Health', function ($value) {
                return match ($value) {
                    '✅' => ['color' => 'green'],
                    '❌' => ['color' => 'red'],
                    '⚠️' => ['color' => 'yellow'],
                    default => []
                };
            });

        echo $table->render();

        $this->println('');
        $this->info('Features demonstrated:');
        $this->println('  • System monitoring data display');
        $this->println('  • Multiple status indicators');
        $this->println('  • Performance metrics formatting');
        $this->println('  • Health status with emoji indicators');
        $this->println('  • Memory usage display');
    }

    /**
     * Demonstrate different table styles.
     */
    private function demoTableStyles(int $width): void {
        $this->println('🎨 Table Style Variations', ['bold' => true, 'color' => 'cyan']);
        $this->println('-------------------------');

        $data = [
            ['Coffee', '$3.50', 'Hot'],
            ['Tea', '$2.75', 'Hot'],
            ['Juice', '$4.25', 'Cold']
        ];

        $styles = [
            'bordered' => 'Unicode box-drawing characters',
            'simple' => 'ASCII characters for compatibility',
            'minimal' => 'Clean look with minimal borders',
            'compact' => 'Space-efficient layout',
            'markdown' => 'Markdown-compatible format'
        ];

        foreach ($styles as $styleName => $description) {
            $this->println("Style: ".ucfirst($styleName)." ($description)", ['color' => 'yellow']);

            $table = TableBuilder::create()
                ->setHeaders(['Item', 'Price', 'Temperature'])
                ->addRows($data)
                ->useStyle($styleName)
                ->setMaxWidth(min($width, 60)); // Limit width for style demo

            echo $table->render();
            $this->println('');
        }

        $this->info('All table styles are responsive and adapt to terminal width.');
    }

    /**
     * Demonstrate user management table.
     */
    private function demoUserManagement(string $style, string $theme, int $width): void {
        $this->println('👥 User Management System', ['bold' => true, 'color' => 'green']);
        $this->println('-------------------------');

        $users = [
            ['1', 'Ahmed Hassan', 'ahmed.hassan@example.com', 'Active', '2024-01-15', 'Admin', '$1,250.75'],
            ['2', 'Sarah Johnson', 'sarah.johnson@example.com', 'Inactive', '2024-01-16', 'User', '$890.50'],
            ['3', 'Omar Al-Rashid', 'omar.alrashid@example.com', 'Active', '2024-01-17', 'Manager', '$2,100.00'],
            ['4', 'Fatima Al-Zahra', 'fatima.alzahra@example.com', 'Pending', '2024-01-18', 'User', '$750.25'],
            ['5', 'Michael Davis', 'michael.davis@example.com', 'Active', '2024-01-19', 'Admin', '$1,800.80']
        ];

        $table = TableBuilder::create()
            ->setHeaders(['ID', 'Name', 'Email', 'Status', 'Created', 'Role', 'Balance'])
            ->addRows($users)
            ->setTitle('User Management Dashboard')
            ->useStyle($style)
            ->setTheme(TableTheme::create($theme))
            ->setMaxWidth($width)
            ->configureColumn('ID', ['width' => 4, 'align' => 'center'])
            ->configureColumn('Name', ['width' => 15, 'align' => 'left'])
            ->configureColumn('Email', ['width' => 25, 'truncate' => true])
            ->configureColumn('Status', ['width' => 10, 'align' => 'center'])
            ->configureColumn('Created', [
                'width' => 12,
                'align' => 'center',
                'formatter' => fn($date) => date('M j, Y', strtotime($date))
            ])
            ->configureColumn('Role', ['width' => 8, 'align' => 'center'])
            ->configureColumn('Balance', [
                'width' => 12,
                'align' => 'right',
                'formatter' => fn($value) => str_replace('$', '', $value) // Remove existing $ for proper formatting
            ])
            ->colorizeColumn('Status', function ($value) {
                return match (strtolower($value)) {
                    'active' => ['color' => 'green', 'bold' => true],
                    'inactive' => ['color' => 'red', 'bold' => true],
                    'pending' => ['color' => 'yellow', 'bold' => true],
                    default => []
                };
            });

        echo $table->render();

        $this->println('');
        $this->info('Features demonstrated:');
        $this->println('  • Column width control and alignment');
        $this->println('  • Date formatting');
        $this->println('  • Status-based colorization');
        $this->println('  • Email truncation for long addresses');
        $this->println('  • Responsive design within terminal width');
    }

    /**
     * Get terminal width with fallback.
     */
    private function getTerminalWidth(): int {
        // Try to get terminal width
        $width = exec('tput cols 2>/dev/null');

        if (is_numeric($width)) {
            return (int)$width;
        }

        // Fallback to environment variable
        $width = getenv('COLUMNS');

        if ($width !== false && is_numeric($width)) {
            return (int)$width;
        }

        // Default fallback
        return 80;
    }

    /**
     * Run all demonstrations.
     */
    private function runAllDemos(string $style, string $theme, int $width): void {
        $this->demoUserManagement($style, $theme, $width);
        $this->println('');
        $this->demoProductCatalog($style, $theme, $width);
        $this->println('');
        $this->demoServiceStatus($style, $theme, $width);
        $this->println('');
        $this->demoTableStyles($width);
        $this->println('');
        $this->demoColorThemes($width);
    }
}
