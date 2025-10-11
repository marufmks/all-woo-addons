<?php
namespace AllWooAddons\Core;

use AllWooAddons\Contracts\ServiceInterface;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Main Plugin Class
 * 
 * Implements Singleton pattern to ensure only one instance exists.
 * Manages the plugin lifecycle and service registration.
 */
class Plugin
{
    /**
     * Plugin instance
     * 
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    /**
     * Container instance
     * 
     * @var Container
     */
    private Container $container;

    /**
     * Registered services
     * 
     * @var array
     */
    private array $services = [];

    /**
     * Whether the plugin is initialized
     * 
     * @var bool
     */
    private bool $initialized = false;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        $this->container = Container::getInstance();
        $this->registerServices();
    }

    /**
     * Get plugin instance
     * 
     * @return Plugin
     */
    public static function getInstance(): Plugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initialize the plugin
     * 
     * @return void
     */
    public static function init(): void
    {
        $plugin = self::getInstance();
        $plugin->bootstrap();
    }

    /**
     * Bootstrap the plugin
     * 
     * @return void
     */
    private function bootstrap(): void
    {
        if ($this->initialized) {
            return;
        }

        add_action('plugins_loaded', [$this, 'loadServices']);
        $this->initialized = true;
    }

    /**
     * Load and initialize all services
     * 
     * @return void
     */
    public function loadServices(): void
    {
        // Initialize Event Manager and Observer first
        $eventManager = $this->container->get('eventManager');
        $pluginObserver = $this->container->get('pluginObserver');
        $eventManager->subscribe($pluginObserver);

        // Load other services
        foreach ($this->services as $serviceId) {
            $service = $this->container->get($serviceId);
            
            if ($service instanceof ServiceInterface) {
                $service->init();
                $service->register();
            }
        }
    }

    /**
     * Register services in the container
     * 
     * @return void
     */
    private function registerServices(): void
    {
        // Register Event Manager
        $this->container->singleton('eventManager', function() {
            return \AllWooAddons\Core\EventManager::getInstance();
        });

        // Register Plugin Observer
        $this->container->singleton('pluginObserver', function() {
            $eventManager = $this->container->get('eventManager');
            return new \AllWooAddons\Observers\PluginObserver($eventManager);
        });

        // Register Admin service
        $this->container->singleton('admin', function() {
            return new \AllWooAddons\Admin\Admin();
        });
        $this->services[] = 'admin';

        // Register Blocks service
        $this->container->singleton('blocks', function() {
            return new \AllWooAddons\Blocks\Blocks();
        });
        $this->services[] = 'blocks';

        // Register Recently Viewed service
        $this->container->singleton('recentlyViewed', function() {
            return new \AllWooAddons\Services\RecentlyViewedService();
        });
        $this->services[] = 'recentlyViewed';
    }

    /**
     * Get a service from the container
     * 
     * @param string $serviceId Service identifier
     * @return mixed
     */
    public function getService(string $serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * Check if a service exists
     * 
     * @param string $serviceId Service identifier
     * @return bool
     */
    public function hasService(string $serviceId): bool
    {
        return $this->container->has($serviceId);
    }

    /**
     * Get the container instance
     * 
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
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
