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
define('UWA_VERSION', '1.0.0');
define('UWA_FILE', __FILE__);
define('UWA_PATH', __DIR__);
define('UWA_URL', plugins_url('', UWA_FILE));
define('UWA_ASSETS', UWA_URL . '/assets');
define('UWA_NAME', 'Ultimate Woo Addons');
define('UWA_TEXTDOMAIN', 'ultimate-woo-addons');

// Use the Plugin class from the UWA\Core namespace

use UWA\Core\Plugin;

// Boot
Plugin::init();