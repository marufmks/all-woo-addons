<?php
namespace UltimateWooAddons\Core;

class Deactivator {
    public static function deactivate() {
        // Deactivation logic here
        delete_option("ultimate_woo_addons_activated");
    }
}
