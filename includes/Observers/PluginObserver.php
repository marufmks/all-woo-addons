<?php
namespace UltimateWooAddons\Observers;

use UltimateWooAddons\Contracts\ObserverInterface;
use UltimateWooAddons\Core\EventManager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Plugin Observer Class
 * 
 * Observes plugin events and handles notifications.
 * Example implementation of the Observer pattern.
 */
class PluginObserver implements ObserverInterface
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
     * Handle an event notification
     * 
     * @param string $event Event name
     * @param mixed $data Event data
     * @return void
     */
    public function handle(string $event, $data = null): void
    {
        switch ($event) {
            case 'plugin.activated':
                $this->handlePluginActivated($data);
                break;
            case 'plugin.deactivated':
                $this->handlePluginDeactivated($data);
                break;
            case 'block.registered':
                $this->handleBlockRegistered($data);
                break;
            case 'admin.page.loaded':
                $this->handleAdminPageLoaded($data);
                break;
            default:
                // Handle unknown events
                $this->handleUnknownEvent($event, $data);
        }
    }

    /**
     * Get the events this observer is interested in
     * 
     * @return array Array of event names
     */
    public function getSubscribedEvents(): array
    {
        return [
            'plugin.activated',
            'plugin.deactivated',
            'block.registered',
            'admin.page.loaded',
        ];
    }

    /**
     * Handle plugin activation event
     * 
     * @param mixed $data Event data
     * @return void
     */
    private function handlePluginActivated($data): void
    {
        // Log activation
        error_log('Ultimate Woo Addons: Plugin activated');
        
        // Set activation flag
        update_option('ultimate_woo_addons_activated', true);
        update_option('ultimate_woo_addons_activation_time', current_time('timestamp'));
        
        // Trigger WordPress action for other plugins to hook into
        $this->eventManager->doAction('ultimate_woo_addons_activated', $data);
    }

    /**
     * Handle plugin deactivation event
     * 
     * @param mixed $data Event data
     * @return void
     */
    private function handlePluginDeactivated($data): void
    {
        // Log deactivation
        error_log('Ultimate Woo Addons: Plugin deactivated');
        
        // Remove activation flag
        delete_option('ultimate_woo_addons_activated');
        
        // Trigger WordPress action for other plugins to hook into
        $this->eventManager->doAction('ultimate_woo_addons_deactivated', $data);
    }

    /**
     * Handle block registration event
     * 
     * @param mixed $data Event data
     * @return void
     */
    private function handleBlockRegistered($data): void
    {
        if (is_array($data) && isset($data['block_name'])) {
            error_log("Ultimate Woo Addons: Block '{$data['block_name']}' registered");
            
            // Update registered blocks option
            $registeredBlocks = get_option('ultimate_woo_addons_registered_blocks', []);
            $registeredBlocks[] = $data['block_name'];
            update_option('ultimate_woo_addons_registered_blocks', array_unique($registeredBlocks));
        }
    }

    /**
     * Handle admin page loaded event
     * 
     * @param mixed $data Event data
     * @return void
     */
    private function handleAdminPageLoaded($data): void
    {
        if (is_array($data) && isset($data['page'])) {
            error_log("Ultimate Woo Addons: Admin page '{$data['page']}' loaded");
            
            // Track admin page usage
            $adminPages = get_option('ultimate_woo_addons_admin_pages_loaded', []);
            $adminPages[] = [
                'page' => $data['page'],
                'timestamp' => current_time('timestamp'),
                'user_id' => get_current_user_id(),
            ];
            
            // Keep only last 100 entries
            if (count($adminPages) > 100) {
                $adminPages = array_slice($adminPages, -100);
            }
            
            update_option('ultimate_woo_addons_admin_pages_loaded', $adminPages);
        }
    }

    /**
     * Handle unknown events
     * 
     * @param string $event Event name
     * @param mixed $data Event data
     * @return void
     */
    private function handleUnknownEvent(string $event, $data): void
    {
        error_log("Ultimate Woo Addons: Unknown event '{$event}' received");
    }
}
