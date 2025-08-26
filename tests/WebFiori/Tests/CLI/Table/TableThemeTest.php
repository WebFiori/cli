<?php

namespace tests\WebFiori\CLI\Table;

use PHPUnit\Framework\TestCase;
use WebFiori\CLI\Table\TableTheme;

/**
 * Unit tests for TableTheme class.
 * 
 * Tests color schemes, style combinations, and theming functionality.
 */
class TableThemeTest extends TestCase {
    
    private TableTheme $theme;
    
    protected function setUp(): void {
        require_once __DIR__ . '/../../../../WebFiori/Cli/Table/TableTheme.php';
        
        $this->theme = new TableTheme();
    }
    
    /**
     * @test
     */
    public function testConstructor() {
        $theme = new TableTheme();
        
        $this->assertInstanceOf(TableTheme::class, $theme);
    }
    
    /**
     * @test
     */
    public function testConstructorWithConfig() {
        $config = [
            'headerColors' => ['color' => 'blue'],
            'useAlternatingRows' => true
        ];
        
        $theme = new TableTheme($config);
        
        $this->assertInstanceOf(TableTheme::class, $theme);
    }
    
    /**
     * @test
     */
    public function testConfigure() {
        $config = [
            'headerColors' => ['color' => 'red', 'bold' => true],
            'cellColors' => ['color' => 'white'],
            'useAlternatingRows' => true
        ];
        
        $result = $this->theme->configure($config);
        
        $this->assertSame($this->theme, $result); // Fluent interface
    }
    
    /**
     * @test
     */
    public function testConfigureWithUnderscoreKeys() {
        $config = [
            'header_colors' => ['color' => 'blue'],
            'cell_colors' => ['color' => 'black'],
            'alternating_row_colors' => [[], ['background' => 'gray']],
            'use_alternating_rows' => true,
            'status_colors' => ['active' => ['color' => 'green']]
        ];
        
        $this->theme->configure($config);
        
        // Should not throw any errors
        $this->assertInstanceOf(TableTheme::class, $this->theme);
    }
    
    /**
     * @test
     */
    public function testApplyHeaderStyle() {
        $this->theme->setHeaderColors(['color' => 'blue', 'bold' => true]);
        
        $result = $this->theme->applyHeaderStyle('Test Header');
        
        $this->assertStringContainsString('Test Header', $result);
        $this->assertStringContainsString("\x1b[", $result); // ANSI escape sequence
    }
    
    /**
     * @test
     */
    public function testApplyHeaderStyleWithCustomStyler() {
        $styler = fn($text) => ">>> $text <<<";
        $this->theme->setHeaderStyler($styler);
        
        $result = $this->theme->applyHeaderStyle('Test');
        
        $this->assertEquals('>>> Test <<<', $result);
    }
    
    /**
     * @test
     */
    public function testApplyCellStyle() {
        $this->theme->setCellColors(['color' => 'green']);
        
        $result = $this->theme->applyCellStyle('Test Cell', 0, 0);
        
        $this->assertStringContainsString('Test Cell', $result);
        $this->assertStringContainsString("\x1b[", $result); // ANSI escape sequence
    }
    
    /**
     * @test
     */
    public function testApplyCellStyleWithAlternatingRows() {
        $this->theme->setAlternatingRowColors([
            [],
            ['background' => 'gray']
        ]);
        
        $result1 = $this->theme->applyCellStyle('Row 0', 0, 0);
        $result2 = $this->theme->applyCellStyle('Row 1', 1, 0);
        
        $this->assertStringContainsString('Row 0', $result1);
        $this->assertStringContainsString('Row 1', $result2);
        // Row 1 should have background color
        $this->assertStringContainsString("\x1b[", $result2);
    }
    
    /**
     * @test
     */
    public function testApplyCellStyleWithCustomStyler() {
        $styler = fn($text, $row, $col) => "[$row,$col] $text";
        $this->theme->setCellStyler($styler);
        
        $result = $this->theme->applyCellStyle('Test', 1, 2);
        
        $this->assertEquals('[1,2] Test', $result);
    }
    
    /**
     * @test
     */
    public function testSetHeaderColors() {
        $colors = ['color' => 'red', 'bold' => true];
        $result = $this->theme->setHeaderColors($colors);
        
        $this->assertSame($this->theme, $result); // Fluent interface
    }
    
    /**
     * @test
     */
    public function testSetCellColors() {
        $colors = ['color' => 'blue'];
        $result = $this->theme->setCellColors($colors);
        
        $this->assertSame($this->theme, $result);
    }
    
    /**
     * @test
     */
    public function testSetAlternatingRowColors() {
        $colors = [[], ['background' => 'light-gray']];
        $result = $this->theme->setAlternatingRowColors($colors);
        
        $this->assertSame($this->theme, $result);
    }
    
    /**
     * @test
     */
    public function testUseAlternatingRows() {
        $result = $this->theme->useAlternatingRows(true);
        
        $this->assertSame($this->theme, $result);
    }
    
    /**
     * @test
     */
    public function testSetStatusColors() {
        $colors = [
            'active' => ['color' => 'green'],
            'inactive' => ['color' => 'red']
        ];
        $result = $this->theme->setStatusColors($colors);
        
        $this->assertSame($this->theme, $result);
    }
    
    /**
     * @test
     */
    public function testSetHeaderStyler() {
        $styler = fn($text) => strtoupper($text);
        $result = $this->theme->setHeaderStyler($styler);
        
        $this->assertSame($this->theme, $result);
    }
    
    /**
     * @test
     */
    public function testSetCellStyler() {
        $styler = fn($text, $row, $col) => $text;
        $result = $this->theme->setCellStyler($styler);
        
        $this->assertSame($this->theme, $result);
    }
    
    /**
     * @test
     */
    public function testDefaultTheme() {
        $theme = TableTheme::default();
        
        $this->assertInstanceOf(TableTheme::class, $theme);
    }
    
    /**
     * @test
     */
    public function testDarkTheme() {
        $theme = TableTheme::dark();
        
        $this->assertInstanceOf(TableTheme::class, $theme);
        
        // Test that it applies colors
        $headerResult = $theme->applyHeaderStyle('Test');
        $this->assertStringContainsString("\x1b[", $headerResult);
    }
    
    /**
     * @test
     */
    public function testLightTheme() {
        $theme = TableTheme::light();
        
        $this->assertInstanceOf(TableTheme::class, $theme);
    }
    
    /**
     * @test
     */
    public function testColorfulTheme() {
        $theme = TableTheme::colorful();
        
        $this->assertInstanceOf(TableTheme::class, $theme);
        
        // Should have alternating rows
        $result1 = $theme->applyCellStyle('Test', 0, 0);
        $result2 = $theme->applyCellStyle('Test', 1, 0);
        
        // Both should have colors but potentially different
        $this->assertStringContainsString("\x1b[", $result1);
        $this->assertStringContainsString("\x1b[", $result2);
    }
    
    /**
     * @test
     */
    public function testMinimalTheme() {
        $theme = TableTheme::minimal();
        
        $this->assertInstanceOf(TableTheme::class, $theme);
        
        // Should have minimal styling
        $headerResult = $theme->applyHeaderStyle('Test');
        $this->assertStringContainsString('Test', $headerResult);
    }
    
    /**
     * @test
     */
    public function testProfessionalTheme() {
        $theme = TableTheme::professional();
        
        $this->assertInstanceOf(TableTheme::class, $theme);
    }
    
    /**
     * @test
     */
    public function testHighContrastTheme() {
        $theme = TableTheme::highContrast();
        
        $this->assertInstanceOf(TableTheme::class, $theme);
        
        // Should apply high contrast colors
        $headerResult = $theme->applyHeaderStyle('Test');
        $this->assertStringContainsString("\x1b[", $headerResult);
    }
    
    /**
     * @test
     */
    public function testCustomTheme() {
        $config = [
            'headerColors' => ['color' => 'magenta'],
            'cellColors' => ['color' => 'cyan']
        ];
        
        $theme = TableTheme::custom($config);
        
        $this->assertInstanceOf(TableTheme::class, $theme);
    }
    
    /**
     * @test
     */
    public function testGetAvailableThemes() {
        $themes = TableTheme::getAvailableThemes();
        
        $this->assertIsArray($themes);
        $this->assertContains('default', $themes);
        $this->assertContains('dark', $themes);
        $this->assertContains('light', $themes);
        $this->assertContains('colorful', $themes);
        $this->assertContains('minimal', $themes);
        $this->assertContains('professional', $themes);
        $this->assertContains('high-contrast', $themes);
    }
    
    /**
     * @test
     */
    public function testCreateByName() {
        $darkTheme = TableTheme::create('dark');
        $this->assertInstanceOf(TableTheme::class, $darkTheme);
        
        $lightTheme = TableTheme::create('light');
        $this->assertInstanceOf(TableTheme::class, $lightTheme);
        
        $colorfulTheme = TableTheme::create('colorful');
        $this->assertInstanceOf(TableTheme::class, $colorfulTheme);
        
        $minimalTheme = TableTheme::create('minimal');
        $this->assertInstanceOf(TableTheme::class, $minimalTheme);
        
        $professionalTheme = TableTheme::create('professional');
        $this->assertInstanceOf(TableTheme::class, $professionalTheme);
        
        $highContrastTheme = TableTheme::create('high-contrast');
        $this->assertInstanceOf(TableTheme::class, $highContrastTheme);
        
        $defaultTheme = TableTheme::create('invalid-name');
        $this->assertInstanceOf(TableTheme::class, $defaultTheme);
    }
    
    /**
     * @test
     */
    public function testCreateWithAlternativeNames() {
        $highContrastTheme = TableTheme::create('highcontrast');
        $this->assertInstanceOf(TableTheme::class, $highContrastTheme);
        
        $autoTheme = TableTheme::create('environment');
        $this->assertInstanceOf(TableTheme::class, $autoTheme);
        
        $autoTheme2 = TableTheme::create('auto');
        $this->assertInstanceOf(TableTheme::class, $autoTheme2);
    }
    
    /**
     * @test
     */
    public function testStatusColorApplication() {
        $this->theme->setStatusColors([
            'success' => ['color' => 'green'],
            'error' => ['color' => 'red']
        ]);
        
        $successResult = $this->theme->applyCellStyle('success message', 0, 0);
        $errorResult = $this->theme->applyCellStyle('error occurred', 0, 0);
        $normalResult = $this->theme->applyCellStyle('normal text', 0, 0);
        
        $this->assertStringContainsString("\x1b[", $successResult); // Should have color
        $this->assertStringContainsString("\x1b[", $errorResult); // Should have color
        $this->assertEquals('normal text', $normalResult); // Should not have color
    }
    
    /**
     * @test
     */
    public function testColorCodeGeneration() {
        $theme = new TableTheme();
        
        // Test basic colors
        $redResult = $theme->applyHeaderStyle('test');
        $theme->setHeaderColors(['color' => 'red']);
        $redResult = $theme->applyHeaderStyle('test');
        
        $this->assertStringContainsString('test', $redResult);
    }
    
    /**
     * @test
     */
    public function testComplexColorConfiguration() {
        $this->theme->setHeaderColors([
            'color' => 'white',
            'background' => 'blue',
            'bold' => true,
            'underline' => true
        ]);
        
        $result = $this->theme->applyHeaderStyle('Complex Header');
        
        $this->assertStringContainsString('Complex Header', $result);
        $this->assertStringContainsString("\x1b[", $result); // Should have ANSI codes
        $this->assertStringContainsString("\x1b[0m", $result); // Should have reset code
    }
}
