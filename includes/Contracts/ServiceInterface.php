<?php
namespace UltimateWooAddons\Contracts;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Service Interface
 * 
 * All services in the plugin should implement this interface
 * to ensure consistent initialization and registration.
 */
interface ServiceInterface
{
    /**
     * Initialize the service
     * 
     * @return void
     */
    public function init(): void;

    /**
     * Register WordPress hooks and filters
     * 
     * @return void
     */
    public function register(): void;
}
