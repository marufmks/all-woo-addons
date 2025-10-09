<?php 
namespace UltimateWooAddons\Blocks;

use UltimateWooAddons\Abstracts\AbstractService;
use UltimateWooAddons\Blocks\BlockFactory;
use UltimateWooAddons\Blocks\ProductGridBlock;
use UltimateWooAddons\Blocks\HelloWorldBlock;
use UltimateWooAddons\Blocks\RecentlyViewedProductsBlock;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Blocks Service Class
 * 
 * Manages all block registration and initialization using the Factory pattern.
 */
class Blocks extends AbstractService
{
    /**
     * Registered blocks
     * 
     * @var array
     */
    private array $registeredBlocks = [];

    /**
     * Service-specific initialization logic
     * 
     * @return void
     */
    protected function doInit(): void
    {
        $this->registerBlockTypes();
    }

    /**
     * Service-specific registration logic
     * 
     * @return void
     */
    protected function doRegister(): void
    {
        add_action('init', [$this, 'registerBlocks']);
    }

    /**
     * Register block types with the factory
     * 
     * @return void
     */
    private function registerBlockTypes(): void
    {
        // Register Product Grid Block
        BlockFactory::registerBlockType('ultimate-woo-addons/product-grid', ProductGridBlock::class);
        
        // Register Hello World Block
        BlockFactory::registerBlockType('ultimate-woo-addons/hello-world', HelloWorldBlock::class);

        // Register Recently Viewed Products Block
        BlockFactory::registerBlockType('ultimate-woo-addons/recently-viewed-products', RecentlyViewedProductsBlock::class);
    }

    /**
     * Register blocks with WordPress
     * 
     * @return void
     */
    public function registerBlocks(): void
    {
        $this->registerProductGridBlock();
        $this->registerHelloWorldBlock();
        $this->registerRecentlyViewedProductsBlock();
    }

    /**
     * Register Product Grid Block
     * 
     * @return void
     */
    private function registerProductGridBlock(): void
    {
        $blockPath = ULTIMATEWOOADDONS_PATH . '/build/blocks/product-grid';
        
        if (!file_exists($blockPath . '/block.json')) {
            return;
        }

        $block = BlockFactory::create('ultimate-woo-addons/product-grid');
        $block->register();
        
        $this->registeredBlocks[] = 'ultimate-woo-addons/product-grid';
    }

    /**
     * Register Hello World Block
     * 
     * @return void
     */
    private function registerHelloWorldBlock(): void
    {
        $blockPath = ULTIMATEWOOADDONS_PATH . '/build/blocks/hello-world';
        
        if (!file_exists($blockPath . '/block.json')) {
            return;
        }

        $block = BlockFactory::create('ultimate-woo-addons/hello-world');
        $block->register();
        
        $this->registeredBlocks[] = 'ultimate-woo-addons/hello-world';
    }

    private function registerRecentlyViewedProductsBlock(): void
    {
        $blockPath = ULTIMATEWOOADDONS_PATH . '/build/blocks/recently-viewed-products';

        if (!file_exists($blockPath . '/block.json')) {
            return;
        }

        $block = BlockFactory::create('ultimate-woo-addons/recently-viewed-products');
        $block->register();

        $this->registeredBlocks[] = 'ultimate-woo-addons/recently-viewed-products';
    }

    /**
     * Get registered blocks
     * 
     * @return array
     */
    public function getRegisteredBlocks(): array
    {
        return $this->registeredBlocks;
    }

    /**
     * Check if a block is registered
     * 
     * @param string $blockName Block name
     * @return bool
     */
    public function isBlockRegistered(string $blockName): bool
    {
        return in_array($blockName, $this->registeredBlocks);
    }

    /**
     * Get block factory class name
     * 
     * @return string
     */
    public function getBlockFactoryClass(): string
    {
        return BlockFactory::class;
    }
}