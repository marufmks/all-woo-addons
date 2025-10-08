<?php
/**
 * Plugin Name:     Ultimate Woo Addons
 * Plugin URI:      https://github.com/marufmks/ultimate-woo-addons
 * Description:     A WooCommerce plugin that adds custom blocks and admin features.
 * Author:          Maruf
 * Author URI:      https://github.com/marufmks
 * Text Domain:     ultimate-woo-addons
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Ultimate_Woo_Addons
 */

// Your code starts here.

if (!defined('ABSPATH')) exit;

require __DIR__ . '/vendor/autoload.php';

//define constants
define('ULTIMATEWOOADDONS_VERSION', '1.0.0');
define('ULTIMATEWOOADDONS_FILE', __FILE__);
define('ULTIMATEWOOADDONS_PATH', __DIR__);
define('ULTIMATEWOOADDONS_URL', plugins_url('', ULTIMATEWOOADDONS_FILE));
define('ULTIMATEWOOADDONS_ASSETS', ULTIMATEWOOADDONS_URL . '/assets');
define('ULTIMATEWOOADDONS_NAME', 'Ultimate Woo Addons');
define('ULTIMATEWOOADDONS_TEXTDOMAIN', 'ultimate-woo-addons');

// Use the Plugin class from the UltimateWooAddons\Core namespace

use UltimateWooAddons\Core\Plugin;

// Boot
Plugin::init();

// Activation and Deactivation hooks
use UltimateWooAddons\Core\Activator;
use UltimateWooAddons\Core\Deactivator;

register_activation_hook(__FILE__, [Activator::class, 'activateStatic']);
register_deactivation_hook(__FILE__, [Deactivator::class, 'deactivateStatic']);