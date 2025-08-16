<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\Option;
use WebFiori\Cli\InputValidator;

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
                Option::DESCRIPTION => 'Pre-fill your name (optional)',
                Option::OPTIONAL => true
            ],
            '--quick' => [
                Option::DESCRIPTION => 'Use quick mode with minimal questions',
                Option::OPTIONAL => true
            ]
        ], 'Interactive survey demonstrating various input methods');
    }
    
    public function exec(): int {
        $this->println("📋 Welcome to the Interactive Survey!");
        $this->println("=====================================");
        $this->println();
        
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
            new InputValidator(function($input) {
                return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
            }, 'Please enter a valid email address')
        );
        
        // Age with numeric validation
        $this->surveyData['age'] = $this->readInteger(
            '🎂 How old are you?',
            25
        );
        
        // Validate age range
        if ($this->surveyData['age'] < 13 || $this->surveyData['age'] > 120) {
            $this->warning('⚠️  Age seems unusual, but we\'ll accept it!');
        }
        
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
        
        $countryIndex = $this->select('🌍 Select your country:', $countries, 0);
        $this->surveyData['country'] = $countries[$countryIndex];
        
        // Programming languages (multiple choice simulation)
        $this->println();
        $this->info('💻 Programming experience:');
        
        $languages = ['PHP', 'JavaScript', 'Python', 'Java', 'C++', 'Go', 'Rust'];
        $knownLanguages = [];
        
        foreach ($languages as $lang) {
            if ($this->confirm("Do you know $lang?", false)) {
                $knownLanguages[] = $lang;
            }
        }
        
        $this->surveyData['languages'] = $knownLanguages;
        
        // Experience level
        $this->println();
        $experienceLevels = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];
        $expIndex = $this->select('📈 Your programming experience level:', $experienceLevels, 1);
        $this->surveyData['experience'] = $experienceLevels[$expIndex];
        
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
            new InputValidator(function($input) {
                return preg_match('/^[A-Za-z\s]+$/', trim($input));
            }, 'Please enter only letters and spaces')
        );
        
        // Rating with range validation
        $this->surveyData['satisfaction'] = $this->getInput(
            '⭐ Rate your satisfaction with CLI tools (1-10):',
            '7',
            new InputValidator(function($input) {
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
     * Show survey summary.
     */
    private function showSummary(): void {
        $this->success("📊 Survey Summary");
        $this->println("================");
        
        $this->println("👤 Name: " . $this->surveyData['name']);
        $this->println("📧 Email: " . $this->surveyData['email']);
        $this->println("🎂 Age: " . $this->surveyData['age']);
        $this->println("🌍 Country: " . $this->surveyData['country']);
        $this->println("📈 Experience: " . $this->surveyData['experience']);
        
        if (!empty($this->surveyData['languages'])) {
            $this->println("💻 Languages: " . implode(', ', $this->surveyData['languages']));
        } else {
            $this->println("💻 Languages: None specified");
        }
        
        if (isset($this->surveyData['favorite_color'])) {
            $this->println("🎨 Favorite Color: " . $this->surveyData['favorite_color']);
        }
        
        if (isset($this->surveyData['satisfaction'])) {
            $rating = (int)$this->surveyData['satisfaction'];
            $stars = str_repeat('⭐', $rating) . str_repeat('☆', 10 - $rating);
            $this->println("⭐ Satisfaction: $rating/10 $stars");
        }
        
        if (isset($this->surveyData['feedback'])) {
            $this->println("💬 Feedback: " . $this->surveyData['feedback']);
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
        
        $this->success("✅ Thank you for completing the survey!");
        
        // Generate survey ID
        $surveyId = 'SRV-' . date('Ymd') . '-' . rand(1000, 9999);
        $this->info("📋 Survey ID: $surveyId");
        
        // Show some statistics
        $this->println();
        $this->info("📈 Quick Stats:");
        $this->println("   • Questions answered: " . count($this->surveyData));
        $this->println("   • Languages known: " . count($this->surveyData['languages'] ?? []));
        $this->println("   • Completion time: ~" . rand(2, 5) . " minutes");
        
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
