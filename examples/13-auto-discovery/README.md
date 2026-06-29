# Auto-Discovery with Factory

This example demonstrates how to use `CommandDiscovery::setFactory()` to inject dependencies into auto-discovered commands.

## Problem

By default, auto-discovery instantiates commands using `new $className()`, which requires zero-argument constructors. Commands that need dependencies (database connections, services, configuration) cannot use auto-discovery without a factory.

## Solution

Set a factory callable that knows how to construct each command:

```php
$discovery->setFactory(function (string $className) use ($container) {
    return $container->get($className);
});
```

## Running

```bash
php main.php greet
# Output: Hello from the container!

php main.php status
# Output:
# Application is running.
# Version: 2.1.0
```

## Key Points

- The factory receives the fully qualified class name as its only argument
- It must return a `Command` instance
- If no factory is set, the default `new $className()` behavior is used
- Factory exceptions are caught and handled like any other instantiation error
- Works with any DI container (PSR-11, webfiori/container, or a simple closure)
