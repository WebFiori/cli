<?php

/**
 * Test Runner for WebFiori CLI Table Feature
 * 
 * This script runs all unit tests for the table functionality
 * and provides a summary of test results.
 */

require_once '../../../../vendor/autoload.php';

// Include all test classes
require_once 'TableBuilderTest.php';
require_once 'TableStyleTest.php';
require_once 'ColumnTest.php';
require_once 'TableDataTest.php';
require_once 'TableFormatterTest.php';
require_once 'TableThemeTest.php';
require_once 'ColumnCalculatorTest.php';
require_once 'TableRendererTest.php';

use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestRunner;
use tests\WebFiori\Cli\Table\TableBuilderTest;
use tests\WebFiori\Cli\Table\TableStyleTest;
use tests\WebFiori\Cli\Table\ColumnTest;
use tests\WebFiori\Cli\Table\TableDataTest;
use tests\WebFiori\Cli\Table\TableFormatterTest;
use tests\WebFiori\Cli\Table\TableThemeTest;
use tests\WebFiori\Cli\Table\ColumnCalculatorTest;
use tests\WebFiori\Cli\Table\TableRendererTest;

echo "🧪 WebFiori CLI Table Feature - Unit Test Suite\n";
echo "===============================================\n\n";

// Create test suite
$suite = new TestSuite('WebFiori CLI Table Tests');

// Add all test classes
$testClasses = [
    TableBuilderTest::class => 'TableBuilder (Main Interface)',
    TableStyleTest::class => 'TableStyle (Visual Styling)',
    ColumnTest::class => 'Column (Column Configuration)',
    TableDataTest::class => 'TableData (Data Management)',
    TableFormatterTest::class => 'TableFormatter (Content Formatting)',
    TableThemeTest::class => 'TableTheme (Color Themes)',
    ColumnCalculatorTest::class => 'ColumnCalculator (Width Calculations)',
    TableRendererTest::class => 'TableRenderer (Rendering Engine)'
];

foreach ($testClasses as $testClass => $description) {
    echo "Adding test class: $description\n";
    $suite->addTestSuite($testClass);
}

echo "\n🚀 Running Tests...\n";
echo "==================\n\n";

// Run the tests
$runner = new TestRunner();
$result = $runner->run($suite);

// Display summary
echo "\n📊 Test Summary\n";
echo "===============\n";
echo "Tests Run: " . $result->count() . "\n";
echo "Failures: " . $result->failureCount() . "\n";
echo "Errors: " . $result->errorCount() . "\n";
echo "Skipped: " . $result->skippedCount() . "\n";
echo "Warnings: " . $result->warningCount() . "\n";

if ($result->wasSuccessful()) {
    echo "\n✅ All tests passed successfully!\n";
    echo "🎉 WebFiori CLI Table feature is working correctly.\n";
    exit(0);
} else {
    echo "\n❌ Some tests failed.\n";
    echo "Please review the test output above for details.\n";
    exit(1);
}
