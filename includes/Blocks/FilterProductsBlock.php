<?php
namespace AllWooAddons\Blocks;

use AllWooAddons\Abstracts\AbstractBlock;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Filter Products Block Class
 * 
 * Handles the filter products block functionality with category, price, and sorting options.
 */
class FilterProductsBlock extends AbstractBlock
{
    /**
     * Default attributes
     * 
     * @var array
     */
    private array $defaultAttributes = [
        'limit' => 12,
        'columns' => 4,
        'category' => '',
        'minPrice' => 0,
        'maxPrice' => 0,
        'sortBy' => 'date', // date, price_asc, price_desc, popularity, rating, sales
        'showPrice' => true,
        'showImage' => true,
        'showTitle' => true,
        'showRating' => false,
        'showButton' => true,
        'buttonText' => 'View Product',
    ];

    /**
     * Constructor
     * 
     * @param string $blockName Block name
     * @param array $blockConfig Block configuration
     * @param array $dependencies Block dependencies
     */
    public function __construct(
        string $blockName = 'all-woo-addons/filter-products',
        array $blockConfig = [],
        array $dependencies = []
    ) {
        $defaultConfig = [
            'title' => __('Filter Products', 'all-woo-addons'),
            'description' => __('Display WooCommerce products with filtering options by category, price, and sorting.', 'all-woo-addons'),
            'category' => 'woocommerce',
            'icon' => 'filter',
            'keywords' => ['products', 'filter', 'category', 'price', 'woocommerce'],
            'supports' => [
                'align' => ['wide', 'full'],
                'html' => false,
            ],
            'attributes' => [
                'limit' => [
                    'type' => 'number',
                    'default' => 12,
                ],
                'columns' => [
                    'type' => 'number',
                    'default' => 4,
                ],
                'category' => [
                    'type' => 'string',
                    'default' => '',
                ],
                'minPrice' => [
                    'type' => 'number',
                    'default' => 0,
                ],
                'maxPrice' => [
                    'type' => 'number',
                    'default' => 0,
                ],
                'sortBy' => [
                    'type' => 'string',
                    'default' => 'date',
                ],
                'showPrice' => [
                    'type' => 'boolean',
                    'default' => true,
                ],
                'showImage' => [
                    'type' => 'boolean',
                    'default' => true,
                ],
                'showTitle' => [
                    'type' => 'boolean',
                    'default' => true,
                ],
                'showRating' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
                'showButton' => [
                    'type' => 'boolean',
                    'default' => true,
                ],
                'buttonText' => [
                    'type' => 'string',
                    'default' => 'View Product',
                ],
            ],
        ];

        $blockConfig = array_merge($defaultConfig, $blockConfig);
        parent::__construct($blockName, $blockConfig, $dependencies);
    }

    /**
     * Render the block content
     * 
     * @param array $attributes Block attributes
     * @param string $content Block content
     * @return string
     */
    public function render(array $attributes, string $content = ''): string
    {
        if (!function_exists('wc_get_products')) {
            return '<p>' . __('WooCommerce is not active.', 'all-woo-addons') . '</p>';
        }

        $attributes = $this->sanitizeAttributes($attributes);
        $products = $this->getProducts($attributes);

        if (empty($products)) {
            return '<p class="all-woo-addons-no-products">' . __('No products found matching your criteria.', 'all-woo-addons') . '</p>';
        }

        return $this->renderProductGrid($products, $attributes);
    }

    /**
     * Get products based on attributes
     * 
     * @param array $attributes Block attributes
     * @return array
     */
    private function getProducts(array $attributes): array
    {
        $args = [
            'limit' => $attributes['limit'],
            'status' => 'publish',
        ];

        // Category filter
        if (!empty($attributes['category'])) {
            $args['category'] = [$attributes['category']];
        }

        // Price filter
        if ($attributes['minPrice'] > 0 || $attributes['maxPrice'] > 0) {
            $args['meta_query'] = [];

            if ($attributes['minPrice'] > 0) {
                $args['meta_query'][] = [
                    'key' => '_price',
                    'value' => $attributes['minPrice'],
                    'compare' => '>=',
                    'type' => 'NUMERIC',
                ];
            }

            if ($attributes['maxPrice'] > 0) {
                $args['meta_query'][] = [
                    'key' => '_price',
                    'value' => $attributes['maxPrice'],
                    'compare' => '<=',
                    'type' => 'NUMERIC',
                ];
            }
        }

        // Sorting
        switch ($attributes['sortBy']) {
            case 'price_asc':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order'] = 'ASC';
                break;
            case 'price_desc':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order'] = 'DESC';
                break;
            case 'popularity':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'total_sales';
                $args['order'] = 'DESC';
                break;
            case 'rating':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_wc_average_rating';
                $args['order'] = 'DESC';
                break;
            case 'sales':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'total_sales';
                $args['order'] = 'DESC';
                break;
            default: // date
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
        }

        return wc_get_products($args);
    }

    /**
     * Render product grid HTML
     * 
     * @param array $products Products array
     * @param array $attributes Block attributes
     * @return string
     */
    private function renderProductGrid(array $products, array $attributes): string
    {
        $classes = $this->getBlockClasses($attributes);
        $columns = $attributes['columns'];

        ob_start();
        ?>
        <div class="<?php echo esc_attr($classes); ?> all-woo-addons-filter-products"
            data-columns="<?php echo esc_attr($columns); ?>">
            <div class="all-woo-addons-filter-products__grid"
                style="grid-template-columns: repeat(<?php echo esc_attr($columns); ?>, 1fr);">
                <?php foreach ($products as $product): ?>
                    <div class="all-woo-addons-filter-products__card">
                        <?php if ($attributes['showImage']): ?>
                            <div class="all-woo-addons-filter-products__image">
                                <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                    <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="all-woo-addons-filter-products__content">
                            <?php if ($attributes['showTitle']): ?>
                                <h3 class="all-woo-addons-filter-products__title">
                                    <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                        <?php echo esc_html($product->get_name()); ?>
                                    </a>
                                </h3>
                            <?php endif; ?>

                            <?php if ($attributes['showRating'] && function_exists('wc_review_ratings_enabled') && wc_review_ratings_enabled() && $product->get_rating_count() > 0): ?>
                                <div class="all-woo-addons-filter-products__rating">
                                    <?php echo wc_get_rating_html($product->get_average_rating(), $product->get_rating_count()); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($attributes['showPrice']): ?>
                                <div class="all-woo-addons-filter-products__price">
                                    <?php echo $product->get_price_html(); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($attributes['showButton']): ?>
                                <div class="all-woo-addons-filter-products__button-wrapper">
                                    <a href="<?php echo esc_url($product->get_permalink()); ?>"
                                        class="all-woo-addons-filter-products__button">
                                        <?php echo esc_html($attributes['buttonText']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Sanitize block attributes
     * 
     * @param array $attributes Raw attributes
     * @return array Sanitized attributes
     */
    protected function sanitizeAttributes(array $attributes): array
    {
        $sanitized = [];

        foreach ($this->defaultAttributes as $key => $default) {
            if (isset($attributes[$key])) {
                switch ($key) {
                    case 'limit':
                        $sanitized[$key] = max(1, min(50, intval($attributes[$key])));
                        break;
                    case 'columns':
                        $sanitized[$key] = max(1, min(6, intval($attributes[$key])));
                        break;
                    case 'category':
                        $sanitized[$key] = sanitize_text_field($attributes[$key]);
                        break;
                    case 'minPrice':
                    case 'maxPrice':
                        $sanitized[$key] = max(0, floatval($attributes[$key]));
                        break;
                    case 'sortBy':
                        $allowedSortBy = ['date', 'price_asc', 'price_desc', 'popularity', 'rating', 'sales'];
                        $sanitized[$key] = in_array($attributes[$key], $allowedSortBy) ? $attributes[$key] : 'date';
                        break;
                    case 'showPrice':
                    case 'showImage':
                    case 'showTitle':
                    case 'showRating':
                    case 'showButton':
                        $sanitized[$key] = (bool) $attributes[$key];
                        break;
                    case 'buttonText':
                        $sanitized[$key] = sanitize_text_field($attributes[$key]);
                        break;
                    default:
                        $sanitized[$key] = $attributes[$key];
                }
            } else {
                $sanitized[$key] = $default;
            }
        }

        return $sanitized;
    }
}