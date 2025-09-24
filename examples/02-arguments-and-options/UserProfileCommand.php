<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\ArgumentOption;

/**
 * User profile command that demonstrates comprehensive argument validation.
 * 
 * This command shows:
 * - Required vs optional arguments
 * - Data type validation (email, age, etc.)
 * - Boolean flags
 * - Default values
 * - Complex validation rules
 */
class UserProfileCommand extends Command {
    public function __construct() {
        parent::__construct('profile', [
            '--name' => [
                ArgumentOption::DESCRIPTION => 'User full name (required)',
                ArgumentOption::OPTIONAL => false
            ],
            '--email' => [
                ArgumentOption::DESCRIPTION => 'User email address (required)',
                ArgumentOption::OPTIONAL => false
            ],
            '--age' => [
                ArgumentOption::DESCRIPTION => 'User age (13-120, required)',
                ArgumentOption::OPTIONAL => false
            ],
            '--role' => [
                ArgumentOption::DESCRIPTION => 'User role in the system',
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => 'user',
                ArgumentOption::VALUES => ['user', 'admin', 'moderator', 'guest']
            ],
            '--department' => [
                ArgumentOption::DESCRIPTION => 'User department',
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::DEFAULT => 'General'
            ],
            '--active' => [
                ArgumentOption::DESCRIPTION => 'Mark user as active (flag)',
                ArgumentOption::OPTIONAL => true
            ],
            '--skills' => [
                ArgumentOption::DESCRIPTION => 'Comma-separated list of skills',
                ArgumentOption::OPTIONAL => true
            ],
            '--bio' => [
                ArgumentOption::DESCRIPTION => 'Short biography (max 200 characters)',
                ArgumentOption::OPTIONAL => true
            ]
        ], 'Creates a user profile with validation and formatting');
    }

    public function exec(): int {
        $this->info("🔧 Creating User Profile...");
        $this->println();

        // Collect and validate all arguments
        $profile = $this->collectProfileData();

        if ($profile === null) {
            return 1; // Validation failed
        }

        // Display the created profile
        $this->displayProfile($profile);

        // Save profile (simulated)
        $this->simulateSave($profile);

        return 0;
    }

    /**
     * Collect and validate all profile data.
     */
    private function collectProfileData(): ?array {
        $profile = [];

        // Validate name
        $name = trim($this->getArgValue('--name') ?? '');

        if (empty($name)) {
            $this->error('❌ Name is required and cannot be empty');

            return null;
        }

        if (strlen($name) < 2) {
            $this->error('❌ Name must be at least 2 characters long');

            return null;
        }

        if (strlen($name) > 50) {
            $this->error('❌ Name cannot exceed 50 characters');

            return null;
        }
        $profile['name'] = $name;

        // Validate email
        $email = trim($this->getArgValue('--email') ?? '');

        if (empty($email)) {
            $this->error('❌ Email is required');

            return null;
        }

        if (!$this->validateEmail($email)) {
            $this->error("❌ Invalid email format: $email");

            return null;
        }
        $profile['email'] = $email;

        // Validate age
        $ageStr = $this->getArgValue('--age');

        if (!is_numeric($ageStr)) {
            $this->error('❌ Age must be a number');

            return null;
        }
        $age = (int)$ageStr;

        if (!$this->validateAge($age)) {
            $this->error("❌ Age must be between 13 and 120, got: $age");

            return null;
        }
        $profile['age'] = $age;

        // Get role (already validated by ArgumentOption::VALUES)
        $profile['role'] = $this->getArgValue('--role') ?? 'user';

        // Get department
        $profile['department'] = $this->getArgValue('--department') ?? 'General';

        // Get active status (boolean flag)
        $profile['active'] = $this->isArgProvided('--active');

        // Parse skills
        $skillsStr = $this->getArgValue('--skills');
        $profile['skills'] = $skillsStr ? $this->parseSkills($skillsStr) : [];

        // Validate bio
        $bio = $this->getArgValue('--bio');

        if ($bio !== null) {
            if (strlen($bio) > 200) {
                $this->error('❌ Bio cannot exceed 200 characters');

                return null;
            }
            $profile['bio'] = $bio;
        }

        return $profile;
    }

    /**
     * Display the created profile in a formatted way.
     */
    private function displayProfile(array $profile): void {
        $this->success("✅ User Profile Created Successfully!");
        $this->println();

        // Basic info
        $this->println("👤 Name: ".$profile['name']);
        $this->println("📧 Email: ".$profile['email']);
        $this->println("🎂 Age: ".$profile['age']);
        $this->println("👔 Role: ".$profile['role']);
        $this->println("🏢 Department: ".$profile['department']);

        // Status with color coding
        $status = $profile['active'] ? 'active' : 'inactive';
        $statusIcon = $profile['active'] ? '🟢' : '🔴';
        $this->println("$statusIcon Status: $status");

        // Skills if provided
        if (!empty($profile['skills'])) {
            $this->println("🛠️  Skills: ".implode(', ', $profile['skills']));
        }

        // Bio if provided
        if (isset($profile['bio'])) {
            $this->println("📝 Bio: ".$profile['bio']);
        }

        $this->println();
    }

    /**
     * Parse comma-separated skills.
     */
    private function parseSkills(string $skillsStr): array {
        $skills = array_map('trim', explode(',', $skillsStr));

        return array_filter($skills, function ($skill) {
            return !empty($skill) && strlen($skill) <= 30;
        });
    }

    /**
     * Simulate saving the profile.
     */
    private function simulateSave(array $profile): void {
        $this->info("💾 Saving profile to database...");

        // Simulate processing time
        usleep(500000); // 0.5 seconds

        $userId = rand(1000, 9999);
        $this->success("✅ Profile saved successfully! User ID: $userId");

        // Show summary
        $skillCount = count($profile['skills']);
        $this->info("📊 Profile Summary:");
        $this->println("   • User ID: $userId");
        $this->println("   • Role: ".ucfirst($profile['role']));
        $this->println("   • Skills: $skillCount");
        $this->println("   • Status: ".($profile['active'] ? 'Active' : 'Inactive'));
    }

    /**
     * Validate age range.
     */
    private function validateAge(int $age): bool {
        return $age >= 13 && $age <= 120;
    }

    /**
     * Validate email format.
     */
    private function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
