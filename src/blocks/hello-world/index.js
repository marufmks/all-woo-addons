import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { RichText } from '@wordpress/block-editor';

registerBlockType('ultimate-woo-addons/hello-world', {
    title: __('Hello World', 'ultimate-woo-addons'),
    icon: 'smiley',
    category: 'widgets',
    attributes: {
        content: { type: 'string', source: 'html', selector: 'p' }
    },
    edit({ attributes, setAttributes }) {
        return (
            <RichText
                tagName="p"
                value={attributes.content}
                onChange={(val) => setAttributes({ content: val })}
                placeholder={__('Write something...', 'ultimate-woo-addons')}
            />
        );
    },
    save({ attributes }) {
        return <RichText.Content tagName="p" value={attributes.content} />;
    }
});
