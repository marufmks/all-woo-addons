<?php
namespace UltimateWooAddons\Core;

use UltimateWooAddons\Contracts\ActivatorInterface;
use UltimateWooAddons\Core\EventManager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Plugin Activator Class
 * 
 * Handles plugin activation logic and notifies observers.
 */
class Activator implements ActivatorInterface
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
        // Set activation flag
        add_option('ultimate_woo_addons_activated', true);
        add_option('ultimate_woo_addons_activation_time', current_time('timestamp'));
        add_option('ultimate_woo_addons_version', ULTIMATEWOOADDONS_VERSION);

        // Create database tables if needed
        $this->createDatabaseTables();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Notify observers
        $this->eventManager->notify('plugin.activated', [
            'version' => ULTIMATEWOOADDONS_VERSION,
            'timestamp' => current_time('timestamp'),
        ]);
    }

    /**
     * Deactivate the plugin
     * 
     * @return void
     */
    public function deactivate(): void
    {
        // Remove activation flag
        delete_option('ultimate_woo_addons_activated');

        // Flush rewrite rules
        flush_rewrite_rules();

        // Notify observers
        $this->eventManager->notify('plugin.deactivated', [
            'timestamp' => current_time('timestamp'),
        ]);
    }

    /**
     * Create database tables
     * 
     * @return void
     */
    private function createDatabaseTables(): void
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Example table creation (uncomment if needed)
        /*
        $table_name = $wpdb->prefix . 'ultimate_woo_addons_logs';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            event varchar(100) NOT NULL,
            data longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        */
    }

    /**
     * Static activation method for WordPress hooks
     * 
     * @return void
     */
    public static function activateStatic(): void
    {
        $eventManager = EventManager::getInstance();
        $activator = new self($eventManager);
        $activator->activate();
    }

    /**
     * Static deactivation method for WordPress hooks
     * 
     * @return void
     */
    public static function deactivateStatic(): void
    {
        $eventManager = EventManager::getInstance();
        $activator = new self($eventManager);
        $activator->deactivate();
    }
}
