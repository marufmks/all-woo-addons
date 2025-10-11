# All Woo Addons - OOP Architecture Guide

This document explains the Object-Oriented Programming (OOP) architecture implemented in the All Woo Addons plugin.

## Overview

The plugin has been refactored from a simple static method approach to a comprehensive OOP architecture using modern design patterns and principles.

## Design Patterns Implemented

### 1. Singleton Pattern
**Location**: `includes/Core/Plugin.php`, `includes/Core/Container.php`, `includes/Core/EventManager.php`

The Singleton pattern ensures only one instance of critical classes exists:
- **Plugin**: Main plugin class
- **Container**: Dependency injection container
- **EventManager**: Event management system

```php
// Get plugin instance
$plugin = Plugin::getInstance();

// Get container instance
$container = Container::getInstance();

// Get event manager instance
$eventManager = EventManager::getInstance();
```

### 2. Factory Pattern
**Location**: `includes/Blocks/BlockFactory.php`

The Factory pattern centralizes block creation logic:

```php
// Register a new block type
BlockFactory::registerBlockType('my-block', MyBlockClass::class);

// Create a block instance
$block = BlockFactory::create('my-block', $config, $dependencies);

// Check if block is registered
if (BlockFactory::isRegistered('my-block')) {
    // Block is available
}
```

### 3. Observer Pattern
**Location**: `includes/Core/EventManager.php`, `includes/Observers/PluginObserver.php`

The Observer pattern manages event notifications:

```php
// Subscribe an observer to events
$eventManager->subscribe($observer);

// Notify observers of an event
$eventManager->notify('plugin.activated', $data);

// Unsubscribe an observer
$eventManager->unsubscribe($observer);
```

### 4. Dependency Injection
**Location**: `includes/Core/Container.php`

The DI container manages service dependencies:

```php
// Register a service
$container->singleton('myService', function() {
    return new MyService();
});

// Get a service
$service = $container->get('myService');

// Check if service exists
if ($container->has('myService')) {
    // Service is available
}
```

## Architecture Components

### Interfaces
**Location**: `includes/Contracts/`

Interfaces define contracts for consistent behavior:

- **ServiceInterface**: All services must implement this
- **BlockInterface**: All blocks must implement this
- **ActivatorInterface**: Activation/deactivation handlers
- **ObserverInterface**: Event observers

### Abstract Classes
**Location**: `includes/Abstracts/`

Abstract classes provide common functionality:

- **AbstractService**: Base class for all services
- **AbstractBlock**: Base class for all blocks

### Services
**Location**: `includes/Admin/`, `includes/Blocks/`

Services handle specific plugin functionality:

- **Admin**: Admin interface management
- **Blocks**: Block registration and management

### Core Classes
**Location**: `includes/Core/`

Core classes provide fundamental functionality:

- **Plugin**: Main plugin class (Singleton)
- **Container**: Dependency injection (Singleton)
- **EventManager**: Event management (Singleton)
- **Activator**: Plugin activation
- **Deactivator**: Plugin deactivation

## Usage Examples

### Creating a New Service

```php
<?php
namespace AllWooAddons\Services;

use AllWooAddons\Abstracts\AbstractService;

class MyService extends AbstractService
{
    protected function doInit(): void
    {
        // Service initialization logic
    }

    protected function doRegister(): void
    {
        add_action('init', [$this, 'myMethod']);
    }

    public function myMethod(): void
    {
        // Service method implementation
    }
}
```

### Creating a New Block

```php
<?php
namespace AllWooAddons\Blocks;

use AllWooAddons\Abstracts\AbstractBlock;

class MyBlock extends AbstractBlock
{
    public function render(array $attributes, string $content = ''): string
    {
        // Block rendering logic
        return '<div>My Block Content</div>';
    }

    protected function sanitizeAttributes(array $attributes): array
    {
        // Custom attribute sanitization
        return $attributes;
    }
}
```

### Creating a New Observer

```php
<?php
namespace AllWooAddons\Observers;

use AllWooAddons\Contracts\ObserverInterface;

class MyObserver implements ObserverInterface
{
    public function handle(string $event, $data = null): void
    {
        switch ($event) {
            case 'my.event':
                $this->handleMyEvent($data);
                break;
        }
    }

    public function getSubscribedEvents(): array
    {
        return ['my.event'];
    }

    private function handleMyEvent($data): void
    {
        // Event handling logic
    }
}
```

### Registering Services in Plugin

```php
// In Plugin::registerServices()
$this->container->singleton('myService', function() {
    return new \AllWooAddons\Services\MyService();
});
$this->services[] = 'myService';
```

## Benefits of This Architecture

### 1. **Separation of Concerns**
Each class has a single responsibility, making the code easier to understand and maintain.

### 2. **Dependency Injection**
Services are injected rather than hard-coded, making testing and modification easier.

### 3. **Event-Driven Architecture**
The Observer pattern allows for loose coupling between components.

### 4. **Extensibility**
New services, blocks, and observers can be easily added without modifying existing code.

### 5. **Testability**
The architecture supports unit testing with proper mocking and dependency injection.

### 6. **Maintainability**
Clear interfaces and abstract classes make the codebase easier to maintain and extend.

## Best Practices

### 1. **Always Use Interfaces**
Define interfaces for all major components to ensure consistent behavior.

### 2. **Extend Abstract Classes**
Use abstract classes for common functionality rather than duplicating code.

### 3. **Use Dependency Injection**
Inject dependencies through constructors rather than using global functions.

### 4. **Follow Single Responsibility Principle**
Each class should have only one reason to change.

### 5. **Use Proper Encapsulation**
Keep properties private and provide public methods for access.

### 6. **Implement Proper Error Handling**
Use try-catch blocks and proper exception handling.

## Migration from Static Methods

The plugin has been migrated from static methods to instance methods:

**Before (Static)**:
```php
class Admin
{
    public static function register()
    {
        add_action('admin_menu', [self::class, 'register_admin_menu']);
    }
}
```

**After (OOP)**:
```php
class Admin extends AbstractService
{
    protected function doRegister(): void
    {
        add_action('admin_menu', [$this, 'registerAdminMenu']);
    }
}
```

## Testing

The architecture supports comprehensive testing:

```php
// Test service registration
$container = Container::getInstance();
$service = $container->get('admin');
$this->assertInstanceOf(Admin::class, $service);

// Test event notifications
$eventManager = EventManager::getInstance();
$observer = new TestObserver();
$eventManager->subscribe($observer);
$eventManager->notify('test.event', $data);
$this->assertTrue($observer->wasNotified());
```

## Conclusion

This OOP architecture provides a solid foundation for the All Woo Addons plugin, making it more maintainable, testable, and extensible. The use of design patterns ensures consistent behavior and makes the codebase easier to understand and modify.
