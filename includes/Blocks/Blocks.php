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

    public static function register_blocks()
    {
        register_block_type(ULTIMATEWOOADDONS_PATH.'/build/blocks/hello-world');
    }
}