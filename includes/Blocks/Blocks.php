<?php 
namespace UltimateWooAddons\Blocks;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
class Blocks
{
    public static function register()
    {
        add_action('init', [self::class, 'register_blocks']);
    }

    public static function register_blocks() {
        register_block_type(
            ULTIMATEWOOADDONS_PATH.'/build/blocks/product-grid',
            [
                'render_callback' => [self::class, 'render_product_grid']
            ]
        );

        register_block_type(ULTIMATEWOOADDONS_PATH.'/build/blocks/hello-world');

        
    }

    public static function render_product_grid($attributes) {
        if (!function_exists('wc_get_products')) {
            return '<p>WooCommerce not active.</p>';
        }

        $products = wc_get_products([
            'limit' => 6,
            'status' => 'publish'
        ]);

        ob_start();
        echo '<div class="ultimate-woo-addons-product-grid">';
        foreach ($products as $product) {
            echo '<div class="ultimate-woo-addons-product-card">';
            echo $product->get_image();
            echo '<h3>' . esc_html($product->get_name()) . '</h3>';
            echo wc_price($product->get_price());
            echo '</div>';
        }
        echo '</div>';
        return ob_get_clean();
    }
}