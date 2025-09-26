<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\InputValidator;
use WebFiori\Cli\ArgumentOption;

/**
 * Interactive survey command demonstrating various input methods.
 * 
 * This command shows:
 * - Different types of user input
 * - Input validation and error handling
 * - Default values and optional inputs
 * - Selection menus and confirmations
 * - Data collection and summary
 */
class SurveyCommand extends Command {
    private array $surveyData = [];

    public function __construct() {
        parent::__construct('survey', [
            '--name' => [
                ArgumentOption::DESCRIPTION => 'Pre-fill your name (optional)',
                ArgumentOption::OPTIONAL => true
            ],
            '--quick' => [
                ArgumentOption::DESCRIPTION => 'Use quick mode with minimal questions',
                ArgumentOption::OPTIONAL => true
            ]
        ], 'Interactive survey demonstrating various input methods');
    }

    public function exec(): int {
        $this->println("📋 Welcome to the Interactive Survey!");
        $this->println("=====================================");
        $this->println();

        // Check if we can run interactive survey
        if (!$this->supportsInteractiveInput()) {
            $this->warning("Non-interactive input detected. Using simplified survey mode.");
            return $this->runSimplifiedSurvey();
        }

        $quickMode = $this->isArgProvided('--quick');

        if ($quickMode) {
            $this->info("⚡ Running in quick mode - fewer questions!");
            $this->println();
        }

        // Collect survey data
        $this->collectBasicInfo();
        $this->collectPreferences();

        if (!$quickMode) {
            $this->collectDetailedInfo();
        }

        // Show summary and confirm
        $this->showSummary();

        if ($this->confirm('Submit this survey?', true)) {
            $this->submitSurvey();
        } else {
            $this->warning('Survey cancelled.');

            return 1;
        }

        return 0;
    }

    /**
     * Collect basic information.
     */
    private function collectBasicInfo(): void {
        $this->info("📝 Basic Information");
        $this->println("-------------------");

        // Name (with pre-fill option)
        $preFillName = $this->getArgValue('--name');
        $this->surveyData['name'] = $this->getInput(
            '👤 What\'s your name?',
            $preFillName ?? 'Anonymous'
        );

        // Email with validation
        $this->surveyData['email'] = $this->getInput(
            '📧 Enter your email:',
            null,
            new InputValidator(function ($input) {
                return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
            }, 'Please enter a valid email address')
        );

        // Age with numeric validation
        $age = $this->getInput('🎂 How old are you?', '25');
        $this->surveyData['age'] = is_numeric($age) ? (int)$age : 25;

        // Validate age range
        if ($this->surveyData['age'] < 13 || $this->surveyData['age'] > 120) {
            $this->warning('⚠️  Age seems unusual, but we\'ll accept it!');
        }

        $this->println();
    }

    /**
     * Collect detailed information (only in full mode).
     */
    private function collectDetailedInfo(): void {
        $this->info("📋 Additional Details");
        $this->println("--------------------");

        // Favorite color with custom validation
        $this->surveyData['favorite_color'] = $this->getInput(
            '🎨 What\'s your favorite color?',
            'Blue',
            new InputValidator(function ($input) {
                return preg_match('/^[A-Za-z\s]+$/', trim($input));
            }, 'Please enter only letters and spaces')
        );

        // Rating with range validation
        $this->surveyData['satisfaction'] = $this->getInput(
            '⭐ Rate your satisfaction with CLI tools (1-10):',
            '7',
            new InputValidator(function ($input) {
                $num = (int)$input;

                return $num >= 1 && $num <= 10;
            }, 'Please enter a number between 1 and 10')
        );

        // Optional feedback
        $feedback = $this->getInput('💬 Any additional feedback? (optional):', '');

        if (!empty(trim($feedback))) {
            $this->surveyData['feedback'] = trim($feedback);
        }

        // Newsletter subscription
        $this->surveyData['newsletter'] = $this->confirm('📧 Subscribe to our newsletter?', false);

        $this->println();
    }

    /**
     * Collect user preferences.
     */
    private function collectPreferences(): void {
        $this->info("🎯 Preferences");
        $this->println("-------------");

        // Country selection
        $countries = [
            'United States',
            'Canada', 
            'United Kingdom',
            'Australia',
            'Germany',
            'France',
            'Japan',
            'Other'
        ];

        // Display countries and get selection
        $this->println('🌍 Select your country:');
        foreach ($countries as $i => $country) {
            $this->println("%d: %s", $i, $country);
        }
        $countryInput = $this->getInput('Enter number (0-7)', '0');
        $countryIndex = is_numeric($countryInput) ? (int)$countryInput : 0;
        $countryIndex = max(0, min($countryIndex, count($countries) - 1));
        $this->surveyData['country'] = $countries[$countryIndex];

        // Programming languages (multiple choice simulation)
        $this->println();
        $this->info('💻 Programming experience:');

        $languages = ['PHP', 'JavaScript', 'Python', 'Java', 'C++', 'Go', 'Rust'];
        $knownLanguages = [];

        foreach ($languages as $lang) {
            $answer = $this->getInput("Do you know $lang? (y/N)", 'n');
            if (strtolower($answer) === 'y' || strtolower($answer) === 'yes') {
                $knownLanguages[] = $lang;
            }
        }

        $this->surveyData['languages'] = $knownLanguages;

        // Experience level
        $this->println();
        $experienceLevels = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];
        $this->println('📈 Your programming experience level:');
        foreach ($experienceLevels as $i => $level) {
            $this->println("%d: %s", $i, $level);
        }
        $expInput = $this->getInput('Enter number (0-3)', '1');
        $expIndex = is_numeric($expInput) ? (int)$expInput : 1;
        $expIndex = max(0, min($expIndex, count($experienceLevels) - 1));
        $this->surveyData['experience'] = $experienceLevels[$expIndex];

        $this->println();
    }

    /**
     * Show survey summary.
     */
    private function showSummary(): void {
        $this->success("📊 Survey Summary");
        $this->println("================");

        $this->println("👤 Name: ".$this->surveyData['name']);
        $this->println("📧 Email: ".$this->surveyData['email']);
        $this->println("🎂 Age: ".$this->surveyData['age']);
        $this->println("🌍 Country: ".$this->surveyData['country']);
        $this->println("📈 Experience: ".$this->surveyData['experience']);

        if (!empty($this->surveyData['languages'])) {
            $this->println("💻 Languages: ".implode(', ', $this->surveyData['languages']));
        } else {
            $this->println("💻 Languages: None specified");
        }

        if (isset($this->surveyData['favorite_color'])) {
            $this->println("🎨 Favorite Color: ".$this->surveyData['favorite_color']);
        }

        if (isset($this->surveyData['satisfaction'])) {
            $rating = (int)$this->surveyData['satisfaction'];
            $stars = str_repeat('⭐', $rating).str_repeat('☆', 10 - $rating);
            $this->println("⭐ Satisfaction: $rating/10 $stars");
        }

        if (isset($this->surveyData['feedback'])) {
            $this->println("💬 Feedback: ".$this->surveyData['feedback']);
        }

        if (isset($this->surveyData['newsletter'])) {
            $newsletter = $this->surveyData['newsletter'] ? 'Yes' : 'No';
            $this->println("📧 Newsletter: $newsletter");
        }

        $this->println();
    }

    /**
     * Submit the survey (simulated).
     */
    private function submitSurvey(): void {
        $this->info("📤 Submitting survey...");

        // Simulate processing time
        for ($i = 0; $i < 3; $i++) {
            $this->prints('.');
            usleep(500000); // 0.5 seconds
        }
        $this->println();
    }

    /**
     * Run simplified survey for non-interactive input streams.
     */
    private function runSimplifiedSurvey(): int {
        $this->println();
        
        // Use pre-filled name or default
        $name = $this->getArgValue('--name') ?? 'Anonymous User';
        
        // Simulate survey data collection
        $this->surveyData = [
            'name' => $name,
            'email' => 'user@example.com',
            'age' => 25,
            'country' => 'United States',
            'languages' => ['PHP'],
            'experience' => 'Intermediate',
            'color' => 'Blue',
            'satisfaction' => 8,
            'feedback' => '',
            'newsletter' => false
        ];
        
        $this->success("📊 Survey completed in simplified mode!");
        $this->println();
        
        // Show summary
        $this->showSummary();
        
        // Auto-submit in simplified mode
        $this->info("📤 Auto-submitting survey...");
        $surveyId = 'SRV-' . date('Ymd') . '-' . rand(1000, 9999);
        $this->success("✅ Survey submitted! ID: $surveyId");
        
        return 0;

        $this->success("✅ Thank you for completing the survey!");

        // Generate survey ID
        $surveyId = 'SRV-'.date('Ymd').'-'.rand(1000, 9999);
        $this->info("📋 Survey ID: $surveyId");

        // Show some statistics
        $this->println();
        $this->info("📈 Quick Stats:");
        $this->println("   • Questions answered: ".count($this->surveyData));
        $this->println("   • Languages known: ".count($this->surveyData['languages'] ?? []));
        $this->println("   • Completion time: ~".rand(2, 5)." minutes");

        if (isset($this->surveyData['satisfaction'])) {
            $satisfaction = (int)$this->surveyData['satisfaction'];

            if ($satisfaction >= 8) {
                $this->success("🎉 Great to hear you're satisfied with CLI tools!");
            } elseif ($satisfaction >= 6) {
                $this->info("👍 Thanks for the feedback, we'll keep improving!");
            } else {
                $this->warning("😔 Sorry to hear that. We'll work on making things better!");
            }
        }
    }
}
