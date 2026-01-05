<?php
namespace AllWooAddons\Blocks;

use AllWooAddons\Abstracts\AbstractService;
use AllWooAddons\Blocks\BlockFactory;
use AllWooAddons\Blocks\ProductGridBlock;
use AllWooAddons\Blocks\RecentlyViewedProductsBlock;
use AllWooAddons\Blocks\FilterProductsBlock;
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
        BlockFactory::registerBlockType('all-woo-addons/product-grid', ProductGridBlock::class);

        // Register Recently Viewed Products Block
        BlockFactory::registerBlockType('all-woo-addons/recently-viewed-products', RecentlyViewedProductsBlock::class);

        // Register Filter Products Block
        BlockFactory::registerBlockType('all-woo-addons/filter-products', FilterProductsBlock::class);
    }

    /**
     * Register blocks with WordPress
     * 
     * @return void
     */
    public function registerBlocks(): void
    {
        $this->registerProductGridBlock();
        $this->registerRecentlyViewedProductsBlock();
        $this->registerFilterProductsBlock();
    }

    /**
     * Register Product Grid Block
     * 
     * @return void
     */
    private function registerProductGridBlock(): void
    {
        $blockPath = ALLWOOADDONS_PATH . '/build/blocks/product-grid';

        if (!file_exists($blockPath . '/block.json')) {
            return;
        }

        $block = BlockFactory::create('all-woo-addons/product-grid');
        $block->register();

        $this->registeredBlocks[] = 'all-woo-addons/product-grid';
    }

    private function registerRecentlyViewedProductsBlock(): void
    {
        $blockPath = ALLWOOADDONS_PATH . '/build/blocks/recently-viewed-products';

        if (!file_exists($blockPath . '/block.json')) {
            return;
        }

        $block = BlockFactory::create('all-woo-addons/recently-viewed-products');
        $block->register();

        $this->registeredBlocks[] = 'all-woo-addons/recently-viewed-products';
    }

    private function registerFilterProductsBlock(): void
    {
        $blockPath = ALLWOOADDONS_PATH . '/build/blocks/filter-products';

        if (!file_exists($blockPath . '/block.json')) {
            return;
        }

        $block = BlockFactory::create('all-woo-addons/filter-products');
        $block->register();

        $this->registeredBlocks[] = 'all-woo-addons/filter-products';
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