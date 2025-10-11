<?php
/**
 * Class RecentlyViewedServiceTest
 *
 * @package Ultimate_Woo_Addons
 */

/**
 * Test case for RecentlyViewedService.
 */
class RecentlyViewedServiceTest extends WP_UnitTestCase {

    /**
     * Test service initialization
     */
    public function test_service_initialization() {
        $service = new \UltimateWooAddons\Services\RecentlyViewedService();
        
        $this->assertInstanceOf(\UltimateWooAddons\Services\RecentlyViewedService::class, $service);
    }

    /**
     * Test cookie management
     */
    public function test_cookie_management() {
        $service = new \UltimateWooAddons\Services\RecentlyViewedService();
        
        // Test getting empty recently viewed products
        $products = $service->getRecentlyViewedProducts();
        $this->assertIsArray($products);
        $this->assertEmpty($products);
        
        // Test getting WooCommerce products with empty list
        $woo_products = $service->getRecentlyViewedWooProducts();
        $this->assertIsArray($woo_products);
        $this->assertEmpty($woo_products);
    }

    /**
     * Test clearing recently viewed products
     */
    public function test_clear_recently_viewed() {
        $service = new \UltimateWooAddons\Services\RecentlyViewedService();
        
        // This should not throw any errors
        $service->clearRecentlyViewed();
        
        $this->assertTrue(true);
    }
}
