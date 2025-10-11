<?php
namespace AllWooAddons\Blocks;

use AllWooAddons\Abstracts\AbstractBlock;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Hello World Block Class
 * 
 * A simple example block for demonstration purposes.
 */
class HelloWorldBlock extends AbstractBlock
{
    /**
     * Default attributes
     * 
     * @var array
     */
    private array $defaultAttributes = [
        'message' => 'Hello World!',
        'showDate' => false,
        'textAlign' => 'left',
        'fontSize' => 'medium'
    ];

    /**
     * Constructor
     * 
     * @param string $blockName Block name
     * @param array $blockConfig Block configuration
     * @param array $dependencies Block dependencies
     */
    public function __construct(string $blockName = 'all-woo-addons/hello-world', array $blockConfig = [], array $dependencies = [])
    {
        $defaultConfig = [
            'title' => __('Hello World', 'all-woo-addons'),
            'description' => __('A simple hello world block.', 'all-woo-addons'),
            'category' => 'text',
            'icon' => 'smiley',
            'keywords' => ['hello', 'world', 'greeting'],
            'supports' => [
                'align' => ['left', 'center', 'right'],
                'html' => false,
            ],
            'attributes' => [
                'message' => [
                    'type' => 'string',
                    'default' => 'Hello World!',
                ],
                'showDate' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
                'textAlign' => [
                    'type' => 'string',
                    'default' => 'left',
                ],
                'fontSize' => [
                    'type' => 'string',
                    'default' => 'medium',
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
        $attributes = $this->sanitizeAttributes($attributes);
        $classes = $this->getBlockClasses($attributes);
        
        ob_start();
        ?>
        <div class="<?php echo esc_attr($classes); ?>" style="text-align: <?php echo esc_attr($attributes['textAlign']); ?>;">
            <div class="all-woo-addons-hello-world__message all-woo-addons-hello-world__message--<?php echo esc_attr($attributes['fontSize']); ?>">
                <?php echo esc_html($attributes['message']); ?>
            </div>
            <?php if ($attributes['showDate']): ?>
                <div class="all-woo-addons-hello-world__date">
                    <?php echo esc_html(current_time('F j, Y')); ?>
                </div>
            <?php endif; ?>
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
                    case 'message':
                        $sanitized[$key] = sanitize_text_field($attributes[$key]);
                        break;
                    case 'showDate':
                        $sanitized[$key] = (bool) $attributes[$key];
                        break;
                    case 'textAlign':
                        $allowedAlignments = ['left', 'center', 'right'];
                        $sanitized[$key] = in_array($attributes[$key], $allowedAlignments) ? $attributes[$key] : 'left';
                        break;
                    case 'fontSize':
                        $allowedSizes = ['small', 'medium', 'large'];
                        $sanitized[$key] = in_array($attributes[$key], $allowedSizes) ? $attributes[$key] : 'medium';
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
