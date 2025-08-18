<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\Option;

/**
 * Comprehensive formatting demonstration command.
 * 
 * This command shows:
 * - ANSI color codes and text styling
 * - Various formatting techniques
 * - Visual elements and layouts
 * - Progress indicators and animations
 * - Terminal cursor manipulation
 */
class FormattingDemoCommand extends Command {
    public function __construct() {
        parent::__construct('format-demo', [
            '--section' => [
                Option::DESCRIPTION => 'Show specific section only',
                Option::OPTIONAL => true,
                Option::VALUES => ['colors', 'styles', 'tables', 'progress', 'layouts', 'animations']
            ],
            '--no-colors' => [
                Option::DESCRIPTION => 'Disable color output',
                Option::OPTIONAL => true
            ]
        ], 'Demonstrates various output formatting techniques and ANSI styling');
    }

    public function exec(): int {
        $section = $this->getArgValue('--section');
        $noColors = $this->isArgProvided('--no-colors');

        if ($noColors) {
            $this->warning('⚠️  Color output disabled');
            $this->println();
        }

        $this->showHeader();

        if ($section) {
            $this->runSection($section, $noColors);
        } else {
            $this->runAllSections($noColors);
        }

        $this->showFooter();

        return 0;
    }

    /**
     * Create a bordered box.
     */
    private function createBox(string $content): void {
        $lines = explode("\n", $content);
        $maxLength = max(array_map('strlen', $lines));
        $width = $maxLength + 4;

        // Top border
        $this->prints('┌'.str_repeat('─', $width - 2).'┐', ['color' => 'cyan']);
        $this->println();

        // Content
        foreach ($lines as $line) {
            $this->prints('│ ', ['color' => 'cyan']);
            $this->prints(str_pad($line, $maxLength));
            $this->prints(' │', ['color' => 'cyan']);
            $this->println();
        }

        // Bottom border
        $this->prints('└'.str_repeat('─', $width - 2).'┘', ['color' => 'cyan']);
        $this->println();
    }

    /**
     * Create a data table with alignment.
     */
    private function createDataTable(): void {
        $data = [
            ['Product', 'Price', 'Stock', 'Status'],
            ['Laptop', '$1,299.99', '15', 'In Stock'],
            ['Mouse', '$29.99', '150', 'In Stock'],
            ['Keyboard', '$89.99', '0', 'Out of Stock'],
            ['Monitor', '$399.99', '8', 'Low Stock']
        ];

        $widths = [15, 12, 8, 12];

        // Header
        $this->prints('┌');

        for ($i = 0; $i < count($widths); $i++) {
            $this->prints(str_repeat('─', $widths[$i] + 2));

            if ($i < count($widths) - 1) {
                $this->prints('┬');
            }
        }
        $this->prints('┐');
        $this->println();

        // Header row
        $this->prints('│');

        for ($i = 0; $i < count($data[0]); $i++) {
            $this->prints(' ', ['bold' => true]);
            $this->prints(str_pad($data[0][$i], $widths[$i]), ['bold' => true]);
            $this->prints(' │');
        }
        $this->println();

        // Separator
        $this->prints('├');

        for ($i = 0; $i < count($widths); $i++) {
            $this->prints(str_repeat('─', $widths[$i] + 2));

            if ($i < count($widths) - 1) {
                $this->prints('┼');
            }
        }
        $this->prints('┤');
        $this->println();

        // Data rows
        for ($row = 1; $row < count($data); $row++) {
            $this->prints('│');

            for ($col = 0; $col < count($data[$row]); $col++) {
                $this->prints(' ');

                $cellData = $data[$row][$col];
                $style = [];

                // Color coding for status
                if ($col === 3) {
                    if ($cellData === 'In Stock') {
                        $style = ['color' => 'green'];
                    } elseif ($cellData === 'Out of Stock') {
                        $style = ['color' => 'red'];
                    } elseif ($cellData === 'Low Stock') {
                        $style = ['color' => 'yellow'];
                    }
                }

                $this->prints(str_pad($cellData, $widths[$col]), $style);
                $this->prints(' │');
            }
            $this->println();
        }

        // Bottom border
        $this->prints('└');

        for ($i = 0; $i < count($widths); $i++) {
            $this->prints(str_repeat('─', $widths[$i] + 2));

            if ($i < count($widths) - 1) {
                $this->prints('┴');
            }
        }
        $this->prints('┘');
        $this->println();
    }

    /**
     * Create formatted lists.
     */
    private function createLists(): void {
        // Bulleted list
        $this->println("Bulleted List:");
        $items = ['First item', 'Second item', 'Third item with longer text', 'Fourth item'];

        foreach ($items as $item) {
            $this->prints('  • ', ['color' => 'yellow']);
            $this->println($item);
        }

        $this->println();

        // Numbered list
        $this->println("Numbered List:");

        foreach ($items as $index => $item) {
            $num = $index + 1;
            $this->prints("  $num. ", ['color' => 'cyan', 'bold' => true]);
            $this->println($item);
        }

        $this->println();

        // Checklist
        $this->println("Checklist:");
        $tasks = [
            ['task' => 'Setup environment', 'done' => true],
            ['task' => 'Write code', 'done' => true],
            ['task' => 'Test application', 'done' => false],
            ['task' => 'Deploy to production', 'done' => false]
        ];

        foreach ($tasks as $task) {
            $icon = $task['done'] ? '✅' : '⬜';
            $style = $task['done'] ? ['color' => 'green'] : ['color' => 'gray'];

            $this->prints("  $icon ", $style);
            $this->println($task['task'], $style);
        }
    }

    /**
     * Create a simple table.
     */
    private function createSimpleTable(): void {
        $headers = ['Name', 'Age', 'City'];
        $rows = [
            ['John Doe', '30', 'New York'],
            ['Jane Smith', '25', 'Los Angeles'],
            ['Bob Johnson', '35', 'Chicago']
        ];

        // Header
        $this->prints('| ');

        foreach ($headers as $header) {
            $this->prints(str_pad($header, 12).' | ');
        }
        $this->println();

        // Separator
        $this->println('|'.str_repeat('-', 14).'|'.str_repeat('-', 14).'|'.str_repeat('-', 14).'|');

        // Rows
        foreach ($rows as $row) {
            $this->prints('| ');

            foreach ($row as $cell) {
                $this->prints(str_pad($cell, 12).' | ');
            }
            $this->println();
        }
    }

    /**
     * Create a styled table.
     */
    private function createStyledTable(): void {
        $this->prints('┌─────────────┬─────────┬────────────┐', ['color' => 'blue']);
        $this->println();

        $this->prints('│', ['color' => 'blue']);
        $this->prints(' Name        ', ['bold' => true]);
        $this->prints('│', ['color' => 'blue']);
        $this->prints(' Age     ', ['bold' => true]);
        $this->prints('│', ['color' => 'blue']);
        $this->prints(' Department ', ['bold' => true]);
        $this->prints('│', ['color' => 'blue']);
        $this->println();

        $this->prints('├─────────────┼─────────┼────────────┤', ['color' => 'blue']);
        $this->println();

        $data = [
            ['Alice Brown', '28', 'Engineering'],
            ['Charlie Davis', '32', 'Marketing'],
            ['Diana Wilson', '29', 'Design']
        ];

        foreach ($data as $row) {
            $this->prints('│', ['color' => 'blue']);
            $this->prints(' '.str_pad($row[0], 11).' ');
            $this->prints('│', ['color' => 'blue']);
            $this->prints(' '.str_pad($row[1], 7).' ');
            $this->prints('│', ['color' => 'blue']);
            $this->prints(' '.str_pad($row[2], 10).' ');
            $this->prints('│', ['color' => 'blue']);
            $this->println();
        }

        $this->prints('└─────────────┴─────────┴────────────┘', ['color' => 'blue']);
        $this->println();
    }

    /**
     * Create two-column layout.
     */
    private function createTwoColumns(): void {
        $leftColumn = [
            'Left Column',
            '• Item 1',
            '• Item 2',
            '• Item 3',
            '• Item 4'
        ];

        $rightColumn = [
            'Right Column',
            '→ Feature A',
            '→ Feature B',
            '→ Feature C',
            '→ Feature D'
        ];

        $maxRows = max(count($leftColumn), count($rightColumn));

        for ($i = 0; $i < $maxRows; $i++) {
            $left = $leftColumn[$i] ?? '';
            $right = $rightColumn[$i] ?? '';

            if ($i === 0) {
                $this->prints(str_pad($left, 25), ['bold' => true, 'color' => 'blue']);
                $this->prints(' │ ');
                $this->prints($right, ['bold' => true, 'color' => 'green']);
            } else {
                $this->prints(str_pad($left, 25));
                $this->prints(' │ ');
                $this->prints($right);
            }
            $this->println();
        }
    }

    /**
     * Demonstrate animations.
     */
    private function demonstrateAnimations(): void {
        $this->info("🎬 Animation Demonstration");
        $this->println();

        // Spinner
        $this->println("Spinner Animation:");
        $this->showSpinner(3);

        $this->println();
        $this->println();

        // Bouncing ball
        $this->println("Bouncing Animation:");
        $this->showBouncingBall();

        $this->println();
        $this->println();

        // Loading dots
        $this->println("Loading Dots:");
        $this->showLoadingDots();
    }

    /**
     * Demonstrate color capabilities.
     */
    private function demonstrateColors(bool $noColors): void {
        $this->info("🌈 Color Demonstration");
        $this->println();

        if ($noColors) {
            $this->println("Colors disabled - showing plain text versions");
            $this->println();
        }

        // Basic colors
        $this->println("Basic Foreground Colors:");
        $colors = ['black', 'red', 'green', 'yellow', 'blue', 'magenta', 'cyan', 'white'];

        foreach ($colors as $color) {
            if ($noColors) {
                $this->println("  $color text");
            } else {
                $this->prints("  $color text", ['color' => $color]);
                $this->println();
            }
        }

        $this->println();

        // Light colors
        $this->println("Light Foreground Colors:");
        $lightColors = ['light-red', 'light-green', 'light-yellow', 'light-blue', 'light-magenta', 'light-cyan'];

        foreach ($lightColors as $color) {
            if ($noColors) {
                $this->println("  $color text");
            } else {
                $this->prints("  $color text", ['color' => $color]);
                $this->println();
            }
        }

        $this->println();

        // Background colors
        $this->println("Background Colors:");
        $bgColors = ['red', 'green', 'yellow', 'blue', 'magenta', 'cyan'];

        foreach ($bgColors as $color) {
            if ($noColors) {
                $this->println("  Text with $color background");
            } else {
                $this->prints("  Text with $color background", ['bg-color' => $color, 'color' => 'white']);
                $this->println();
            }
        }

        $this->println();

        // Color combinations
        $this->println("Color Combinations:");
        $combinations = [
            ['color' => 'white', 'bg-color' => 'red', 'text' => 'Error style'],
            ['color' => 'black', 'bg-color' => 'green', 'text' => 'Success style'],
            ['color' => 'black', 'bg-color' => 'yellow', 'text' => 'Warning style'],
            ['color' => 'white', 'bg-color' => 'blue', 'text' => 'Info style']
        ];

        foreach ($combinations as $combo) {
            if ($noColors) {
                $this->println("  ".$combo['text']);
            } else {
                $this->prints("  ".$combo['text'], [
                    'color' => $combo['color'],
                    'bg-color' => $combo['bg-color']
                ]);
                $this->println();
            }
        }
    }

    /**
     * Demonstrate layout techniques.
     */
    private function demonstrateLayouts(): void {
        $this->info("📐 Layout Demonstration");
        $this->println();

        // Boxes
        $this->println("Bordered Box:");
        $this->createBox("This is content inside a bordered box!\nIt can contain multiple lines\nand various formatting.");

        $this->println();

        // Columns
        $this->println("Two-Column Layout:");
        $this->createTwoColumns();

        $this->println();

        // Lists
        $this->println("Formatted Lists:");
        $this->createLists();
    }

    /**
     * Demonstrate progress indicators.
     */
    private function demonstrateProgress(): void {
        $this->info("📈 Progress Indicators");
        $this->println();

        // Simple progress bar
        $this->println("Simple Progress Bar:");
        $this->showSimpleProgress();

        $this->println();
        $this->println();

        // Percentage progress
        $this->println("Percentage Progress:");
        $this->showPercentageProgress();

        $this->println();
        $this->println();

        // Multi-step progress
        $this->println("Multi-step Progress:");
        $this->showMultiStepProgress();
    }

    /**
     * Demonstrate text styling.
     */
    private function demonstrateStyles(bool $noColors): void {
        $this->info("✨ Text Styling Demonstration");
        $this->println();

        $styles = [
            ['style' => ['bold' => true], 'name' => 'Bold text'],
            ['style' => ['underline' => true], 'name' => 'Underlined text'],
            ['style' => ['bold' => true, 'color' => 'red'], 'name' => 'Bold red text'],
            ['style' => ['underline' => true, 'color' => 'blue'], 'name' => 'Underlined blue text'],
            ['style' => ['bold' => true, 'bg-color' => 'yellow', 'color' => 'black'], 'name' => 'Bold text with background']
        ];

        foreach ($styles as $styleDemo) {
            if ($noColors) {
                $this->println("  ".$styleDemo['name']);
            } else {
                $this->prints("  ".$styleDemo['name'], $styleDemo['style']);
                $this->println();
            }
        }

        $this->println();

        // Message types
        $this->println("Message Types:");
        $this->success("✅ Success message");
        $this->error("❌ Error message");
        $this->warning("⚠️  Warning message");
        $this->info("ℹ️  Info message");
    }

    /**
     * Demonstrate table formatting.
     */
    private function demonstrateTables(): void {
        $this->info("📊 Table Demonstration");
        $this->println();

        // Simple table
        $this->println("Simple Table:");
        $this->createSimpleTable();

        $this->println();

        // Styled table
        $this->println("Styled Table:");
        $this->createStyledTable();

        $this->println();

        // Data table
        $this->println("Data Table with Alignment:");
        $this->createDataTable();
    }

    /**
     * Run all demonstration sections.
     */
    private function runAllSections(bool $noColors): void {
        $sections = ['colors', 'styles', 'tables', 'progress', 'layouts', 'animations'];

        foreach ($sections as $index => $section) {
            $this->runSection($section, $noColors);

            if ($index < count($sections) - 1) {
                $this->println();
                $this->println(str_repeat('─', 60));
                $this->println();
            }
        }
    }

    /**
     * Run a specific demonstration section.
     */
    private function runSection(string $section, bool $noColors): void {
        switch ($section) {
            case 'colors':
                $this->demonstrateColors($noColors);
                break;
            case 'styles':
                $this->demonstrateStyles($noColors);
                break;
            case 'tables':
                $this->demonstrateTables();
                break;
            case 'progress':
                $this->demonstrateProgress();
                break;
            case 'layouts':
                $this->demonstrateLayouts();
                break;
            case 'animations':
                $this->demonstrateAnimations();
                break;
            default:
                $this->error("Unknown section: $section");
        }
    }

    /**
     * Show bouncing ball animation.
     */
    private function showBouncingBall(): void {
        $width = 30;
        $ball = '●';

        // Move right
        for ($pos = 0; $pos < $width; $pos++) {
            $spaces = str_repeat(' ', $pos);
            $this->prints("\r$spaces$ball", ['color' => 'red']);
            usleep(100000);
        }

        // Move left
        for ($pos = $width; $pos >= 0; $pos--) {
            $spaces = str_repeat(' ', $pos);
            $this->prints("\r$spaces$ball", ['color' => 'blue']);
            usleep(100000);
        }

        $this->println();
    }

    /**
     * Show the demo footer.
     */
    private function showFooter(): void {
        $this->println();
        $this->success("✨ Formatting demonstration completed!");
        $this->info("💡 Tip: Use --section=<name> to view specific sections");
    }

    /**
     * Show the demo header.
     */
    private function showHeader(): void {
        $this->println("🎨 WebFiori CLI Formatting Demonstration");
        $this->println("========================================");
        $this->println();
    }

    /**
     * Show loading dots animation.
     */
    private function showLoadingDots(): void {
        $message = "Loading";

        for ($cycle = 0; $cycle < 3; $cycle++) {
            for ($dots = 0; $dots <= 3; $dots++) {
                $dotStr = str_repeat('.', $dots);
                $this->prints("\r$message$dotStr   ");
                usleep(500000); // 0.5 seconds
            }
        }

        $this->prints("\rLoading complete! ✨", ['color' => 'green']);
        $this->println();
    }

    /**
     * Show multi-step progress.
     */
    private function showMultiStepProgress(): void {
        $steps = [
            'Initializing...',
            'Loading data...',
            'Processing...',
            'Validating...',
            'Finalizing...'
        ];

        foreach ($steps as $index => $step) {
            $stepNum = $index + 1;
            $totalSteps = count($steps);

            $this->prints("Step $stepNum/$totalSteps: $step", ['color' => 'blue']);

            // Simulate work
            for ($i = 0; $i < 10; $i++) {
                $this->prints('.');
                usleep(200000); // 0.2 seconds
            }

            $this->prints(' ✅', ['color' => 'green']);
            $this->println();
        }

        $this->success('All steps completed!');
    }

    /**
     * Show percentage progress.
     */
    private function showPercentageProgress(): void {
        $total = 100;

        for ($i = 0; $i <= $total; $i += 5) {
            $percent = $i;
            $barLength = 30;
            $filled = (int)(($percent / 100) * $barLength);
            $empty = $barLength - $filled;

            $bar = str_repeat('▓', $filled).str_repeat('░', $empty);

            $this->prints("\rProgress: [$bar] $percent%");
            usleep(150000); // 0.15 seconds
        }

        $this->prints(' Done!', ['color' => 'green', 'bold' => true]);
        $this->println();
    }

    /**
     * Show simple progress bar.
     */
    private function showSimpleProgress(): void {
        $total = 20;

        for ($i = 0; $i <= $total; $i++) {
            $filled = str_repeat('█', $i);
            $empty = str_repeat('░', $total - $i);

            $this->prints("\r[$filled$empty]");
            usleep(100000); // 0.1 seconds
        }

        $this->prints(' Complete!', ['color' => 'green']);
        $this->println();
    }

    /**
     * Show spinner animation.
     */
    private function showSpinner(int $duration): void {
        $chars = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
        $start = time();
        $i = 0;

        while (time() - $start < $duration) {
            $char = $chars[$i % count($chars)];
            $this->prints("\r$char Processing...", ['color' => 'blue']);
            usleep(100000); // 0.1 seconds
            $i++;
        }

        $this->prints("\r✅ Processing complete!", ['color' => 'green']);
        $this->println();
    }
}
