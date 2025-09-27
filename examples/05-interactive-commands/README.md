# Interactive Commands Example

This example demonstrates building complex interactive CLI workflows with multi-level menu navigation, breadcrumb tracking, and robust error handling.

## 🎯 What You'll Learn

- Creating hierarchical menu systems with navigation
- Building multi-level interactive interfaces
- State management and breadcrumb tracking
- Error handling with retry limits
- User experience best practices
- Navigation commands and keyboard shortcuts

## 📁 Files

- `InteractiveMenuCommand.php` - Complete multi-level menu system with navigation
- `main.php` - Application entry point
- `README.md` - This documentation



## 🚀 Running the Example

### Basic Usage
```bash
# Start the interactive menu system
php main.php menu

# Start in a specific section
php main.php menu --section=users      # User Management
php main.php menu --section=settings   # System Settings  
php main.php menu --section=reports    # Reports & Analytics
php main.php menu --section=tools      # Tools & Utilities
```

### Navigation Commands
- **Numbers (1-9)**: Select menu options
- **`back` or `b`**: Go to previous menu
- **`home` or `h`**: Go to main menu  
- **`exit` or `q`**: Quit application
- **`0`**: Exit from main menu

## 📖 Key Features

### 1. Multi-Level Navigation
- **Hierarchical menus**: 3+ levels deep (Main → Settings → System Config)
- **Breadcrumb tracking**: Shows current location path
- **Menu stack management**: Maintains navigation history
- **Quick section access**: Jump directly to sections via arguments

### 2. Robust Error Handling
- **Failed attempts counter**: Max 5 invalid inputs before exit
- **Graceful degradation**: Clear error messages with attempt count
- **Counter reset**: Resets on valid input to allow recovery
- **Infinite loop prevention**: Automatic exit after too many failures

### 3. User Experience
- **ANSI colors and icons**: Rich visual interface
- **Clear navigation hints**: Instructions shown on startup
- **Consistent layout**: Standardized menu formatting
- **Responsive feedback**: Immediate validation and error messages

### 4. State Management
- **Menu stack**: Tracks navigation path for back/home functionality
- **Breadcrumbs**: Visual indication of current location
- **Session persistence**: Maintains state throughout navigation
- **Context awareness**: Different options based on current menu

## 🎨 Expected Output

### Startup Screen
```
🎛️  Interactive Menu System
========================

💡 Navigation Tips:
   • Enter number to select option
   • Type 'back' or 'b' to go back
   • Type 'home' or 'h' to go to main menu
   • Type 'exit' or 'q' to quit

Press Enter to continue...
```

### Main Menu
```
📍 Current: Main Menu

📋 Main Menu:

  1. 👥 User Management
  2. ⚙️  System Settings
  3. 📊 Reports & Analytics
  4. 🔧 Tools & Utilities
  5. ❓ Help & Documentation

  0. 🚪 Exit

Your choice: 2
```

### Sub-Menu Navigation
```
📍 Current: Main Menu > System Settings

⚙️  System Settings:

  1. 🖥️  System Configuration
  2. 🎨 Appearance Settings
  3. 🔐 Security Settings
  4. 📧 Email Configuration
  5. 🗄️  Database Settings
  6. 📝 Logging Configuration

  9. ⬅️  Back to Main Menu

Your choice: 1
```

### Deep Navigation
```
📍 Current: Main Menu > System Settings > System Configuration

🖥️  System Configuration
======================

Current Settings:
   • Application Name: MyApp
   • Version: 1.0.0
   • Environment: Development
   • Debug Mode: Enabled
   • Timezone: UTC

  1. Change Application Name
  2. Update Environment
  3. Toggle Debug Mode
  4. Set Timezone
  5. Reset to Defaults

  9. ⬅️  Back to Settings

Your choice: back
```

### Error Handling
```
Your choice: 99
Error: Invalid choice. Please try again. (1/5)
Press Enter to continue...

Your choice: abc
Error: Invalid choice. Please enter a number or command. (2/5)
Press Enter to continue...

Your choice: 999
Error: Invalid choice. Please try again. (3/5)
Press Enter to continue...

Your choice: invalid
Error: Invalid choice. Please enter a number or command. (4/5)
Press Enter to continue...

Your choice: wrong
Error: Invalid choice. Please enter a number or command. (5/5)
Error: Too many invalid attempts. Exiting...

👋 Thank you for using the Interactive Menu System!
Have a great day!
```

### Navigation Commands
```
Your choice: back
# Goes to previous menu

Your choice: home  
# Goes to main menu

Your choice: q
# Exits application

Your choice: exit
# Also exits application
```

## 🧪 Test Scenarios

### 1. Basic Navigation
```bash
echo -e "\n1\n2\n9\n0" | php main.php menu
# Navigate: Main → Users → Create User → Back → Exit
```

### 2. Deep Navigation
```bash
echo -e "\n2\n1\nback\nhome\nq" | php main.php menu  
# Navigate: Main → Settings → Config → Back → Home → Quit
```

### 3. Error Handling
```bash
echo -e "\n2\n99\n99\n99\n99\n99" | php main.php menu
# Test: Settings → 5 invalid inputs → Auto-exit
```

### 4. Section Arguments
```bash
php main.php menu --section=settings
# Start directly in System Settings
```

### 5. Keyboard Shortcuts
```bash
echo -e "\n2\nb\nh\nexit" | php main.php menu
# Test: Settings → back → home → exit
```

## ⚠️ Known Issues

1. **PHP Warning**: Minor undefined array key warning in user creation form (line 259)
2. **Input Handling**: Some forms may not handle all edge cases perfectly
3. **Display**: ANSI colors may not work in all terminal environments

## 🔧 Technical Implementation

### Core Classes
- `InteractiveMenuCommand`: Main command class with navigation logic
- Menu stack management with `$menuStack` and `$breadcrumbs` arrays
- Failed attempts tracking with `$failedTries` counter (max 5)
- State management for multi-level navigation

### Key Methods
- `handleMenuChoice()`: Processes user input and navigation
- `navigateTo()`: Manages menu transitions and breadcrumbs  
- `goBack()` / `goHome()`: Navigation utilities
- `invalidChoice()`: Error handling with retry counter
- `displayCurrentMenu()`: Renders current menu state

### Error Prevention
- Input validation with retry limits
- Graceful exit after 5 failed attempts
- Counter reset on valid input for recovery
- Clear error messages with attempt tracking

## 💡 Learning Opportunities

### Extend the Example

1. **Add Search Functionality**
```php
private function searchMenus(string $query): array {
    // Search across all menu items
    return $this->findMatchingItems($query);
}
```

2. **Implement Bookmarks**
```php
private function bookmarkCurrentLocation(): void {
    $this->bookmarks[] = [
        'path' => $this->breadcrumbs,
        'menu' => end($this->menuStack)
    ];
}
```

3. **Add Themes Support**
```php
private function setTheme(string $theme): void {
    $this->colors = match($theme) {
        'dark' => ['bg' => 'black', 'text' => 'white'],
        'light' => ['bg' => 'white', 'text' => 'black'],
        default => $this->defaultColors
    };
}
```

4. **Implement Menu History**
```php
private function showHistory(): void {
    foreach ($this->navigationHistory as $item) {
        $this->println("• {$item['timestamp']}: {$item['path']}");
    }
}
```

5. **Add Context-Sensitive Help**
```php
private function showContextHelp(): void {
    $currentMenu = end($this->menuStack);
    $help = $this->getHelpForMenu($currentMenu);
    $this->displayHelp($help);
}
```

### Best Practices Demonstrated

1. **State Management**: Proper tracking of navigation state and user context
2. **Error Recovery**: Graceful handling of invalid input with retry limits  
3. **User Experience**: Clear feedback, consistent interface, helpful navigation
4. **Code Organization**: Separation of concerns, modular menu handlers
5. **Extensibility**: Easy to add new menus and navigation features

### Integration Ideas

- **Database Integration**: Store user preferences and navigation history
- **Configuration System**: Customizable menu layouts and themes
- **Plugin Architecture**: Dynamically loaded menu modules
- **API Integration**: Menus that interact with external services
- **Logging System**: Track user interactions and menu usage analytics

## 🔗 Related Examples

- **[01-basic-hello-world](../01-basic-hello-world/)**: Simple command structure
- **[02-arguments-and-options](../02-arguments-and-options/)**: Command arguments
- **[03-user-input](../03-user-input/)**: Input validation and handling
- **[04-output-formatting](../04-output-formatting/)**: ANSI colors and formatting

