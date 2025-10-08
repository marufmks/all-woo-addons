<?php
namespace UltimateWooAddons\Core;

use UltimateWooAddons\Contracts\ActivatorInterface;
use UltimateWooAddons\Core\EventManager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Plugin Deactivator Class
 * 
 * Handles plugin deactivation logic and cleanup.
 * This class is kept separate from Activator for better separation of concerns.
 */
class Deactivator implements ActivatorInterface
{
    /**
     * Event Manager instance
     * 
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * Constructor
     * 
     * @param EventManager $eventManager Event Manager instance
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * Activate the plugin
     * 
     * @return void
     */
    public function activate(): void
    {
        // This method is not used in Deactivator
        // It's here to satisfy the ActivatorInterface
    }

    /**
     * Deactivate the plugin
     * 
     * @return void
     */
    public function deactivate(): void
    {
        // Clean up plugin data
        $this->cleanupPluginData();

        // Clear scheduled events
        $this->clearScheduledEvents();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Notify observers
        $this->eventManager->notify('plugin.deactivated', [
            'timestamp' => current_time('timestamp'),
        ]);
    }

    /**
     * Clean up plugin data
     * 
     * @return void
     */
    private function cleanupPluginData(): void
    {
        // Remove plugin options
        delete_option('ultimate_woo_addons_activated');
        delete_option('ultimate_woo_addons_activation_time');
        delete_option('ultimate_woo_addons_registered_blocks');
        delete_option('ultimate_woo_addons_admin_pages_loaded');

        // Keep version for potential future use
        // delete_option('ultimate_woo_addons_version');
    }

    /**
     * Clear scheduled events
     * 
     * @return void
     */
    private function clearScheduledEvents(): void
    {
        // Clear any scheduled WordPress cron jobs
        wp_clear_scheduled_hook('ultimate_woo_addons_daily_cleanup');
        wp_clear_scheduled_hook('ultimate_woo_addons_weekly_report');
    }

    /**
     * Static deactivation method for WordPress hooks
     * 
     * @return void
     */
    public static function deactivateStatic(): void
    {
        $eventManager = EventManager::getInstance();
        $deactivator = new self($eventManager);
        $deactivator->deactivate();
    }
}
