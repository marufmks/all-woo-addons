<?php
namespace AllWooAddons\Core;

use AllWooAddons\Contracts\ObserverInterface;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Event Manager Class
 * 
 * Implements Observer pattern for managing WordPress hooks and events.
 * Provides a centralized way to handle plugin events and notifications.
 */
class EventManager
{
    /**
     * Event Manager instance
     * 
     * @var EventManager|null
     */
    private static ?EventManager $instance = null;

    /**
     * Registered observers
     * 
     * @var array
     */
    private array $observers = [];

    /**
     * Event listeners
     * 
     * @var array
     */
    private array $listeners = [];

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        // Private constructor for singleton pattern
    }

    /**
     * Get Event Manager instance
     * 
     * @return EventManager
     */
    public static function getInstance(): EventManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Subscribe an observer to events
     * 
     * @param ObserverInterface $observer Observer instance
     * @return void
     */
    public function subscribe(ObserverInterface $observer): void
    {
        $this->observers[] = $observer;
        
        // Register for specific events
        foreach ($observer->getSubscribedEvents() as $event) {
            if (!isset($this->listeners[$event])) {
                $this->listeners[$event] = [];
            }
            $this->listeners[$event][] = $observer;
        }
    }

    /**
     * Unsubscribe an observer from events
     * 
     * @param ObserverInterface $observer Observer instance
     * @return void
     */
    public function unsubscribe(ObserverInterface $observer): void
    {
        // Remove from observers array
        $this->observers = array_filter($this->observers, function($obs) use ($observer) {
            return $obs !== $observer;
        });

        // Remove from event listeners
        foreach ($this->listeners as $event => $listeners) {
            $this->listeners[$event] = array_filter($listeners, function($listener) use ($observer) {
                return $listener !== $observer;
            });
        }
    }

    /**
     * Notify observers of an event
     * 
     * @param string $event Event name
     * @param mixed $data Event data
     * @return void
     */
    public function notify(string $event, $data = null): void
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $observer) {
            $observer->handle($event, $data);
        }
    }

    /**
     * Add a WordPress action hook
     * 
     * @param string $hook WordPress hook name
     * @param callable $callback Callback function
     * @param int $priority Hook priority
     * @param int $acceptedArgs Number of accepted arguments
     * @return void
     */
    public function addAction(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        add_action($hook, $callback, $priority, $acceptedArgs);
    }

    /**
     * Add a WordPress filter hook
     * 
     * @param string $hook WordPress hook name
     * @param callable $callback Callback function
     * @param int $priority Hook priority
     * @param int $acceptedArgs Number of accepted arguments
     * @return void
     */
    public function addFilter(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        add_filter($hook, $callback, $priority, $acceptedArgs);
    }

    /**
     * Remove a WordPress action hook
     * 
     * @param string $hook WordPress hook name
     * @param callable $callback Callback function
     * @param int $priority Hook priority
     * @return bool
     */
    public function removeAction(string $hook, callable $callback, int $priority = 10): bool
    {
        return remove_action($hook, $callback, $priority);
    }

    /**
     * Remove a WordPress filter hook
     * 
     * @param string $hook WordPress hook name
     * @param callable $callback Callback function
     * @param int $priority Hook priority
     * @return bool
     */
    public function removeFilter(string $hook, callable $callback, int $priority = 10): bool
    {
        return remove_filter($hook, $callback, $priority);
    }

    /**
     * Apply WordPress filters
     * 
     * @param string $hook WordPress hook name
     * @param mixed $value Value to filter
     * @param mixed ...$args Additional arguments
     * @return mixed
     */
    public function applyFilters(string $hook, $value, ...$args)
    {
        return apply_filters($hook, $value, ...$args);
    }

    /**
     * Do WordPress action
     * 
     * @param string $hook WordPress hook name
     * @param mixed ...$args Arguments to pass
     * @return void
     */
    public function doAction(string $hook, ...$args): void
    {
        do_action($hook, ...$args);
    }

    /**
     * Get all registered observers
     * 
     * @return array
     */
    public function getObservers(): array
    {
        return $this->observers;
    }

    /**
     * Get listeners for a specific event
     * 
     * @param string $event Event name
     * @return array
     */
    public function getEventListeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }

    /**
     * Get all registered events
     * 
     * @return array
     */
    public function getRegisteredEvents(): array
    {
        return array_keys($this->listeners);
    }

    /**
     * Clear all observers and listeners
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->observers = [];
        $this->listeners = [];
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
        // Prevent cloning
    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
