<?php

use WebFiori\Cli\Command;
use WebFiori\Cli\ArgumentOption;

/**
 * Interactive menu system demonstrating complex CLI navigation.
 * 
 * This command shows:
 * - Multi-level menu structures
 * - Navigation and breadcrumbs
 * - State management
 * - User experience patterns
 * - Dynamic menu generation
 */
class InteractiveMenuCommand extends Command {
    private array $breadcrumbs = [];

    private array $menuStack = [];
    private bool $running = true;
    private int $failedTries = 0;
    private const MAX_FAILED_TRIES = 5;

    public function __construct() {
        parent::__construct('menu', [
            '--section' => [
                ArgumentOption::DESCRIPTION => 'Start in specific menu section',
                ArgumentOption::OPTIONAL => true,
                ArgumentOption::VALUES => ['users', 'settings', 'reports', 'tools']
            ]
        ], 'Interactive multi-level menu system with navigation');
    }

    public function exec(): int {
        $startSection = $this->getArgValue('--section');

        $this->showWelcome();

        // Initialize menu stack
        $this->menuStack = ['main'];
        $this->breadcrumbs = ['Main Menu'];

        // Jump to specific section if requested
        if ($startSection) {
            $this->navigateToSection($startSection);
        }

        // Main menu loop
        while ($this->running) {
            $this->displayCurrentMenu();
            $choice = $this->getUserChoice();
            $this->handleMenuChoice($choice);
        }

        $this->showGoodbye();

        return 0;
    }

    /**
     * Display the current menu.
     */
    private function displayCurrentMenu(): void {
        $this->clearConsole();

        // Show breadcrumbs
        $this->info("📍 Current: ".implode(' > ', $this->breadcrumbs));
        $this->println();

        $currentMenu = end($this->menuStack);

        switch ($currentMenu) {
            case 'main':
                $this->displayMainMenu();
                break;
            case 'users':
                $this->displayUsersMenu();
                break;
            case 'settings':
                $this->displaySettingsMenu();
                break;
            case 'reports':
                $this->displayReportsMenu();
                break;
            case 'tools':
                $this->displayToolsMenu();
                break;
            case 'user-create':
                $this->displayUserCreateForm();
                break;
            case 'system-config':
                $this->displaySystemConfig();
                break;
            default:
                $this->displayMainMenu();
        }
    }

    /**
     * Display main menu.
     */
    private function displayMainMenu(): void {
        $this->success("📋 Main Menu:");
        $this->println();

        $options = [
            1 => '👥 User Management',
            2 => '⚙️  System Settings',
            3 => '📊 Reports & Analytics',
            4 => '🔧 Tools & Utilities',
            5 => '❓ Help & Documentation'
        ];

        foreach ($options as $num => $option) {
            $this->println("  $num. $option");
        }

        $this->println();
        $this->println("  0. 🚪 Exit");
        $this->println();
    }

    /**
     * Display reports menu.
     */
    private function displayReportsMenu(): void {
        $this->success("📊 Reports & Analytics:");
        $this->println();

        $options = [
            1 => '📈 Usage Statistics',
            2 => '👥 User Activity Report',
            3 => '🚨 Error Log Analysis',
            4 => '⚡ Performance Metrics',
            5 => '💾 Storage Usage Report',
            6 => '📅 Custom Date Range Report'
        ];

        foreach ($options as $num => $option) {
            $this->println("  $num. $option");
        }

        $this->println();
        $this->println("  9. ⬅️  Back to Main Menu");
        $this->println();
    }

    /**
     * Display settings menu.
     */
    private function displaySettingsMenu(): void {
        $this->success("⚙️  System Settings:");
        $this->println();

        $options = [
            1 => '🖥️  System Configuration',
            2 => '🎨 Appearance Settings',
            3 => '🔐 Security Settings',
            4 => '📧 Email Configuration',
            5 => '🗄️  Database Settings',
            6 => '📝 Logging Configuration'
        ];

        foreach ($options as $num => $option) {
            $this->println("  $num. $option");
        }

        $this->println();
        $this->println("  9. ⬅️  Back to Main Menu");
        $this->println();
    }

    /**
     * Display system configuration.
     */
    private function displaySystemConfig(): void {
        $this->success("🖥️  System Configuration");
        $this->println("======================");
        $this->println();

        $this->info("Current Settings:");
        $this->println("   • Application Name: MyApp");
        $this->println("   • Version: 1.0.0");
        $this->println("   • Environment: Development");
        $this->println("   • Debug Mode: Enabled");
        $this->println("   • Timezone: UTC");
        $this->println();

        $options = [
            1 => 'Change Application Name',
            2 => 'Update Environment',
            3 => 'Toggle Debug Mode',
            4 => 'Set Timezone',
            5 => 'Reset to Defaults'
        ];

        foreach ($options as $num => $option) {
            $this->println("  $num. $option");
        }

        $this->println();
        $this->println("  9. ⬅️  Back to Settings");
        $this->println();

        $choice = $this->getUserChoice();

        if ($choice >= 1 && $choice <= 5) {
            $this->handleSystemConfigAction($choice);
        } elseif ($choice == 9) {
            $this->goBack();
        }
    }

    /**
     * Display tools menu.
     */
    private function displayToolsMenu(): void {
        $this->success("🔧 Tools & Utilities:");
        $this->println();

        $options = [
            1 => '🧹 System Cleanup',
            2 => '💾 Database Backup',
            3 => '🔄 Data Import/Export',
            4 => '🔍 System Diagnostics',
            5 => '🛠️  Maintenance Mode',
            6 => '📦 Update Manager'
        ];

        foreach ($options as $num => $option) {
            $this->println("  $num. $option");
        }

        $this->println();
        $this->println("  9. ⬅️  Back to Main Menu");
        $this->println();
    }

    /**
     * Display user creation form.
     */
    private function displayUserCreateForm(): void {
        $this->success("➕ Create New User");
        $this->println("================");
        $this->println();

        $this->info("Please enter user details:");
        $this->println();

        // Simulate form
        $name = $this->getInput('👤 Full Name: ');
        $email = $this->getInput('📧 Email Address: ');
        $role = $this->select('👔 Role:', ['User', 'Admin', 'Moderator'], 0);

        $this->println();
        $this->info("📋 User Summary:");
        $this->println("   • Name: $name");
        $this->println("   • Email: $email");
        $this->println("   • Role: ".['User', 'Admin', 'Moderator'][$role]);
        $this->println();

        if ($this->confirm('Create this user?', true)) {
            $this->success("✅ User '$name' created successfully!");
        } else {
            $this->warning("❌ User creation cancelled.");
        }

        $this->println();
        $this->println("Press Enter to continue...");
        $this->readln();

        // Go back to users menu
        $this->goBack();
    }

    /**
     * Display users menu.
     */
    private function displayUsersMenu(): void {
        $this->success("👥 User Management:");
        $this->println();

        $options = [
            1 => '📋 List All Users',
            2 => '➕ Create New User',
            3 => '✏️  Edit User',
            4 => '🗑️  Delete User',
            5 => '🔍 Search Users',
            6 => '📈 User Statistics'
        ];

        foreach ($options as $num => $option) {
            $this->println("  $num. $option");
        }

        $this->println();
        $this->println("  9. ⬅️  Back to Main Menu");
        $this->println();
    }

    /**
     * Get user choice.
     */
    private function getUserChoice(): string {
        $this->prints("Your choice: ", ['color' => 'yellow', 'bold' => true]);

        return trim($this->readln());
    }

    /**
     * Go back to previous menu.
     */
    private function goBack(): void {
        if (count($this->menuStack) > 1) {
            array_pop($this->menuStack);
            array_pop($this->breadcrumbs);
        }
    }

    /**
     * Go to main menu.
     */
    private function goHome(): void {
        $this->menuStack = ['main'];
        $this->breadcrumbs = ['Main Menu'];
    }

    /**
     * Handle main menu choices.
     */
    private function handleMainMenuChoice(int $choice): void {
        $this->failedTries = 0; // Reset on valid choice
        
        switch ($choice) {
            case 0:
                $this->running = false;
                break;
            case 1:
                $this->navigateTo('users', 'User Management');
                break;
            case 2:
                $this->navigateTo('settings', 'System Settings');
                break;
            case 3:
                $this->navigateTo('reports', 'Reports & Analytics');
                break;
            case 4:
                $this->navigateTo('tools', 'Tools & Utilities');
                break;
            case 5:
                $this->showHelp();
                break;
            default:
                $this->invalidChoice();
        }
    }

    /**
     * Handle menu choice.
     */
    private function handleMenuChoice(string $choice): void {
        // Handle special commands
        $lowerChoice = strtolower($choice);

        if (in_array($lowerChoice, ['exit', 'quit', 'q'])) {
            $this->running = false;

            return;
        }

        if (in_array($lowerChoice, ['back', 'b'])) {
            $this->goBack();

            return;
        }

        if (in_array($lowerChoice, ['home', 'h'])) {
            $this->goHome();

            return;
        }

        // Handle numeric choices
        if (!is_numeric($choice)) {
            $this->failedTries++;
            $this->error("Invalid choice. Please enter a number or command. ({$this->failedTries}/" . self::MAX_FAILED_TRIES . ")");
            
            if ($this->failedTries >= self::MAX_FAILED_TRIES) {
                $this->error("Too many invalid attempts. Exiting...");
                $this->running = false;
                return;
            }
            
            $this->println("Press Enter to continue...");
            $this->readln();

            return;
        }

        // Reset counter on valid input
        $this->failedTries = 0;

        $choice = (int)$choice;
        $currentMenu = end($this->menuStack);

        switch ($currentMenu) {
            case 'main':
                $this->handleMainMenuChoice($choice);
                break;
            case 'users':
                $this->handleUsersMenuChoice($choice);
                break;
            case 'settings':
                $this->handleSettingsMenuChoice($choice);
                break;
            case 'reports':
                $this->handleReportsMenuChoice($choice);
                break;
            case 'tools':
                $this->handleToolsMenuChoice($choice);
                break;
        }
    }

    /**
     * Handle reports menu choices.
     */
    private function handleReportsMenuChoice(int $choice): void {
        $this->failedTries = 0; // Reset on valid choice
        
        switch ($choice) {
            case 1:
                $this->showUsageStats();
                break;
            case 2:
                $this->showUserActivity();
                break;
            case 3:
                $this->showErrorAnalysis();
                break;
            case 4:
                $this->showPerformanceMetrics();
                break;
            case 5:
                $this->showStorageReport();
                break;
            case 6:
                $this->showCustomReport();
                break;
            case 9:
                $this->goBack();
                break;
            default:
                $this->invalidChoice();
        }
    }

    /**
     * Handle settings menu choices.
     */
    private function handleSettingsMenuChoice(int $choice): void {
        $this->failedTries = 0; // Reset on valid choice
        
        switch ($choice) {
            case 1:
                $this->navigateTo('system-config', 'System Configuration');
                break;
            case 2:
                $this->showAppearanceSettings();
                break;
            case 3:
                $this->showSecuritySettings();
                break;
            case 4:
                $this->showEmailConfig();
                break;
            case 5:
                $this->showDatabaseSettings();
                break;
            case 6:
                $this->showLoggingConfig();
                break;
            case 9:
                $this->goBack();
                break;
            default:
                $this->invalidChoice();
        }
    }

    private function handleSystemConfigAction(int $action): void {
        $actions = [
            1 => "Change Application Name",
            2 => "Update Environment", 
            3 => "Toggle Debug Mode",
            4 => "Set Timezone",
            5 => "Reset to Defaults"
        ];

        $this->showPlaceholder($actions[$action] ?? "Unknown Action");
    }

    /**
     * Handle tools menu choices.
     */
    private function handleToolsMenuChoice(int $choice): void {
        $this->failedTries = 0; // Reset on valid choice
        
        switch ($choice) {
            case 1:
                $this->runSystemCleanup();
                break;
            case 2:
                $this->runDatabaseBackup();
                break;
            case 3:
                $this->showDataImportExport();
                break;
            case 4:
                $this->runSystemDiagnostics();
                break;
            case 5:
                $this->toggleMaintenanceMode();
                break;
            case 6:
                $this->showUpdateManager();
                break;
            case 9:
                $this->goBack();
                break;
            default:
                $this->invalidChoice();
        }
    }

    /**
     * Handle users menu choices.
     */
    private function handleUsersMenuChoice(int $choice): void {
        $this->failedTries = 0; // Reset on valid choice
        
        switch ($choice) {
            case 1:
                $this->showUsersList();
                break;
            case 2:
                $this->navigateTo('user-create', 'Create User');
                break;
            case 3:
                $this->showEditUser();
                break;
            case 4:
                $this->showDeleteUser();
                break;
            case 5:
                $this->showSearchUsers();
                break;
            case 6:
                $this->showUserStats();
                break;
            case 9:
                $this->goBack();
                break;
            default:
                $this->invalidChoice();
        }
    }

    /**
     * Show invalid choice message.
     */
    private function invalidChoice(): void {
        $this->failedTries++;
        $this->error("Invalid choice. Please try again. ({$this->failedTries}/" . self::MAX_FAILED_TRIES . ")");
        
        if ($this->failedTries >= self::MAX_FAILED_TRIES) {
            $this->error("Too many invalid attempts. Exiting...");
            $this->running = false;
            return;
        }
        
        $this->println("Press Enter to continue...");
        $this->readln();
    }

    /**
     * Navigate to a menu section.
     */
    private function navigateTo(string $menu, string $title): void {
        $this->menuStack[] = $menu;
        $this->breadcrumbs[] = $title;
    }

    /**
     * Navigate to specific section.
     */
    private function navigateToSection(string $section): void {
        $sectionMap = [
            'users' => ['users', 'User Management'],
            'settings' => ['settings', 'System Settings'],
            'reports' => ['reports', 'Reports & Analytics'],
            'tools' => ['tools', 'Tools & Utilities']
        ];

        if (isset($sectionMap[$section])) {
            [$menu, $title] = $sectionMap[$section];
            $this->navigateTo($menu, $title);
        }
    }
    private function runDatabaseBackup(): void {
        $this->showPlaceholder("Database Backup");
    }
    private function runSystemCleanup(): void {
        $this->showPlaceholder("System Cleanup");
    }
    private function runSystemDiagnostics(): void {
        $this->showPlaceholder("System Diagnostics");
    }
    private function showAppearanceSettings(): void {
        $this->showPlaceholder("Appearance Settings");
    }
    private function showCustomReport(): void {
        $this->showPlaceholder("Custom Date Range Report");
    }
    private function showDatabaseSettings(): void {
        $this->showPlaceholder("Database Settings");
    }
    private function showDataImportExport(): void {
        $this->showPlaceholder("Data Import/Export");
    }
    private function showDeleteUser(): void {
        $this->showPlaceholder("Delete User");
    }
    private function showEditUser(): void {
        $this->showPlaceholder("Edit User");
    }
    private function showEmailConfig(): void {
        $this->showPlaceholder("Email Configuration");
    }
    private function showErrorAnalysis(): void {
        $this->showPlaceholder("Error Log Analysis");
    }

    /**
     * Show goodbye message.
     */
    private function showGoodbye(): void {
        $this->clearConsole();
        $this->success("👋 Thank you for using the Interactive Menu System!");
        $this->info("Have a great day!");
    }

    /**
     * Show help information.
     */
    private function showHelp(): void {
        $this->clearConsole();
        $this->success("❓ Help & Documentation");
        $this->println("======================");
        $this->println();

        $this->info("📖 Available Commands:");
        $this->println("   • Numbers (1-9): Select menu options");
        $this->println("   • 'back' or 'b': Go to previous menu");
        $this->println("   • 'home' or 'h': Go to main menu");
        $this->println("   • 'exit' or 'q': Quit application");
        $this->println();

        $this->info("🎯 Quick Navigation:");
        $this->println("   • Use --section=users to start in User Management");
        $this->println("   • Use --section=settings for System Settings");
        $this->println("   • Use --section=reports for Reports & Analytics");
        $this->println("   • Use --section=tools for Tools & Utilities");
        $this->println();

        $this->println("Press Enter to continue...");
        $this->readln();
    }
    private function showLoggingConfig(): void {
        $this->showPlaceholder("Logging Configuration");
    }
    private function showPerformanceMetrics(): void {
        $this->showPlaceholder("Performance Metrics");
    }

    /**
     * Show placeholder for unimplemented features.
     */
    private function showPlaceholder(string $feature): void {
        $this->clearConsole();
        $this->info("🚧 $feature");
        $this->println(str_repeat('=', strlen($feature) + 4));
        $this->println();
        $this->warning("This feature is not yet implemented in this demo.");
        $this->info("In a real application, this would show the $feature interface.");
        $this->println();
        $this->println("Press Enter to go back...");
        $this->readln();
    }
    private function showSearchUsers(): void {
        $this->showPlaceholder("Search Users");
    }
    private function showSecuritySettings(): void {
        $this->showPlaceholder("Security Settings");
    }
    private function showStorageReport(): void {
        $this->showPlaceholder("Storage Usage Report");
    }
    private function showUpdateManager(): void {
        $this->showPlaceholder("Update Manager");
    }
    private function showUsageStats(): void {
        $this->showPlaceholder("Usage Statistics");
    }
    private function showUserActivity(): void {
        $this->showPlaceholder("User Activity Report");
    }

    // Placeholder methods for menu actions
    private function showUsersList(): void {
        $this->showPlaceholder("Users List");
    }
    private function showUserStats(): void {
        $this->showPlaceholder("User Statistics");
    }

    /**
     * Show welcome message.
     */
    private function showWelcome(): void {
        $this->clearConsole();
        $this->println("🎛️  Interactive Menu System");
        $this->println("========================");
        $this->println();
        $this->info("💡 Navigation Tips:");
        $this->println("   • Enter number to select option");
        $this->println("   • Type 'back' or 'b' to go back");
        $this->println("   • Type 'home' or 'h' to go to main menu");
        $this->println("   • Type 'exit' or 'q' to quit");
        $this->println();
        $this->println("Press Enter to continue...");
        $this->readln();
    }
    private function toggleMaintenanceMode(): void {
        $this->showPlaceholder("Maintenance Mode");
    }
}
