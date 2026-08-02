<?php

namespace tests\WebFiori\Cli\Table;

use PHPUnit\Framework\TestSuite;

/**
 * Complete test suite for WebFiori CLI Table feature.
 * 
 * This test suite includes all unit tests for the table functionality,
 * providing comprehensive coverage of all classes and features.
 */
class TableTestSuite extends TestSuite {
    
    public static function suite(): TestSuite {
        $suite = new TestSuite('WebFiori CLI Table Feature Tests');
        
        // Add all test classes
        $suite->addTestSuite(TableBuilderTest::class);
        $suite->addTestSuite(TableStyleTest::class);
        $suite->addTestSuite(ColumnTest::class);
        $suite->addTestSuite(TableDataTest::class);
        $suite->addTestSuite(TableFormatterTest::class);
        $suite->addTestSuite(TableThemeTest::class);
        $suite->addTestSuite(ColumnCalculatorTest::class);
        $suite->addTestSuite(TableRendererTest::class);
        
        return $suite;
    }
}
