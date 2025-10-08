<?php
namespace UltimateWooAddons\Contracts;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Observer Interface
 * 
 * All observers should implement this interface to ensure
 * consistent event handling and notification.
 */
interface ObserverInterface
{
    /**
     * Handle an event notification
     * 
     * @param string $event Event name
     * @param mixed $data Event data
     * @return void
     */
    public function handle(string $event, $data = null): void;

    /**
     * Get the events this observer is interested in
     * 
     * @return array Array of event names
     */
    public function getSubscribedEvents(): array;
}
