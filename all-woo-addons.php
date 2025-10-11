<?php
/**
 * Plugin Name:     All Woo Addons
 * Plugin URI:      https://github.com/marufmks/all-woo-addons
 * Description:     A WooCommerce plugin that adds custom blocks and admin features.
 * Author:          Maruf
 * Author URI:      https://github.com/marufmks
 * Text Domain:     all-woo-addons
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Ultimate_Woo_Addons
 */

// Your code starts here.

if (!defined('ABSPATH')) exit;

require __DIR__ . '/vendor/autoload.php';

//define constants
define('ALLWOOADDONS_VERSION', '1.0.0');
define('ALLWOOADDONS_FILE', __FILE__);
define('ALLWOOADDONS_PATH', __DIR__);
define('ALLWOOADDONS_URL', plugins_url('', ALLWOOADDONS_FILE));
define('ALLWOOADDONS_ASSETS', ALLWOOADDONS_URL . '/assets');
define('ALLWOOADDONS_NAME', 'All Woo Addons');
define('ALLWOOADDONS_TEXTDOMAIN', 'all-woo-addons');

// Use the Plugin class from the AllWooAddons\Core namespace

use AllWooAddons\Core\Plugin;

// Boot
Plugin::init();

// Activation and Deactivation hooks
use AllWooAddons\Core\Activator;
use AllWooAddons\Core\Deactivator;

register_activation_hook(__FILE__, [Activator::class, 'activateStatic']);
register_deactivation_hook(__FILE__, [Deactivator::class, 'deactivateStatic']);