<?php

use WebFiori\CLI\Command;
use WebFiori\CLI\Option;

/**
 * Calculator command that demonstrates various argument types and validation.
 * 
 * This command shows:
 * - Required arguments with value constraints
 * - Optional arguments with defaults
 * - Array-like argument processing
 * - Mathematical operations
 * - Input validation and error handling
 */
class CalculatorCommand extends Command {
    public function __construct() {
        parent::__construct('calc', [
            '--operation' => [
                Option::DESCRIPTION => 'Mathematical operation to perform',
                Option::OPTIONAL => false,
                Option::VALUES => ['add', 'subtract', 'multiply', 'divide', 'average']
            ],
            '--numbers' => [
                Option::DESCRIPTION => 'Comma-separated list of numbers (e.g., "1,2,3,4")',
                Option::OPTIONAL => false
            ],
            '--precision' => [
                Option::DESCRIPTION => 'Number of decimal places for the result',
                Option::OPTIONAL => true,
                Option::DEFAULT => '2'
            ],
            '--verbose' => [
                Option::DESCRIPTION => 'Show detailed calculation steps',
                Option::OPTIONAL => true
            ]
        ], 'Performs mathematical calculations on a list of numbers');
    }

    public function exec(): int {
        // Get and validate arguments
        $operation = $this->getArgValue('--operation');
        $numbersStr = $this->getArgValue('--numbers');
        $precision = (int)($this->getArgValue('--precision') ?? 2);
        $verbose = $this->isArgProvided('--verbose');

        // Parse and validate numbers
        $numbers = $this->parseNumbers($numbersStr);

        if (empty($numbers)) {
            $this->error('No valid numbers provided. Please provide comma-separated numbers.');
            $this->info('Example: --numbers="1,2,3,4.5"');

            return 1;
        }

        // Validate precision
        if ($precision < 0 || $precision > 10) {
            $this->error('Precision must be between 0 and 10');

            return 1;
        }

        // Show input if verbose
        if ($verbose) {
            $this->info("🔢 Operation: ".ucfirst($operation));
            $this->info("📊 Numbers: ".implode(', ', $numbers));
            $this->info("🎯 Precision: $precision decimal places");
            $this->println();
        }

        // Perform calculation
        try {
            $result = $this->performCalculation($operation, $numbers);

            // Display result
            $this->success("✅ Performing $operation on: ".implode(', ', $numbers));
            $this->println("📊 Result: ".number_format($result, $precision));

            // Show additional info if verbose
            if ($verbose) {
                $this->println();
                $this->info("📈 Statistics:");
                $this->println("   • Count: ".count($numbers));
                $this->println("   • Min: ".min($numbers));
                $this->println("   • Max: ".max($numbers));

                if ($operation !== 'average') {
                    $this->println("   • Average: ".number_format(array_sum($numbers) / count($numbers), $precision));
                }
            }
        } catch (Exception $e) {
            $this->error("❌ Calculation error: ".$e->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * Parse comma-separated numbers string into array of floats.
     */
    private function parseNumbers(string $numbersStr): array {
        $parts = array_map('trim', explode(',', $numbersStr));
        $numbers = [];

        foreach ($parts as $part) {
            if (is_numeric($part)) {
                $numbers[] = (float)$part;
            } else if (!empty($part)) {
                $this->warning("⚠️  Ignoring invalid number: '$part'");
            }
        }

        return $numbers;
    }

    /**
     * Perform the mathematical operation.
     */
    private function performCalculation(string $operation, array $numbers): float {
        switch ($operation) {
            case 'add':
                return array_sum($numbers);

            case 'subtract':
                if (count($numbers) < 2) {
                    throw new Exception('Subtraction requires at least 2 numbers');
                }
                $result = $numbers[0];

                for ($i = 1; $i < count($numbers); $i++) {
                    $result -= $numbers[$i];
                }

                return $result;

            case 'multiply':
                $result = 1;

                foreach ($numbers as $number) {
                    $result *= $number;
                }

                return $result;

            case 'divide':
                if (count($numbers) < 2) {
                    throw new Exception('Division requires at least 2 numbers');
                }
                $result = $numbers[0];

                for ($i = 1; $i < count($numbers); $i++) {
                    if ($numbers[$i] == 0) {
                        throw new Exception('Division by zero is not allowed');
                    }
                    $result /= $numbers[$i];
                }

                return $result;

            case 'average':
                return array_sum($numbers) / count($numbers);

            default:
                throw new Exception("Unknown operation: $operation");
        }
    }
}
