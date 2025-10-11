import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
    PanelBody,
    RangeControl,
    ToggleControl,
    TextControl,
    Notice,
} from '@wordpress/components';
import { useMemo } from '@wordpress/element';
import { createElement } from '@wordpress/element';

const MAX_PLACEHOLDERS = 6;

export default function Edit({ attributes, setAttributes }) {
    const {
        limit,
        columns,
        showPrice,
        showRating,
        showButton,
        heading,
        buttonText,
    } = attributes;

    const blockProps = useBlockProps({
        className: 'all-woo-addons-recently-viewed-products is-preview',
        style: { ['--awa-rvp-columns']: columns },
    });

    const previewItems = useMemo(
        () =>
            Array.from({ length: Math.min(limit, MAX_PLACEHOLDERS) }, (_, index) => ({
                name: `${__('Product', 'all-woo-addons')} ${index + 1}`,
                price: __('$49.00', 'all-woo-addons'),
            })),
        [limit]
    );

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Layout', 'all-woo-addons')} initialOpen={true}>
                    <RangeControl
                        label={__('Products to show', 'all-woo-addons')}
                        value={limit}
                        onChange={(value) => setAttributes({ limit: value })}
                        min={1}
                        max={12}
                    />
                    <RangeControl
                        label={__('Columns', 'all-woo-addons')}
                        value={columns}
                        onChange={(value) => setAttributes({ columns: value })}
                        min={1}
                        max={6}
                    />
                    <TextControl
                        label={__('Heading', 'all-woo-addons')}
                        value={heading}
                        onChange={(value) => setAttributes({ heading: value })}
                        placeholder={__('Recently Viewed', 'all-woo-addons')}
                    />
                </PanelBody>
                <PanelBody title={__('Display options', 'all-woo-addons')}>
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
                <Notice status="info" isDismissible={false}>
                    {__('Visitors will see their own recently viewed products on the front end.', 'all-woo-addons')}
                </Notice>

                {heading && (
                    <header className="all-woo-addons-recently-viewed-products__header">
                        <h2>{heading}</h2>
                        <span className="all-woo-addons-recently-viewed-products__accent" />
                    </header>
                )}

                <div className="all-woo-addons-recently-viewed-products__grid">
                    {previewItems.map((item, index) => (
                        <article key={index} className="all-woo-addons-recently-viewed-products__card">
                            <div className="all-woo-addons-recently-viewed-products__media is-placeholder">
                                <span className="all-woo-addons-recently-viewed-products__image-placeholder" />
                                {showButton && (
                                    <span className="all-woo-addons-recently-viewed-products__button is-placeholder-button">
                                        {buttonText || __('View Product', 'all-woo-addons')}
                                    </span>
                                )}
                            </div>
                            <div className="all-woo-addons-recently-viewed-products__content">
                                <span className="all-woo-addons-recently-viewed-products__title">
                                    {item.name}
                                </span>
                                {showRating && (
                                    <span className="all-woo-addons-recently-viewed-products__rating is-placeholder-rating">
                                        ★★★★★
                                    </span>
                                )}
                                {showPrice && (
                                    <span className="all-woo-addons-recently-viewed-products__price">
                                        {item.price}
                                    </span>
                                )}
                            </div>
                        </article>
                    ))}
                </div>
            </div>
        </>
    );
}
