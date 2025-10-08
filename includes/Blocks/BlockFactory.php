<?php
namespace UltimateWooAddons\Blocks;

use UltimateWooAddons\Contracts\BlockInterface;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Block Factory Class
 * 
 * Implements Factory pattern for creating block instances.
 * Centralizes block creation logic and provides a consistent interface.
 */
class BlockFactory
{
    /**
     * Registered block types
     * 
     * @var array
     */
    private static array $blockTypes = [];

    /**
     * Block instances cache
     * 
     * @var array
     */
    private static array $instances = [];

    /**
     * Register a block type
     * 
     * @param string $blockName Block name
     * @param string $blockClass Block class name
     * @return void
     */
    public static function registerBlockType(string $blockName, string $blockClass): void
    {
        if (!class_exists($blockClass)) {
            throw new \InvalidArgumentException("Block class '{$blockClass}' does not exist.");
        }

        if (!is_subclass_of($blockClass, BlockInterface::class)) {
            throw new \InvalidArgumentException("Block class '{$blockClass}' must implement BlockInterface.");
        }

        self::$blockTypes[$blockName] = $blockClass;
    }

    /**
     * Create a block instance
     * 
     * @param string $blockName Block name
     * @param array $config Block configuration
     * @param array $dependencies Block dependencies
     * @return BlockInterface
     * @throws \InvalidArgumentException If block type is not registered
     */
    public static function create(string $blockName, array $config = [], array $dependencies = []): BlockInterface
    {
        if (!isset(self::$blockTypes[$blockName])) {
            throw new \InvalidArgumentException("Block type '{$blockName}' is not registered.");
        }

        $blockClass = self::$blockTypes[$blockName];
        
        // Use singleton pattern for block instances
        $cacheKey = $blockName . '_' . md5(serialize($config) . serialize($dependencies));
        
        if (!isset(self::$instances[$cacheKey])) {
            self::$instances[$cacheKey] = new $blockClass($blockName, $config, $dependencies);
        }

        return self::$instances[$cacheKey];
    }

    /**
     * Get all registered block types
     * 
     * @return array
     */
    public static function getRegisteredBlockTypes(): array
    {
        return array_keys(self::$blockTypes);
    }

    /**
     * Check if a block type is registered
     * 
     * @param string $blockName Block name
     * @return bool
     */
    public static function isRegistered(string $blockName): bool
    {
        return isset(self::$blockTypes[$blockName]);
    }

    /**
     * Unregister a block type
     * 
     * @param string $blockName Block name
     * @return void
     */
    public static function unregisterBlockType(string $blockName): void
    {
        unset(self::$blockTypes[$blockName]);
        
        // Clear related instances from cache
        foreach (self::$instances as $key => $instance) {
            if (strpos($key, $blockName . '_') === 0) {
                unset(self::$instances[$key]);
            }
        }
    }

    /**
     * Clear all instances cache
     * 
     * @return void
     */
    public static function clearCache(): void
    {
        self::$instances = [];
    }

    /**
     * Get block class for a registered block type
     * 
     * @param string $blockName Block name
     * @return string|null
     */
    public static function getBlockClass(string $blockName): ?string
    {
        return self::$blockTypes[$blockName] ?? null;
    }
}
