<?php
namespace UltimateWooAddons\Services;

use UltimateWooAddons\Abstracts\AbstractService;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recently Viewed Products Service
 * 
 * Handles tracking and management of recently viewed products using cookies.
 */
class RecentlyViewedService extends AbstractService
{
    /**
     * Cookie name for recently viewed products
     */
    private const COOKIE_NAME = 'woocommerce_recently_viewed';
    
    /**
     * Maximum number of products to track
     */
    private const MAX_PRODUCTS = 15;
    
    /**
     * Cookie expiration time (30 days)
     */
    private const COOKIE_EXPIRY = 2592000;

    /**
     * Initialize the service
     * 
     * @return void
     */
    public function init(): void
    {
        // Hook into WooCommerce product page views
        add_action('woocommerce_single_product_summary', [$this, 'trackProductView'], 5);
        
        // Also track on template redirect for better coverage
        add_action('template_redirect', [$this, 'trackProductViewOnRedirect']);
    }

    /**
     * Register the service
     * 
     * @return void
     */
    public function register(): void
    {
        // Service is registered via hooks in init()
    }

    /**
     * Track product view on single product page
     * 
     * @return void
     */
    public function trackProductView(): void
    {
        if (!is_product()) {
            return;
        }

        global $product;
        
        if (!$product || !$product->is_visible()) {
            return;
        }

        $this->addToRecentlyViewed($product->get_id());
    }

    /**
     * Track product view on template redirect
     * 
     * @return void
     */
    public function trackProductViewOnRedirect(): void
    {
        if (!is_product()) {
            return;
        }

        $product_id = get_the_ID();
        
        if (!$product_id || !wc_get_product($product_id)) {
            return;
        }

        $this->addToRecentlyViewed($product_id);
    }

    /**
     * Add product to recently viewed list
     * 
     * @param int $product_id Product ID
     * @return void
     */
    private function addToRecentlyViewed(int $product_id): void
    {
        // Get current recently viewed products
        $recently_viewed = $this->getRecentlyViewedProducts();
        
        // Remove the product if it already exists
        $recently_viewed = array_filter($recently_viewed, function($id) use ($product_id) {
            return $id !== $product_id;
        });
        
        // Add the new product to the beginning
        array_unshift($recently_viewed, $product_id);
        
        // Limit to maximum number of products
        $recently_viewed = array_slice($recently_viewed, 0, self::MAX_PRODUCTS);
        
        // Set the cookie
        $this->setRecentlyViewedCookie($recently_viewed);
    }

    /**
     * Get recently viewed product IDs from cookie
     * 
     * @return array Array of product IDs
     */
    public function getRecentlyViewedProducts(): array
    {
        if (empty($_COOKIE[self::COOKIE_NAME])) {
            return [];
        }

        $raw_ids = sanitize_text_field(wp_unslash($_COOKIE[self::COOKIE_NAME]));
        $ids = array_filter(array_map('absint', explode('|', $raw_ids)));
        
        return array_values($ids);
    }

    /**
     * Set recently viewed products cookie
     * 
     * @param array $product_ids Array of product IDs
     * @return void
     */
    private function setRecentlyViewedCookie(array $product_ids): void
    {
        if (empty($product_ids)) {
            return;
        }

        $cookie_value = implode('|', $product_ids);
        
        // Set cookie with proper path and domain
        $cookie_path = COOKIEPATH ? COOKIEPATH : '/';
        $cookie_domain = COOKIE_DOMAIN;
        
        setcookie(
            self::COOKIE_NAME,
            $cookie_value,
            time() + self::COOKIE_EXPIRY,
            $cookie_path,
            $cookie_domain,
            is_ssl(),
            true // HttpOnly
        );
        
        // Also set it in $_COOKIE for immediate access
        $_COOKIE[self::COOKIE_NAME] = $cookie_value;
    }

    /**
     * Clear recently viewed products
     * 
     * @return void
     */
    public function clearRecentlyViewed(): void
    {
        $cookie_path = COOKIEPATH ? COOKIEPATH : '/';
        $cookie_domain = COOKIE_DOMAIN;
        
        setcookie(
            self::COOKIE_NAME,
            '',
            time() - 3600,
            $cookie_path,
            $cookie_domain,
            is_ssl(),
            true
        );
        
        unset($_COOKIE[self::COOKIE_NAME]);
    }

    /**
     * Get recently viewed products with WooCommerce product objects
     * 
     * @param int $limit Maximum number of products to return
     * @return array Array of WC_Product objects
     */
    public function getRecentlyViewedWooProducts(int $limit = 4): array
    {
        $product_ids = $this->getRecentlyViewedProducts();
        
        if (empty($product_ids)) {
            return [];
        }

        // Limit the results
        $product_ids = array_slice($product_ids, 0, $limit);
        
        $args = [
            'include' => $product_ids,
            'orderby' => 'post__in',
            'status' => 'publish',
            'limit' => $limit,
        ];

        return wc_get_products($args);
    }
}
