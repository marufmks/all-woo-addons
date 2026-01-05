
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