<?php
namespace UltimateWooAddons\Core;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Plugin
{
    public static function init()
    {
        add_action('plugins_loaded', [self::class, 'register_classes']);
       
    }

    public static function register_classes()
    {
        // Initialize Admin
        \UltimateWooAddons\Admin\Admin::register();
        // Initialize Blocks
        \UltimateWooAddons\Blocks\Blocks::register();
    }
}
