# Interactive Commands Example

This example demonstrates building complex interactive CLI workflows with menus, wizards, and dynamic user interfaces.

## 🎯 What You'll Learn

- Creating interactive menu systems
- Building step-by-step wizards
- Dynamic command flows
- State management in CLI apps
- User experience best practices
- Error recovery and navigation

## 📁 Files

- `InteractiveMenuCommand.php` - Multi-level menu system
- `ProjectWizardCommand.php` - Project creation wizard
- `GameCommand.php` - Interactive CLI game
- `main.php` - Application entry point
- `README.md` - This documentation

## 🚀 Running the Examples

### Interactive Menu
```bash
# Start the interactive menu
php main.php menu

# Menu with specific starting section
php main.php menu --section=settings
```

### Project Wizard
```bash
# Create a new project interactively
php main.php wizard

# Wizard with template
php main.php wizard --template=web-app
```

### CLI Game
```bash
# Play the number guessing game
php main.php game

# Game with difficulty level
php main.php game --difficulty=hard
```

## 📖 Key Features

### 1. Menu Navigation
- **Hierarchical menus**: Nested menu structures
- **Breadcrumb navigation**: Show current location
- **Quick navigation**: Jump to sections
- **Search functionality**: Find menu items
- **History tracking**: Previous selections

### 2. Wizard Workflows
- **Step validation**: Validate each step before proceeding
- **Progress tracking**: Show completion progress
- **Back navigation**: Return to previous steps
- **Save/Resume**: Save progress and resume later
- **Templates**: Pre-configured workflows

### 3. Interactive Elements
- **Dynamic lists**: Lists that update based on user input
- **Real-time validation**: Immediate feedback
- **Conditional flows**: Different paths based on choices
- **Auto-completion**: Suggest completions
- **Keyboard shortcuts**: Quick actions

## 🎨 Expected Output

### Interactive Menu
```
🎛️  Interactive Menu System
========================

📋 Main Menu:
  1. User Management
  2. System Settings
  3. Reports & Analytics
  4. Tools & Utilities
  5. Help & Documentation
  0. Exit

Current: Main Menu
Your choice [1-5, 0 to exit]: 1

👥 User Management:
  1. List Users
  2. Create User
  3. Edit User
  4. Delete User
  5. User Reports
  9. Back to Main Menu

Current: Main Menu > User Management
Your choice [1-5, 9 for back]: 2

✨ Create New User
================
Enter user details...
```

### Project Wizard
```
🧙‍♂️ Project Creation Wizard
==========================

Step 1/5: Project Type
  1. Web Application
  2. API Service
  3. CLI Tool
  4. Library/Package
  5. Mobile App

Your choice: 1

Step 2/5: Framework Selection
  1. Laravel (PHP)
  2. React (JavaScript)
  3. Vue.js (JavaScript)
  4. Django (Python)

Your choice: 1

Step 3/5: Project Configuration
Project name: MyAwesomeApp
Description: A fantastic web application
Author: John Doe

Step 4/5: Features Selection
☑️  Authentication
☑️  Database Integration
☐  API Documentation
☑️  Testing Framework
☐  Docker Support

Step 5/5: Review & Create
📋 Project Summary:
   • Type: Web Application
   • Framework: Laravel
   • Name: MyAwesomeApp
   • Features: 3 selected

Create project? [Y/n]: Y

🎉 Project created successfully!
```

## 💡 Try This

Extend the examples:

1. **Add keyboard shortcuts**: Implement hotkeys for common actions
2. **Create themes**: Different color schemes for menus
3. **Add search**: Search functionality across menus
4. **Implement bookmarks**: Save favorite menu locations
5. **Add help system**: Context-sensitive help

```php
// Example: Add keyboard shortcuts
private function handleKeyboardShortcut(string $input): bool {
    return match(strtolower($input)) {
        'h' => $this->showHelp(),
        'q' => $this->confirmExit(),
        's' => $this->showSettings(),
        default => false
    };
}
```
