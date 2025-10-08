<?php
namespace UltimateWooAddons\Contracts;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Activator Interface
 * 
 * All activation/deactivation handlers should implement this interface
 * to ensure consistent plugin lifecycle management.
 */
interface ActivatorInterface
{
    /**
     * Activate the plugin
     * 
     * @return void
     */
    public function activate(): void;

    /**
     * Deactivate the plugin
     * 
     * @return void
     */
    public function deactivate(): void;
}
