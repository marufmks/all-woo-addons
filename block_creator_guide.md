# Block Creator Guide

This guide will walk you through creating a new block for the All Woo Addons plugin. We'll use a **Filter Products** block as a complete example that allows filtering products by category, price range, most sold, trending, etc.

## Table of Contents

1. [Overview](#overview)
2. [Block Architecture](#block-architecture)
3. [Step-by-Step Guide](#step-by-step-guide)
4. [Complete Example: Filter Products Block](#complete-example-filter-products-block)
5. [Build Process](#build-process)
6. [Testing Your Block](#testing-your-block)

---

## Overview

Blocks in this plugin follow a dual-structure approach:
- **PHP Backend**: Handles server-side rendering and block registration
- **JavaScript Frontend**: Handles the block editor UI and preview

All blocks extend `AbstractBlock` and are registered through the `BlockFactory` pattern.

---

## Block Architecture

### File Structure

When creating a new block, you'll need to create files in two locations:

```
plugin-root/
├── includes/Blocks/
│   └── YourBlockName.php          # PHP block class
├── src/blocks/
│   └── your-block-name/
│       ├── block.json             # Block metadata
│       ├── index.js               # Block registration
│       ├── edit.js                # Editor component
│       ├── style.scss             # Frontend styles
│       └── editor.scss            # Editor styles (optional)
└── build/blocks/
    └── your-block-name/           # Built files (auto-generated)
```

---

## Step-by-Step Guide

### Step 1: Create the PHP Block Class

Create a new file: `includes/Blocks/FilterProductsBlock.php`

**Key Requirements:**
- Extend `AbstractBlock`
- Implement `render()` method
- Define default attributes
- Implement `sanitizeAttributes()` method
- Set up block configuration in constructor

### Step 2: Create Block Metadata

Create `src/blocks/filter-products/block.json` with:
- Block name (must match PHP class registration)
- Title, description, icon
- Attributes definition
- Supports configuration

### Step 3: Create JavaScript Files

Create the frontend files:
- `src/blocks/filter-products/index.js` - Block registration
- `src/blocks/filter-products/edit.js` - Editor UI component

### Step 4: Register the Block

Add your block to:
- `includes/Blocks/Blocks.php` - Register with factory and WordPress

### Step 5: Build the Block

Run the build process to compile JavaScript and generate build files.

---

## Complete Example: Filter Products Block

Let's create a complete **Filter Products** block that allows filtering by:
- Category
- Price range
- Sort by: Most sold, Trending, Price (low to high), Price (high to low), Newest
- Number of products to display
- Columns layout

### Step 1: PHP Block Class

Create `includes/Blocks/FilterProductsBlock.php`:

```php
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
        <div class="<?php echo esc_attr($classes); ?> all-woo-addons-filter-products" data-columns="<?php echo esc_attr($columns); ?>">
            <div class="all-woo-addons-filter-products__grid" style="grid-template-columns: repeat(<?php echo esc_attr($columns); ?>, 1fr);">
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
                                    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="all-woo-addons-filter-products__button">
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
```

### Step 2: Block Metadata

Create `src/blocks/filter-products/block.json`:

```json
{
  "apiVersion": 2,
  "name": "all-woo-addons/filter-products",
  "title": "Filter Products",
  "category": "woocommerce",
  "icon": "filter",
  "description": "Display WooCommerce products with filtering options by category, price, and sorting.",
  "keywords": ["products", "filter", "category", "price", "woocommerce", "sort"],
  "supports": {
    "align": ["wide", "full"],
    "html": false
  },
  "attributes": {
    "limit": {
      "type": "number",
      "default": 12
    },
    "columns": {
      "type": "number",
      "default": 4
    },
    "category": {
      "type": "string",
      "default": ""
    },
    "minPrice": {
      "type": "number",
      "default": 0
    },
    "maxPrice": {
      "type": "number",
      "default": 0
    },
    "sortBy": {
      "type": "string",
      "default": "date"
    },
    "showPrice": {
      "type": "boolean",
      "default": true
    },
    "showImage": {
      "type": "boolean",
      "default": true
    },
    "showTitle": {
      "type": "boolean",
      "default": true
    },
    "showRating": {
      "type": "boolean",
      "default": false
    },
    "showButton": {
      "type": "boolean",
      "default": true
    },
    "buttonText": {
      "type": "string",
      "default": "View Product"
    }
  },
  "editorScript": "file:./index.js",
  "style": "file:./style-index.css",
  "editorStyle": "file:./index.css"
}
```

### Step 3: JavaScript Block Registration

Create `src/blocks/filter-products/index.js`:

```javascript
import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import Edit from './edit';
import './style.scss';
import './editor.scss';

registerBlockType(metadata.name, {
    edit: Edit,
    save: () => null, // Dynamic block - rendered by PHP
});
```

### Step 4: Editor Component

Create `src/blocks/filter-products/edit.js`:

```javascript
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
    PanelBody,
    RangeControl,
    ToggleControl,
    TextControl,
    SelectControl,
    __experimentalNumberControl as NumberControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';

export default function Edit({ attributes, setAttributes }) {
    const {
        limit,
        columns,
        category,
        minPrice,
        maxPrice,
        sortBy,
        showPrice,
        showImage,
        showTitle,
        showRating,
        showButton,
        buttonText,
    } = attributes;

    const blockProps = useBlockProps({
        className: 'all-woo-addons-filter-products is-preview',
        style: { ['--awa-fp-columns']: columns },
    });

    // Fetch product categories
    const categories = useSelect((select) => {
        return select('core').getEntityRecords('taxonomy', 'product_cat', {
            per_page: 100,
        });
    }, []);

    // Get category options for select
    const categoryOptions = useMemo(() => {
        const options = [
            { label: __('All Categories', 'all-woo-addons'), value: '' },
        ];
        
        if (categories) {
            categories.forEach((cat) => {
                options.push({
                    label: cat.name,
                    value: cat.slug,
                });
            });
        }
        
        return options;
    }, [categories]);

    // Sort options
    const sortOptions = [
        { label: __('Newest', 'all-woo-addons'), value: 'date' },
        { label: __('Price: Low to High', 'all-woo-addons'), value: 'price_asc' },
        { label: __('Price: High to Low', 'all-woo-addons'), value: 'price_desc' },
        { label: __('Most Sold', 'all-woo-addons'), value: 'sales' },
        { label: __('Popularity', 'all-woo-addons'), value: 'popularity' },
        { label: __('Highest Rated', 'all-woo-addons'), value: 'rating' },
    ];

    // Preview products
    const previewProducts = useMemo(() => {
        return Array.from({ length: Math.min(limit, 8) }, (_, index) => ({
            id: index + 1,
            name: `${__('Product', 'all-woo-addons')} ${index + 1}`,
            price: __('$49.99', 'all-woo-addons'),
        }));
    }, [limit]);

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Filter Settings', 'all-woo-addons')} initialOpen={true}>
                    <SelectControl
                        label={__('Category', 'all-woo-addons')}
                        value={category}
                        options={categoryOptions}
                        onChange={(value) => setAttributes({ category: value })}
                    />
                    
                    <div style={{ display: 'flex', gap: '10px' }}>
                        <NumberControl
                            label={__('Min Price', 'all-woo-addons')}
                            value={minPrice}
                            onChange={(value) => setAttributes({ minPrice: parseFloat(value) || 0 })}
                            min={0}
                            step={0.01}
                        />
                        <NumberControl
                            label={__('Max Price', 'all-woo-addons')}
                            value={maxPrice}
                            onChange={(value) => setAttributes({ maxPrice: parseFloat(value) || 0 })}
                            min={0}
                            step={0.01}
                        />
                    </div>
                    
                    <SelectControl
                        label={__('Sort By', 'all-woo-addons')}
                        value={sortBy}
                        options={sortOptions}
                        onChange={(value) => setAttributes({ sortBy: value })}
                    />
                </PanelBody>
                
                <PanelBody title={__('Layout', 'all-woo-addons')}>
                    <RangeControl
                        label={__('Products to show', 'all-woo-addons')}
                        value={limit}
                        onChange={(value) => setAttributes({ limit: value })}
                        min={1}
                        max={50}
                    />
                    <RangeControl
                        label={__('Columns', 'all-woo-addons')}
                        value={columns}
                        onChange={(value) => setAttributes({ columns: value })}
                        min={1}
                        max={6}
                    />
                </PanelBody>
                
                <PanelBody title={__('Display Options', 'all-woo-addons')}>
                    <ToggleControl
                        label={__('Show image', 'all-woo-addons')}
                        checked={showImage}
                        onChange={(value) => setAttributes({ showImage: value })}
                    />
                    <ToggleControl
                        label={__('Show title', 'all-woo-addons')}
                        checked={showTitle}
                        onChange={(value) => setAttributes({ showTitle: value })}
                    />
                    <ToggleControl
                        label={__('Show price', 'all-woo-addons')}
                        checked={showPrice}
                        onChange={(value) => setAttributes({ showPrice: value })}
                    />
                    <ToggleControl
                        label={__('Show rating', 'all-woo-addons')}
                        checked={showRating}
                        onChange={(value) => setAttributes({ showRating: value })}
                    />
                    <ToggleControl
                        label={__('Show button', 'all-woo-addons')}
                        checked={showButton}
                        onChange={(value) => setAttributes({ showButton: value })}
                    />
                    {showButton && (
                        <TextControl
                            label={__('Button text', 'all-woo-addons')}
                            value={buttonText}
                            onChange={(value) => setAttributes({ buttonText: value })}
                            placeholder={__('View Product', 'all-woo-addons')}
                        />
                    )}
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <div className="all-woo-addons-filter-products__preview-header">
                    <h3>{__('Filter Products Preview', 'all-woo-addons')}</h3>
                    <div className="all-woo-addons-filter-products__preview-filters">
                        {category && (
                            <span className="all-woo-addons-filter-products__filter-tag">
                                {__('Category:', 'all-woo-addons')} {categoryOptions.find(opt => opt.value === category)?.label || category}
                            </span>
                        )}
                        {(minPrice > 0 || maxPrice > 0) && (
                            <span className="all-woo-addons-filter-products__filter-tag">
                                {__('Price:', 'all-woo-addons')} ${minPrice} - ${maxPrice || '∞'}
                            </span>
                        )}
                        <span className="all-woo-addons-filter-products__filter-tag">
                            {__('Sort:', 'all-woo-addons')} {sortOptions.find(opt => opt.value === sortBy)?.label}
                        </span>
                    </div>
                </div>
                
                <div className="all-woo-addons-filter-products__grid">
                    {previewProducts.map((product) => (
                        <div key={product.id} className="all-woo-addons-filter-products__card is-placeholder">
                            {showImage && (
                                <div className="all-woo-addons-filter-products__image is-placeholder">
                                    <span className="all-woo-addons-filter-products__image-placeholder" />
                                </div>
                            )}
                            <div className="all-woo-addons-filter-products__content">
                                {showTitle && (
                                    <h3 className="all-woo-addons-filter-products__title">
                                        {product.name}
                                    </h3>
                                )}
                                {showRating && (
                                    <div className="all-woo-addons-filter-products__rating is-placeholder">
                                        ★★★★★
                                    </div>
                                )}
                                {showPrice && (
                                    <div className="all-woo-addons-filter-products__price">
                                        {product.price}
                                    </div>
                                )}
                                {showButton && (
                                    <div className="all-woo-addons-filter-products__button-wrapper">
                                        <span className="all-woo-addons-filter-products__button is-placeholder">
                                            {buttonText || __('View Product', 'all-woo-addons')}
                                        </span>
                                    </div>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}
```

### Step 5: Styles

Create `src/blocks/filter-products/style.scss`:

```scss
.all-woo-addons-filter-products {
    &__grid {
        display: grid;
        gap: 20px;
        grid-template-columns: repeat(var(--awa-fp-columns, 4), 1fr);
        
        @media (max-width: 768px) {
            grid-template-columns: repeat(2, 1fr);
        }
        
        @media (max-width: 480px) {
            grid-template-columns: 1fr;
        }
    }
    
    &__card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        
        &:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    }
    
    &__image {
        position: relative;
        overflow: hidden;
        
        img {
            width: 100%;
            height: auto;
            display: block;
        }
    }
    
    &__content {
        padding: 15px;
    }
    
    &__title {
        margin: 0 0 10px;
        font-size: 16px;
        
        a {
            text-decoration: none;
            color: inherit;
            
            &:hover {
                color: #0073aa;
            }
        }
    }
    
    &__rating {
        margin: 10px 0;
    }
    
    &__price {
        font-size: 18px;
        font-weight: bold;
        color: #0073aa;
        margin: 10px 0;
    }
    
    &__button-wrapper {
        margin-top: 15px;
    }
    
    &__button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #0073aa;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s ease;
        
        &:hover {
            background-color: #005a87;
        }
    }
    
    &__preview-header {
        margin-bottom: 20px;
        padding: 15px;
        background: #f5f5f5;
        border-radius: 4px;
        
        h3 {
            margin: 0 0 10px;
        }
    }
    
    &__preview-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    &__filter-tag {
        display: inline-block;
        padding: 5px 10px;
        background: #fff;
        border-radius: 4px;
        font-size: 12px;
    }
    
    &__image-placeholder {
        display: block;
        width: 100%;
        height: 200px;
        background: #f0f0f0;
    }
    
    &.is-preview {
        .is-placeholder {
            opacity: 0.6;
        }
    }
}
```

Create `src/blocks/filter-products/editor.scss` (optional, for editor-specific styles):

```scss
// Editor-specific styles if needed
.all-woo-addons-filter-products.is-preview {
    padding: 20px;
    border: 1px dashed #ccc;
}
```

### Step 6: Register the Block

Update `includes/Blocks/Blocks.php`:

**Add to the `use` statements at the top:**
```php
use AllWooAddons\Blocks\FilterProductsBlock;
```

**Add to `registerBlockTypes()` method:**
```php
// Register Filter Products Block
BlockFactory::registerBlockType('all-woo-addons/filter-products', FilterProductsBlock::class);
```

**Add a new registration method:**
```php
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
```

**Add to `registerBlocks()` method:**
```php
public function registerBlocks(): void
{
    $this->registerProductGridBlock();
    $this->registerHelloWorldBlock();
    $this->registerRecentlyViewedProductsBlock();
    $this->registerFilterProductsBlock(); // Add this line
}
```

---

## Build Process

After creating all the files, you need to build the block:

1. **Install dependencies** (if not already done):
   ```bash
   npm install
   ```

2. **Build the block**:
   ```bash
   npm run build
   ```

   Or for development with watch mode:
   ```bash
   npm start
   ```

3. **Verify build output**:
   Check that `build/blocks/filter-products/` directory exists with:
   - `block.json`
   - `index.js`
   - `index.css`
   - `style-index.css`

---

## Testing Your Block

### 1. Clear Cache
- Clear WordPress cache if using a caching plugin
- Hard refresh your browser (Ctrl+Shift+R or Cmd+Shift+R)

### 2. Check Block Registration
- Go to WordPress admin → Plugins
- Ensure the plugin is activated
- Check that no PHP errors appear

### 3. Test in Block Editor
- Create or edit a post/page
- Click the "+" button to add blocks
- Search for "Filter Products"
- The block should appear in the WooCommerce category
- Add the block and test all the controls in the sidebar

### 4. Test Frontend Rendering
- Publish or preview the page
- Verify products are displayed correctly
- Test different filter combinations
- Check responsive layout on mobile devices

### 5. Debugging Tips

**If block doesn't appear:**
- Check browser console for JavaScript errors
- Verify `block.json` exists in `build/blocks/filter-products/`
- Check PHP error logs
- Ensure block is registered in `Blocks.php`

**If products don't display:**
- Verify WooCommerce is active
- Check that products exist matching your filters
- Test with no filters first (all products)
- Check PHP error logs

**If styles don't apply:**
- Verify CSS files are built
- Check that `style` and `editorStyle` are set in `block.json`
- Clear browser cache
- Check for CSS conflicts with theme

---

## Key Points to Remember

1. **Block Name Consistency**: The block name in PHP (`all-woo-addons/filter-products`) must match:
   - `block.json` name field
   - BlockFactory registration
   - Directory name in `src/blocks/` and `build/blocks/`

2. **Attributes**: Always define attributes in both:
   - PHP class constructor (`$defaultAttributes` and `attributes` in config)
   - `block.json` file

3. **Sanitization**: Always sanitize user input in `sanitizeAttributes()` method

4. **Dynamic Blocks**: Use `save: () => null` for blocks rendered by PHP

5. **Build Process**: Always run `npm run build` after creating/modifying block files

6. **WooCommerce Functions**: Check `function_exists('wc_get_products')` before using WooCommerce functions

---

## Additional Resources

- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [WooCommerce Product Functions](https://woocommerce.github.io/code-reference/classes/WooCommerce.html)
- Plugin's `OOP_ARCHITECTURE.md` for architecture details

---

## Summary Checklist

When creating a new block, ensure you:

- [ ] Created PHP block class extending `AbstractBlock`
- [ ] Implemented `render()` method
- [ ] Implemented `sanitizeAttributes()` method
- [ ] Created `block.json` with correct metadata
- [ ] Created `index.js` for block registration
- [ ] Created `edit.js` for editor UI
- [ ] Created `style.scss` for frontend styles
- [ ] Registered block in `Blocks.php`
- [ ] Registered block type with `BlockFactory`
- [ ] Built the block with `npm run build`
- [ ] Tested in block editor
- [ ] Tested frontend rendering
- [ ] Verified responsive design

---

Happy block creating! 🎉

