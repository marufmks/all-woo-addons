<?php
namespace AllWooAddons\Core;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Dependency Injection Container
 * 
 * Simple DI container for managing service dependencies
 * and ensuring proper object instantiation.
 */
class Container
{
    /**
     * Container instance
     * 
     * @var Container|null
     */
    private static ?Container $instance = null;

    /**
     * Registered services
     * 
     * @var array
     */
    private array $services = [];

    /**
     * Resolved instances
     * 
     * @var array
     */
    private array $instances = [];

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        // Private constructor for singleton pattern
    }

    /**
     * Get container instance
     * 
     * @return Container
     */
    public static function getInstance(): Container
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Register a service
     * 
     * @param string $id Service identifier
     * @param callable|string $resolver Service resolver (closure or class name)
     * @param bool $singleton Whether to treat as singleton
     * @return void
     */
    public function register(string $id, $resolver, bool $singleton = true): void
    {
        $this->services[$id] = [
            'resolver' => $resolver,
            'singleton' => $singleton,
        ];
    }

    /**
     * Register a singleton service
     * 
     * @param string $id Service identifier
     * @param callable|string $resolver Service resolver
     * @return void
     */
    public function singleton(string $id, $resolver): void
    {
        $this->register($id, $resolver, true);
    }

    /**
     * Register a transient service
     * 
     * @param string $id Service identifier
     * @param callable|string $resolver Service resolver
     * @return void
     */
    public function transient(string $id, $resolver): void
    {
        $this->register($id, $resolver, false);
    }

    /**
     * Get a service instance
     * 
     * @param string $id Service identifier
     * @return mixed
     * @throws \Exception If service is not found
     */
    public function get(string $id)
    {
        if (!isset($this->services[$id])) {
            throw new \Exception("Service '{$id}' is not registered in the container.");
        }

        $service = $this->services[$id];

        // Return existing instance if singleton and already resolved
        if ($service['singleton'] && isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // Resolve the service
        $instance = $this->resolve($service['resolver']);

        // Store instance if singleton
        if ($service['singleton']) {
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    /**
     * Check if a service is registered
     * 
     * @param string $id Service identifier
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    /**
     * Resolve a service
     * 
     * @param callable|string $resolver Service resolver
     * @return mixed
     */
    private function resolve($resolver)
    {
        if (is_callable($resolver)) {
            return $resolver($this);
        }

        if (is_string($resolver) && class_exists($resolver)) {
            return new $resolver();
        }

        throw new \Exception('Invalid service resolver provided.');
    }

    /**
     * Clear all instances (useful for testing)
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->instances = [];
    }

    /**
     * Get all registered service IDs
     * 
     * @return array
     */
    public function getServiceIds(): array
    {
        return array_keys($this->services);
    }
}
