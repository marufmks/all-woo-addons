<?php
namespace UltimateWooAddons\Abstracts;

use UltimateWooAddons\Contracts\BlockInterface;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Abstract Block Class
 * 
 * Base class for all blocks in the plugin.
 * Provides common functionality and enforces the BlockInterface.
 */
abstract class AbstractBlock implements BlockInterface
{
    /**
     * Block name
     * 
     * @var string
     */
    protected string $blockName;

    /**
     * Block configuration
     * 
     * @var array
     */
    protected array $blockConfig;

    /**
     * Block dependencies
     * 
     * @var array
     */
    protected array $dependencies;

    /**
     * Constructor
     * 
     * @param string $blockName Block name
     * @param array $blockConfig Block configuration
     * @param array $dependencies Block dependencies
     */
    public function __construct(string $blockName, array $blockConfig = [], array $dependencies = [])
    {
        $this->blockName = $blockName;
        $this->blockConfig = $blockConfig;
        $this->dependencies = $dependencies;
    }

    /**
     * Get the block name
     * 
     * @return string
     */
    public function getBlockName(): string
    {
        return $this->blockName;
    }

    /**
     * Get the block configuration
     * 
     * @return array
     */
    public function getBlockConfig(): array
    {
        return $this->blockConfig;
    }

    /**
     * Register the block
     * 
     * @return void
     */
    public function register(): void
    {
        $blockName = $this->getBlockName();
        $blockPath = $this->getBlockPath();
        
        // Check if block.json exists
        if (file_exists($blockPath . '/block.json')) {
            // Register using block.json metadata
            register_block_type_from_metadata($blockPath, [
                'render_callback' => [$this, 'render']
            ]);
        } else {
            // Fallback to manual registration
            $config = $this->getBlockConfig();
            
            // Add render callback if not already set
            if (!isset($config['render_callback'])) {
                $config['render_callback'] = [$this, 'render'];
            }

            register_block_type($blockName, $config);
        }
    }

    /**
     * Get the block path
     * 
     * @return string
     */
    protected function getBlockPath(): string
    {
        $blockName = str_replace('ultimate-woo-addons/', '', $this->getBlockName());
        return ULTIMATEWOOADDONS_PATH . '/build/blocks/' . $blockName;
    }

    /**
     * Get a dependency by key
     * 
     * @param string $key Dependency key
     * @return mixed|null
     */
    protected function getDependency(string $key)
    {
        return $this->dependencies[$key] ?? null;
    }

    /**
     * Check if a dependency exists
     * 
     * @param string $key Dependency key
     * @return bool
     */
    protected function hasDependency(string $key): bool
    {
        return isset($this->dependencies[$key]);
    }

    /**
     * Sanitize block attributes
     * 
     * @param array $attributes Raw attributes
     * @return array Sanitized attributes
     */
    protected function sanitizeAttributes(array $attributes): array
    {
        return $attributes; // Override in child classes for specific sanitization
    }

    /**
     * Get block CSS classes
     * 
     * @param array $attributes Block attributes
     * @return string CSS classes
     */
    protected function getBlockClasses(array $attributes): string
    {
        $classes = ['ultimate-woo-addons-block'];
        $classes[] = 'ultimate-woo-addons-' . str_replace('/', '-', $this->blockName);
        
        if (isset($attributes['className'])) {
            $classes[] = $attributes['className'];
        }

        return implode(' ', $classes);
    }
}
