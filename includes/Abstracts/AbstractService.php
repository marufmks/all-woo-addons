<?php
namespace UltimateWooAddons\Abstracts;

use UltimateWooAddons\Contracts\ServiceInterface;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Abstract Service Class
 * 
 * Base class for all services in the plugin.
 * Provides common functionality and enforces the ServiceInterface.
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * Service dependencies
     * 
     * @var array
     */
    protected array $dependencies = [];

    /**
     * Whether the service is initialized
     * 
     * @var bool
     */
    protected bool $initialized = false;

    /**
     * Whether the service is registered
     * 
     * @var bool
     */
    protected bool $registered = false;

    /**
     * Constructor
     * 
     * @param array $dependencies Service dependencies
     */
    public function __construct(array $dependencies = [])
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Initialize the service
     * 
     * @return void
     */
    public function init(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->doInit();
        $this->initialized = true;
    }

    /**
     * Register WordPress hooks and filters
     * 
     * @return void
     */
    public function register(): void
    {
        if ($this->registered) {
            return;
        }

        $this->doRegister();
        $this->registered = true;
    }

    /**
     * Get a dependency by key
     * 
     * @param string $key Dependency key
     * @return mixed|null
     */
    protected function getDependency(string $key)
    {
        return $this->dependencies[$key] ?? null;
    }

    /**
     * Check if a dependency exists
     * 
     * @param string $key Dependency key
     * @return bool
     */
    protected function hasDependency(string $key): bool
    {
        return isset($this->dependencies[$key]);
    }

    /**
     * Service-specific initialization logic
     * Override this method in child classes
     * 
     * @return void
     */
    protected function doInit(): void
    {
        // Override in child classes
    }

    /**
     * Service-specific registration logic
     * Override this method in child classes
     * 
     * @return void
     */
    protected function doRegister(): void
    {
        // Override in child classes
    }

    /**
     * Check if service is initialized
     * 
     * @return bool
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * Check if service is registered
     * 
     * @return bool
     */
    public function isRegistered(): bool
    {
        return $this->registered;
    }
}
