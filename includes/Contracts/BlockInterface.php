<?php
namespace UltimateWooAddons\Contracts;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Block Interface
 * 
 * All blocks should implement this interface to ensure
 * consistent block registration and rendering.
 */
interface BlockInterface
{
    /**
     * Get the block name
     * 
     * @return string
     */
    public function getBlockName(): string;

    /**
     * Get the block configuration
     * 
     * @return array
     */
    public function getBlockConfig(): array;

    /**
     * Render the block content
     * 
     * @param array $attributes Block attributes
     * @param string $content Block content
     * @return string
     */
    public function render(array $attributes, string $content = ''): string;

    /**
     * Register the block
     * 
     * @return void
     */
    public function register(): void;
}
