<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\Option;
use WebFiori\Cli\Progress\ProgressBarFormat;
use WebFiori\Cli\Progress\ProgressBarStyle;

/**
 * Comprehensive progress bar demonstration command.
 * 
 * This command shows:
 * - All available progress bar styles
 * - Different format templates
 * - Custom styling and formatting
 * - Real-world usage patterns
 * - Performance considerations
 */
class ProgressDemoCommand extends Command {
    
    public function __construct() {
        parent::__construct('progress-demo', [
            '--style' => [
                Option::DESCRIPTION => 'Progress bar style (default, ascii, dots, arrow)',
                Option::OPTIONAL => true,
                Option::DEFAULT => 'all',
                Option::VALUES => ['all', 'default', 'ascii', 'dots', 'arrow', 'custom']
            ],
            '--items' => [
                Option::DESCRIPTION => 'Number of items to process (10-1000)',
                Option::OPTIONAL => true,
                Option::DEFAULT => '50'
            ],
            '--delay' => [
                Option::DESCRIPTION => 'Delay between items in milliseconds',
                Option::OPTIONAL => true,
                Option::DEFAULT => '100'
            ],
            '--format' => [
                Option::DESCRIPTION => 'Progress bar format template',
                Option::OPTIONAL => true,
                Option::VALUES => ['basic', 'eta', 'rate', 'verbose', 'custom']
            ]
        ], 'Demonstrates progress bar functionality with different styles and formats');
    }
    
    public function exec(): int {
        $style = $this->getArgValue('--style') ?? 'all';
        $items = (int)($this->getArgValue('--items') ?? 50);
        $delay = (int)($this->getArgValue('--delay') ?? 100);
        $format = $this->getArgValue('--format');
        
        // Validate inputs
        if ($items < 10 || $items > 1000) {
            $this->error('Number of items must be between 10 and 1000');
            return 1;
        }
        
        if ($delay < 10 || $delay > 2000) {
            $this->error('Delay must be between 10 and 2000 milliseconds');
            return 1;
        }
        
        $this->showHeader($style, $items, $delay);
        
        if ($style === 'all') {
            $this->demonstrateAllStyles($items, $delay, $format);
        } else {
            $this->demonstrateStyle($style, $items, $delay, $format);
        }
        
        $this->showFooter();
        
        return 0;
    }
    
    /**
     * Show demonstration header.
     */
    private function showHeader(string $style, int $items, int $delay): void {
        $this->println("🎯 Progress Bar Demonstration");
        $this->println("=============================");
        $this->println();
        
        $this->info("📊 Demo Configuration:");
        $this->println("   • Style: " . ($style === 'all' ? 'All styles' : ucfirst($style)));
        $this->println("   • Items: $items");
        $this->println("   • Delay: {$delay}ms per item");
        $this->println("   • Estimated time: " . round(($items * $delay) / 1000, 1) . " seconds");
        $this->println();
    }
    
    /**
     * Show demonstration footer.
     */
    private function showFooter(): void {
        $this->println();
        $this->success("✨ Progress bar demonstration completed!");
        $this->info("💡 Try different combinations of --style, --items, and --delay");
    }
    
    /**
     * Demonstrate all available styles.
     */
    private function demonstrateAllStyles(int $items, int $delay, ?string $format): void {
        $styles = [
            'default' => 'Default Style (Unicode)',
            'ascii' => 'ASCII Style (Compatible)',
            'dots' => 'Dots Style (Circular)',
            'arrow' => 'Arrow Style (Directional)'
        ];
        
        foreach ($styles as $styleKey => $styleTitle) {
            $this->info("🎨 $styleTitle");
            $this->demonstrateStyle($styleKey, $items, $delay, $format);
            $this->println();
            
            // Brief pause between styles
            if ($styleKey !== 'arrow') {
                usleep(500000); // 0.5 seconds
            }
        }
        
        // Custom style demonstration
        $this->info("🎨 Custom Style (Emoji)");
        $this->demonstrateCustomStyle($items, $delay);
    }
    
    /**
     * Demonstrate a specific style.
     */
    private function demonstrateStyle(string $style, int $items, int $delay, ?string $format): void {
        $progressBar = $this->createProgressBar($items);
        
        // Apply style
        switch ($style) {
            case 'default':
                $progressBar->setStyle(ProgressBarStyle::DEFAULT);
                break;
            case 'ascii':
                $progressBar->setStyle(ProgressBarStyle::ASCII);
                break;
            case 'dots':
                $progressBar->setStyle(ProgressBarStyle::DOTS);
                break;
            case 'arrow':
                $progressBar->setStyle(ProgressBarStyle::ARROW);
                break;
            case 'custom':
                $this->demonstrateCustomStyle($items, $delay);
                return;
        }
        
        // Apply format
        if ($format) {
            $progressBar->setFormat($this->getFormatTemplate($format));
        }
        
        // Configure progress bar
        $progressBar->setWidth(40)
                   ->setUpdateThrottle(0.05); // Update every 50ms
        
        // Start processing
        $progressBar->start("Processing with $style style...");
        
        for ($i = 0; $i < $items; $i++) {
            // Simulate work
            usleep($delay * 1000);
            $progressBar->advance();
        }
        
        $progressBar->finish('Complete!');
    }
    
    /**
     * Demonstrate custom style with emojis.
     */
    private function demonstrateCustomStyle(int $items, int $delay): void {
        $customStyle = new ProgressBarStyle('🟩', '⬜', '🟨');
        
        $progressBar = $this->createProgressBar($items)
            ->setStyle($customStyle)
            ->setFormat('🚀 {message} [{bar}] {percent}% | ⚡ {rate}/s | ⏱️  {eta}')
            ->setWidth(30);
        
        $progressBar->start('Processing with emoji style...');
        
        for ($i = 0; $i < $items; $i++) {
            usleep($delay * 1000);
            $progressBar->advance();
        }
        
        $progressBar->finish('🎉 Emoji processing complete!');
    }
    
    /**
     * Get format template by name.
     */
    private function getFormatTemplate(string $format): string {
        return match($format) {
            'basic' => ProgressBarFormat::DEFAULT_FORMAT,
            'eta' => ProgressBarFormat::ETA_FORMAT,
            'rate' => ProgressBarFormat::RATE_FORMAT,
            'verbose' => ProgressBarFormat::VERBOSE_FORMAT,
            'custom' => '📊 [{bar}] {percent}% | 📈 {current}/{total} | 🕐 {elapsed} | 💾 {memory}',
            default => ProgressBarFormat::DEFAULT_FORMAT
        };
    }
}
