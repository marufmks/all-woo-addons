<?php
namespace UltimateWooAddons\Core;

class Activator {
    public static function activate() {
        // Activation logic here
        add_option("ultimate_woo_addons_activated", true );
    }
}
