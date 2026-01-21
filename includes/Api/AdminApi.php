<?php
namespace AllWooAddons\Api;

if (!defined('ABSPATH')) {
    exit;
}

class AdminApi
{
    private string $optionName = 'all_woo_addons_settings';

    public function __construct()
    {
        \add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes(): void
    {
        \register_rest_route('all-woo-addons/v1', '/test', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'testEndpoint'],
                'permission_callback' => '__return_true'
            ]
        ]);

        \register_rest_route('all-woo-addons/v1', '/settings', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'getSettings'],
                'permission_callback' => [$this, 'checkPermission']
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'updateSettings'],
                'permission_callback' => [$this, 'checkPermission']
            ]
        ]);

        \register_rest_route('all-woo-addons/v1', '/stats', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'getStats'],
                'permission_callback' => [$this, 'checkPermission']
            ]
        ]);

        \register_rest_route('all-woo-addons/v1', '/blocks', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'getBlocks'],
                'permission_callback' => [$this, 'checkPermission']
            ]
        ]);
    }

    public function checkPermission(): bool
    {
        return \current_user_can('manage_options');
    }

    public function testEndpoint(): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'status' => 'success',
            'message' => 'API is working',
            'timestamp' => \current_time('mysql')
        ], 200);
    }

    public function getSettings(): \WP_REST_Response
    {
        $settings = \get_option($this->optionName, $this->getDefaultSettings());
        return new \WP_REST_Response($settings, 200);
    }

    public function updateSettings(\WP_REST_Request $request): \WP_REST_Response
    {
        $settings = $request->get_json_params();
        $currentSettings = \get_option($this->optionName, $this->getDefaultSettings());
        
        $updatedSettings = \array_merge($currentSettings, $settings);
        \update_option($this->optionName, $updatedSettings);

        return new \WP_REST_Response($updatedSettings, 200);
    }

    public function getStats(): \WP_REST_Response
    {
        if (!\class_exists('WooCommerce')) {
            return new \WP_REST_Response([
                'error' => 'WooCommerce is not active',
                'totalRevenue' => 0,
                'totalOrders' => 0,
                'totalProducts' => 0,
                'totalCustomers' => 0,
                'averageOrderValue' => 0,
                'recentOrders' => [],
                'topProducts' => [],
                'revenueByMonth' => []
            ], 200);
        }

        $stats = [
            'totalRevenue' => $this->getTotalRevenue(),
            'totalOrders' => $this->getTotalOrders(),
            'totalProducts' => $this->getTotalProducts(),
            'totalCustomers' => $this->getTotalCustomers(),
            'averageOrderValue' => $this->getAverageOrderValue(),
            'recentOrders' => $this->getRecentOrders(),
            'topProducts' => $this->getTopProducts(),
            'revenueByMonth' => $this->getRevenueByMonth()
        ];

        return new \WP_REST_Response($stats, 200);
    }

    public function getBlocks(): \WP_REST_Response
    {
        $blocks = [
            [
                'id' => 'product-grid',
                'name' => 'Product Grid',
                'description' => 'Display products in a grid layout',
                'icon' => 'grid-view',
                'category' => 'woocommerce'
            ],
            [
                'id' => 'filter-products',
                'name' => 'Filter Products',
                'description' => 'Filter products by categories, attributes, etc.',
                'icon' => 'filter',
                'category' => 'woocommerce'
            ],
            [
                'id' => 'recently-viewed-products',
                'name' => 'Recently Viewed Products',
                'description' => 'Show products the user has recently viewed',
                'icon' => 'visibility',
                'category' => 'woocommerce'
            ]
        ];

        return new \WP_REST_Response($blocks, 200);
    }

    private function getDefaultSettings(): array
    {
        return [
            'blocks' => [
                'product-grid' => true,
                'filter-products' => true,
                'recently-viewed-products' => true
            ],
            'dashboard' => [
                'showRevenue' => true,
                'showOrders' => true,
                'showProducts' => true,
                'showCustomers' => true,
                'dateRange' => '30days'
            ]
        ];
    }

    private function getTotalRevenue(): float
    {
        global $wpdb;
        
        $wpdb->suppress_errors(true);
        $wpdb->hide_errors();
        
        $result = $wpdb->get_var("
            SELECT SUM(CAST(meta.meta_value AS DECIMAL(20,2)))
            FROM {$wpdb->posts} AS posts
            INNER JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
            WHERE posts.post_type = 'shop_order'
            AND posts.post_status IN ('wc-completed', 'wc-processing', 'wc-on-hold')
            AND meta.meta_key = '_order_total'
        ");

        return (float) $result ?: 0;
    }

    private function getTotalOrders(): int
    {
        $count = \wc_get_orders([
            'status' => ['wc-completed', 'wc-processing', 'wc-on-hold', 'wc-pending'],
            'return' => 'count',
            'limit' => -1
        ]);

        return (int) $count;
    }

    private function getTotalProducts(): int
    {
        $count = \wp_count_posts('product');
        return (int) ($count->publish ?? 0);
    }

    private function getTotalCustomers(): int
    {
        $result = \count_users();
        $customerRole = \get_role('customer');
        
        if ($customerRole && isset($result['avail_roles']['customer'])) {
            return (int) $result['avail_roles']['customer'];
        }

        return 0;
    }

    private function getAverageOrderValue(): float
    {
        $totalRevenue = $this->getTotalRevenue();
        $totalOrders = $this->getTotalOrders();

        if ($totalOrders === 0) {
            return 0.0;
        }

        return \round($totalRevenue / $totalOrders, 2);
    }

    private function getRecentOrders(int $limit = 5): array
    {
        $orders = \wc_get_orders([
            'status' => ['wc-completed', 'wc-processing', 'wc-pending'],
            'limit' => $limit,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        $recentOrders = [];
        
        foreach ($orders as $order) {
            $recentOrders[] = [
                'id' => $order->get_id(),
                'number' => $order->get_order_number(),
                'status' => $order->get_status(),
                'total' => $order->get_total(),
                'currency' => $order->get_currency(),
                'date' => $order->get_date_created()->date('Y-m-d H:i:s'),
                'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()
            ];
        }

        return $recentOrders;
    }

    private function getTopProducts(int $limit = 5): array
    {
        global $wpdb;
        
        $wpdb->suppress_errors(true);
        $wpdb->hide_errors();
        
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT p.ID, p.post_title, SUM(oim.meta_value) as total_sold
            FROM {$wpdb->posts} AS p
            INNER JOIN {$wpdb->prefix}woocommerce_order_items AS oi ON p.ID = oi.order_item_id
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim ON oi.order_item_id = oim.order_item_id
            INNER JOIN {$wpdb->posts} AS o ON oi.order_id = o.ID
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND o.post_type = 'shop_order'
            AND o.post_status IN ('wc-completed', 'wc-processing')
            AND oim.meta_key = '_qty'
            GROUP BY p.ID, p.post_title
            ORDER BY total_sold DESC
            LIMIT %d
        ", $limit));

        $topProducts = [];
        
        foreach ($results as $product) {
            $topProducts[] = [
                'id' => $product->ID,
                'name' => $product->post_title,
                'sold' => (int) $product->total_sold
            ];
        }

        return $topProducts;
    }

    private function getRevenueByMonth(int $months = 6): array
    {
        global $wpdb;
        
        $wpdb->suppress_errors(true);
        $wpdb->hide_errors();
        
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT 
                DATE_FORMAT(post_date, '%%Y-%%m') as month,
                SUM(CAST(meta.meta_value AS DECIMAL(20,2))) as revenue
            FROM {$wpdb->posts} AS posts
            INNER JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
            WHERE posts.post_type = 'shop_order'
            AND posts.post_status IN ('wc-completed', 'wc-processing', 'wc-on-hold')
            AND meta.meta_key = '_order_total'
            AND posts.post_date >= DATE_SUB(NOW(), INTERVAL %d MONTH)
            GROUP BY DATE_FORMAT(post_date, '%%Y-%%m')
            ORDER BY month ASC
        ", $months));

        $revenueByMonth = [];
        
        foreach ($results as $row) {
            $revenueByMonth[] = [
                'month' => $row->month,
                'revenue' => (float) $row->revenue
            ];
        }

        return $revenueByMonth;
    }
}
