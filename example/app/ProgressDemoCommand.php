<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\Option;
use WebFiori\Cli\Progress\ProgressBarFormat;
use WebFiori\Cli\Progress\ProgressBarStyle;

/**
 * A command to demonstrate progress bar functionality.
 */
class ProgressDemoCommand extends Command {
    
    public function __construct() {
        parent::__construct('progress-demo', [
            '--style' => [
                Option::DESCRIPTION => 'Progress bar style (default, ascii, dots, arrow)',
                Option::OPTIONAL => true,
                Option::DEFAULT => 'default',
                Option::VALUES => ['default', 'ascii', 'dots', 'arrow']
            ],
            '--items' => [
                Option::DESCRIPTION => 'Number of items to process',
                Option::OPTIONAL => true,
                Option::DEFAULT => '50'
            ],
            '--delay' => [
                Option::DESCRIPTION => 'Delay between items in milliseconds',
                Option::OPTIONAL => true,
                Option::DEFAULT => '100'
            ]
        ], 'Demonstrates progress bar functionality with different styles and formats.');
    }
    
    public function exec(): int {
        $style = $this->getArgValue('--style') ?? 'default';
        $items = (int)($this->getArgValue('--items') ?? 50);
        $delay = (int)($this->getArgValue('--delay') ?? 100);
        
        $this->println("Progress Bar Demo");
        $this->println("================");
        $this->println();
        
        // Demo 1: Basic progress bar
        $this->info("Demo 1: Basic Progress Bar");
        $this->basicProgressDemo($items, $delay, $style);
        $this->println();
        
        // Demo 2: Progress bar with ETA
        $this->info("Demo 2: Progress Bar with ETA");
        $this->etaProgressDemo($items, $delay, $style);
        $this->println();
        
        // Demo 3: Progress bar with custom message
        $this->info("Demo 3: Progress Bar with Custom Message");
        $this->messageProgressDemo($items, $delay, $style);
        $this->println();
        
        // Demo 4: Using withProgressBar helper
        $this->info("Demo 4: Using withProgressBar Helper");
        $this->helperProgressDemo($items, $delay);
        $this->println();
        
        $this->success("All demos completed!");
        
        return 0;
    }
    
    private function basicProgressDemo(int $items, int $delay, string $style): void {
        $progressBar = $this->createProgressBar($items)
            ->setStyle($style)
            ->setWidth(40);
        
        $progressBar->start();
        
        for ($i = 0; $i < $items; $i++) {
            usleep($delay * 1000); // Convert to microseconds
            $progressBar->advance();
        }
        
        $progressBar->finish();
    }
    
    private function etaProgressDemo(int $items, int $delay, string $style): void {
        $progressBar = $this->createProgressBar($items)
            ->setStyle($style)
            ->setFormat(ProgressBarFormat::ETA_FORMAT)
            ->setWidth(30);
        
        $progressBar->start();
        
        for ($i = 0; $i < $items; $i++) {
            usleep($delay * 1000);
            $progressBar->advance();
        }
        
        $progressBar->finish();
    }
    
    private function messageProgressDemo(int $items, int $delay, string $style): void {
        $progressBar = $this->createProgressBar($items)
            ->setStyle($style)
            ->setFormat('[{bar}] {percent}% - Processing item {current}/{total}')
            ->setWidth(25);
        
        $progressBar->start('Processing files...');
        
        for ($i = 0; $i < $items; $i++) {
            usleep($delay * 1000);
            $progressBar->advance();
        }
        
        $progressBar->finish('All files processed!');
    }
    
    private function helperProgressDemo(int $items, int $delay): void {
        // Create some dummy data
        $data = range(1, $items);
        
        $this->withProgressBar($data, function($item, $index) use ($delay) {
            usleep($delay * 1000);
            // Simulate some work
        }, 'Processing data...');
    }
}
