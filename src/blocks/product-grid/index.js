import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';

registerBlockType('ultimate-woo-addons/product-grid', {
    edit: Edit,
    save: () => null // null means dynamic block, rendering handled by PHP
});
