<?php
namespace AllWooAddons\Blocks;

use AllWooAddons\Abstracts\AbstractBlock;

if (!defined('ABSPATH')) {
    exit;
}

class RecentlyViewedProductsBlock extends AbstractBlock
{
    private array $defaultAttributes = [
        'limit' => 4,
        'columns' => 4,
        'showPrice' => true,
        'showRating' => true,
        'showButton' => true,
        'heading' => '',
        'buttonText' => '',
    ];

    public function __construct(string $blockName = 'all-woo-addons/recently-viewed-products', array $blockConfig = [], array $dependencies = [])
    {
        $defaultConfig = [
            'title' => __('Recently Viewed Products', 'all-woo-addons'),
            'description' => __('Display the products a visitor has recently viewed with an elegant layout.', 'all-woo-addons'),
            'category' => 'widgets',
            'icon' => 'visibility',
            'keywords' => ['recent', 'viewed', 'history', 'woocommerce'],
            'supports' => [
                'align' => ['wide', 'full'],
                'html' => false,
            ],
            'attributes' => [
                'limit' => [
                    'type' => 'number',
                    'default' => 4,
                ],
                'columns' => [
                    'type' => 'number',
                    'default' => 4,
                ],
                'showPrice' => [
                    'type' => 'boolean',
                    'default' => true,
                ],
                'showRating' => [
                    'type' => 'boolean',
                    'default' => true,
                ],
                'showButton' => [
                    'type' => 'boolean',
                    'default' => true,
                ],
                'heading' => [
                    'type' => 'string',
                    'default' => __('Recently Viewed', 'all-woo-addons'),
                ],
                'buttonText' => [
                    'type' => 'string',
                    'default' => __('View Product', 'all-woo-addons'),
                ],
            ],
        ];

        $blockConfig = array_merge($defaultConfig, $blockConfig);
        parent::__construct($blockName, $blockConfig, $dependencies);
    }

    public function render(array $attributes, string $content = ''): string
    {
        if (!function_exists('wc_get_products')) {
            return '<p>' . esc_html__('WooCommerce is not active.', 'all-woo-addons') . '</p>';
        }

        $attributes = $this->sanitizeAttributes($attributes);
        $products = $this->getRecentlyViewedProducts($attributes);

        if (empty($products)) {
            return '<div class="all-woo-addons-recently-viewed-products-empty">' . esc_html__('Products you viewed recently will appear here as you browse the shop.', 'all-woo-addons') . '</div>';
        }

        return $this->renderProducts($products, $attributes);
    }

    private function getRecentlyViewedProducts(array $attributes): array
    {
        // Get the recently viewed service from the container
        $plugin = \AllWooAddons\Core\Plugin::getInstance();
        $recentlyViewedService = $plugin->getService('recentlyViewed');
        
        if (!$recentlyViewedService) {
            return [];
        }

        return $recentlyViewedService->getRecentlyViewedWooProducts($attributes['limit']);
    }

    private function renderProducts(array $products, array $attributes): string
    {
        $classes = trim($this->getBlockClasses($attributes) . ' all-woo-addons-recently-viewed-products');
        $columns = max(1, min(6, $attributes['columns']));
        $heading = trim($attributes['heading']);

        ob_start();
        ?>
        <section class="<?php echo esc_attr($classes); ?>" data-columns="<?php echo esc_attr($columns); ?>">
            <?php if ($heading !== ''): ?>
                <header class="all-woo-addons-recently-viewed-products__header">
                    <h2><?php echo esc_html($heading); ?></h2>
                    <span class="all-woo-addons-recently-viewed-products__accent"></span>
                </header>
            <?php endif; ?>
            <div class="all-woo-addons-recently-viewed-products__grid" style="--awa-rvp-columns: <?php echo esc_attr($columns); ?>;">
                <?php foreach ($products as $product): ?>
                    <?php if (!$product instanceof \WC_Product) { continue; } ?>
                    <article class="all-woo-addons-recently-viewed-products__card">
                        <div class="all-woo-addons-recently-viewed-products__media">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                            </a>
                            <?php if ($attributes['showButton']): ?>
                                <a class="all-woo-addons-recently-viewed-products__button" href="<?php echo esc_url($product->get_permalink()); ?>">
                                    <?php echo esc_html($attributes['buttonText']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="all-woo-addons-recently-viewed-products__content">
                            <h3 class="all-woo-addons-recently-viewed-products__title">
                                <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                    <?php echo esc_html($product->get_name()); ?>
                                </a>
                            </h3>
                            <?php if ($attributes['showRating'] && function_exists('wc_review_ratings_enabled') && wc_review_ratings_enabled() && $product->get_rating_count() > 0): ?>
                                <div class="all-woo-addons-recently-viewed-products__rating">
                                    <?php echo wc_get_rating_html($product->get_average_rating(), $product->get_rating_count()); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($attributes['showPrice']): ?>
                                <div class="all-woo-addons-recently-viewed-products__price">
                                    <?php echo wp_kses_post($product->get_price_html()); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php

        return ob_get_clean();
    }

    protected function sanitizeAttributes(array $attributes): array
    {
        $sanitized = $this->defaultAttributes;

        $sanitized['limit'] = isset($attributes['limit']) ? max(1, min(12, (int) $attributes['limit'])) : $this->defaultAttributes['limit'];
        $sanitized['columns'] = isset($attributes['columns']) ? max(1, min(6, (int) $attributes['columns'])) : $this->defaultAttributes['columns'];
        $sanitized['showPrice'] = isset($attributes['showPrice']) ? (bool) $attributes['showPrice'] : $this->defaultAttributes['showPrice'];
        $sanitized['showRating'] = isset($attributes['showRating']) ? (bool) $attributes['showRating'] : $this->defaultAttributes['showRating'];
        $sanitized['showButton'] = isset($attributes['showButton']) ? (bool) $attributes['showButton'] : $this->defaultAttributes['showButton'];
        $sanitized['heading'] = isset($attributes['heading']) && $attributes['heading'] !== '' ? wp_strip_all_tags($attributes['heading']) : __('Recently Viewed', 'all-woo-addons');
        $sanitized['buttonText'] = isset($attributes['buttonText']) && $attributes['buttonText'] !== '' ? sanitize_text_field($attributes['buttonText']) : __('View Product', 'all-woo-addons');

        return $sanitized;
    }
}
