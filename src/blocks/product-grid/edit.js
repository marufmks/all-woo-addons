import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';

export default function Edit() {
    const blockProps = useBlockProps();

    // Fetch products using WooCommerce store API
    const products = useSelect((select) => {
        return select('core').getEntityRecords('postType', 'product', { per_page: 6 });
    }, []);

    if (!products) {
        return <p {...blockProps}>Loading products...</p>;
    }

    if (products.length === 0) {
        return <p {...blockProps}>No products found.</p>;
    }

    return (
        <div {...blockProps} className="all-woo-addons-product-grid">
            {products.map((product) => (
                <div key={product.id} className="all-woo-addons-product-card">
                    <h3>{product.title.rendered}</h3>
                </div>
            ))}
        </div>
    );
}
