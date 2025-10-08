<?php
namespace UltimateWooAddons\Blocks;

use UltimateWooAddons\Abstracts\AbstractBlock;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Product Grid Block Class
 * 
 * Handles the product grid block functionality.
 */
class ProductGridBlock extends AbstractBlock
{
    /**
     * Default attributes
     * 
     * @var array
     */
    private array $defaultAttributes = [
        'limit' => 6,
        'columns' => 3,
        'showPrice' => true,
        'showImage' => true,
        'showTitle' => true,
        'category' => '',
        'orderBy' => 'date',
        'order' => 'DESC'
    ];

    /**
     * Constructor
     * 
     * @param string $blockName Block name
     * @param array $blockConfig Block configuration
     * @param array $dependencies Block dependencies
     */
    public function __construct(string $blockName = 'ultimate-woo-addons/product-grid', array $blockConfig = [], array $dependencies = [])
    {
        $defaultConfig = [
            'title' => __('Product Grid', 'ultimate-woo-addons'),
            'description' => __('Display a grid of WooCommerce products.', 'ultimate-woo-addons'),
            'category' => 'woocommerce',
            'icon' => 'grid-view',
            'keywords' => ['products', 'grid', 'woocommerce'],
            'supports' => [
                'align' => ['wide', 'full'],
                'html' => false,
            ],
            'attributes' => [
                'limit' => [
                    'type' => 'number',
                    'default' => 6,
                ],
                'columns' => [
                    'type' => 'number',
                    'default' => 3,
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
                'category' => [
                    'type' => 'string',
                    'default' => '',
                ],
                'orderBy' => [
                    'type' => 'string',
                    'default' => 'date',
                ],
                'order' => [
                    'type' => 'string',
                    'default' => 'DESC',
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
            return '<p>' . __('WooCommerce is not active.', 'ultimate-woo-addons') . '</p>';
        }

        $attributes = $this->sanitizeAttributes($attributes);
        $products = $this->getProducts($attributes);

        if (empty($products)) {
            return '<p>' . __('No products found.', 'ultimate-woo-addons') . '</p>';
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
            'orderby' => $attributes['orderBy'],
            'order' => $attributes['order'],
        ];

        if (!empty($attributes['category'])) {
            $args['category'] = [$attributes['category']];
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
        <div class="<?php echo esc_attr($classes); ?>" data-columns="<?php echo esc_attr($columns); ?>">
            <div class="ultimate-woo-addons-product-grid__container" style="grid-template-columns: repeat(<?php echo esc_attr($columns); ?>, 1fr);">
                <?php foreach ($products as $product): ?>
                    <div class="ultimate-woo-addons-product-card">
                        <?php if ($attributes['showImage']): ?>
                            <div class="ultimate-woo-addons-product-card__image">
                                <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                    <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="ultimate-woo-addons-product-card__content">
                            <?php if ($attributes['showTitle']): ?>
                                <h3 class="ultimate-woo-addons-product-card__title">
                                    <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                        <?php echo esc_html($product->get_name()); ?>
                                    </a>
                                </h3>
                            <?php endif; ?>
                            
                            <?php if ($attributes['showPrice']): ?>
                                <div class="ultimate-woo-addons-product-card__price">
                                    <?php echo $product->get_price_html(); ?>
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
                    case 'columns':
                        $sanitized[$key] = max(1, intval($attributes[$key]));
                        break;
                    case 'showPrice':
                    case 'showImage':
                    case 'showTitle':
                        $sanitized[$key] = (bool) $attributes[$key];
                        break;
                    case 'category':
                        $sanitized[$key] = sanitize_text_field($attributes[$key]);
                        break;
                    case 'orderBy':
                        $allowedOrderBy = ['date', 'title', 'price', 'popularity', 'rating'];
                        $sanitized[$key] = in_array($attributes[$key], $allowedOrderBy) ? $attributes[$key] : 'date';
                        break;
                    case 'order':
                        $sanitized[$key] = strtoupper($attributes[$key]) === 'ASC' ? 'ASC' : 'DESC';
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
