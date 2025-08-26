# WebFiori CLI Table Feature - Unit Tests

Comprehensive unit test suite for the WebFiori CLI Table feature, providing thorough coverage of all classes and functionality.

## 🎯 Test Coverage

### Core Classes Tested

| Class | Test File | Test Count | Coverage Areas |
|-------|-----------|------------|----------------|
| **TableBuilder** | `TableBuilderTest.php` | 25+ tests | Fluent interface, data management, rendering |
| **TableStyle** | `TableStyleTest.php` | 20+ tests | Style definitions, predefined styles, customization |
| **Column** | `ColumnTest.php` | 30+ tests | Configuration, formatting, alignment, content processing |
| **TableData** | `TableDataTest.php` | 35+ tests | Data container, type detection, statistics, export |
| **TableFormatter** | `TableFormatterTest.php` | 25+ tests | Content formatting, data types, custom formatters |
| **TableTheme** | `TableThemeTest.php` | 20+ tests | Color schemes, theming, ANSI color application |
| **ColumnCalculator** | `ColumnCalculatorTest.php` | 15+ tests | Width calculations, responsive design, optimization |
| **TableRenderer** | `TableRendererTest.php` | 20+ tests | Rendering engine, output generation, visual formatting |

### Total Test Coverage
- **190+ individual test methods**
- **8 test classes** covering all core functionality
- **100% class coverage** of the table feature
- **Edge cases and error conditions** thoroughly tested

## 🚀 Running Tests

### Quick Test Run
```bash
# Run all table tests
cd tests/WebFiori/Cli/Table
php run-tests.php
```

### Using PHPUnit Directly
```bash
# Run with PHPUnit configuration
phpunit --configuration phpunit.xml

# Run specific test class
phpunit TableBuilderTest.php

# Run with coverage report
phpunit --configuration phpunit.xml --coverage-html coverage-html
```

### Individual Test Classes
```bash
# Test specific functionality
php -f TableBuilderTest.php    # Main interface tests
php -f TableStyleTest.php      # Style system tests
php -f ColumnTest.php          # Column configuration tests
php -f TableDataTest.php       # Data management tests
php -f TableFormatterTest.php  # Content formatting tests
php -f TableThemeTest.php      # Color theme tests
php -f ColumnCalculatorTest.php # Width calculation tests
php -f TableRendererTest.php   # Rendering engine tests
```

## 📋 Test Categories

### 1. TableBuilder Tests
- ✅ **Fluent Interface** - Method chaining and return values
- ✅ **Data Management** - Headers, rows, data setting
- ✅ **Configuration** - Column setup, styling, theming
- ✅ **Rendering** - Output generation and display
- ✅ **Edge Cases** - Empty tables, invalid data

### 2. TableStyle Tests
- ✅ **Predefined Styles** - All 8+ built-in styles
- ✅ **Custom Styles** - User-defined styling options
- ✅ **Style Properties** - Border characters, padding, flags
- ✅ **Unicode Support** - Character detection and fallbacks
- ✅ **Border Calculations** - Width and spacing calculations

### 3. Column Tests
- ✅ **Configuration** - Width, alignment, visibility settings
- ✅ **Content Processing** - Formatting, truncation, alignment
- ✅ **Data Types** - Numeric, date, boolean column types
- ✅ **Custom Formatters** - User-defined formatting functions
- ✅ **Color Application** - Status-based colorization
- ✅ **Static Factories** - Convenience creation methods

### 4. TableData Tests
- ✅ **Data Container** - Storage and retrieval functionality
- ✅ **Type Detection** - Automatic data type identification
- ✅ **Statistics** - Column analysis and metrics
- ✅ **Data Operations** - Filtering, sorting, transformation
- ✅ **Export Formats** - JSON, CSV, array conversions
- ✅ **Import Methods** - Creating from various data sources

### 5. TableFormatter Tests
- ✅ **Content Formatting** - Header and cell processing
- ✅ **Data Type Handling** - Numbers, dates, booleans, etc.
- ✅ **Custom Formatters** - Registration and application
- ✅ **Built-in Formatters** - Currency, percentage, file size
- ✅ **Text Processing** - Truncation and smart formatting

### 6. TableTheme Tests
- ✅ **Color Schemes** - Predefined theme variations
- ✅ **ANSI Colors** - Color code generation and application
- ✅ **Theme Configuration** - Custom color setups
- ✅ **Style Application** - Header and cell styling
- ✅ **Status Colors** - Conditional color application

### 7. ColumnCalculator Tests
- ✅ **Width Calculations** - Optimal column sizing
- ✅ **Responsive Design** - Terminal width adaptation
- ✅ **Constraint Handling** - Min/max width enforcement
- ✅ **Auto Configuration** - Intelligent column setup
- ✅ **Edge Cases** - Narrow terminals, large datasets

### 8. TableRenderer Tests
- ✅ **Rendering Engine** - Complete table generation
- ✅ **Style Integration** - Visual formatting application
- ✅ **Theme Integration** - Color and styling application
- ✅ **Output Structure** - Border generation, alignment
- ✅ **Content Processing** - Data formatting and display

## 🔍 Test Quality Assurance

### Test Principles
- **Comprehensive Coverage** - All public methods tested
- **Edge Case Handling** - Invalid inputs, boundary conditions
- **Integration Testing** - Component interaction verification
- **Performance Awareness** - Efficient test execution
- **Maintainability** - Clear, readable test code

### Test Data
- **Realistic Datasets** - Real-world data scenarios
- **Edge Cases** - Empty data, null values, extreme sizes
- **Type Variations** - Different data types and formats
- **Unicode Content** - International characters and symbols
- **Large Datasets** - Performance and memory testing

### Assertions
- **Functional Correctness** - Expected behavior verification
- **Type Safety** - Return type and parameter validation
- **State Consistency** - Object state after operations
- **Output Quality** - Generated content verification
- **Error Handling** - Exception and error conditions

## 📊 Test Results Example

```
🧪 WebFiori CLI Table Feature - Unit Test Suite
===============================================

Adding test class: TableBuilder (Main Interface)
Adding test class: TableStyle (Visual Styling)
Adding test class: Column (Column Configuration)
Adding test class: TableData (Data Management)
Adding test class: TableFormatter (Content Formatting)
Adding test class: TableTheme (Color Themes)
Adding test class: ColumnCalculator (Width Calculations)
Adding test class: TableRenderer (Rendering Engine)

🚀 Running Tests...
==================

PHPUnit 9.5.x by Sebastian Bergmann and contributors.

........................................................................  72 / 190 ( 37%)
........................................................................  144 / 190 ( 75%)
..............................................                            190 / 190 (100%)

Time: 00:02.543, Memory: 12.00 MB

OK (190 tests, 450 assertions)

📊 Test Summary
===============
Tests Run: 190
Failures: 0
Errors: 0
Skipped: 0
Warnings: 0

✅ All tests passed successfully!
🎉 WebFiori CLI Table feature is working correctly.
```

## 🛠️ Development Workflow

### Adding New Tests
1. **Create test method** with descriptive name
2. **Follow naming convention** - `testMethodName()`
3. **Use @test annotation** for clarity
4. **Include setup/teardown** as needed
5. **Add comprehensive assertions**

### Test Method Template
```php
/**
 * @test
 */
public function testSpecificFunctionality() {
    // Arrange
    $input = 'test data';
    $expected = 'expected result';
    
    // Act
    $result = $this->objectUnderTest->methodToTest($input);
    
    // Assert
    $this->assertEquals($expected, $result);
    $this->assertInstanceOf(ExpectedClass::class, $result);
}
```

### Best Practices
- **One concept per test** - Focus on single functionality
- **Descriptive names** - Clear test purpose
- **Arrange-Act-Assert** - Structured test organization
- **Independent tests** - No test dependencies
- **Fast execution** - Efficient test implementation

## 🔧 Continuous Integration

### Automated Testing
- **Pre-commit hooks** - Run tests before commits
- **CI/CD integration** - Automated test execution
- **Coverage reporting** - Track test coverage metrics
- **Performance monitoring** - Test execution time tracking

### Quality Gates
- **100% test pass rate** - All tests must pass
- **Minimum coverage** - Maintain high coverage levels
- **Performance benchmarks** - Test execution time limits
- **Code quality** - Static analysis integration

## 📚 Additional Resources

- **PHPUnit Documentation** - [https://phpunit.de/documentation.html](https://phpunit.de/documentation.html)
- **WebFiori CLI Guide** - Main project documentation
- **Table Feature Documentation** - `WebFiori/Cli/Table/README.md`
- **Example Usage** - `examples/15-table-display/`

---

This comprehensive test suite ensures the WebFiori CLI Table feature is robust, reliable, and ready for production use.
