<?php
namespace AllWooAddons\Admin;

use AllWooAddons\Abstracts\AbstractService;
use AllWooAddons\Api\AdminApi;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Admin Service Class
 * 
 * Handles all admin-related functionality including menu registration,
 * script enqueuing, and admin page rendering.
 */
class Admin extends AbstractService
{
    /**
     * Admin page hook
     * 
     * @var string
     */
    private string $adminPageHook = 'toplevel_page_all-woo-addons';

    /**
     * Service-specific registration logic
     * 
     * @return void
     */
    protected function doRegister(): void
    {
        \add_action('admin_menu', [$this, 'registerAdminMenu']);
        \add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        new AdminApi();
    }

    /**
     * Register admin menu
     * 
     * @return void
     */
    public function registerAdminMenu(): void
    {
        \add_menu_page(
            \__('All Woo Addons', 'all-woo-addons'),
            \__('All Woo Addons', 'all-woo-addons'),
            'manage_options',
            'all-woo-addons',
            [$this, 'renderAdminApp'],
            'dashicons-admin-generic'
        );
    }

    /**
     * Render admin app
     * 
     * @return void
     */
    public function renderAdminApp(): void
    {
        echo '<div id="all-woo-addons-admin"></div>';
        \wp_enqueue_script('all-woo-addons-admin');
    }

    /**
     * Enqueue admin scripts
     * 
     * @param string $hook Current admin page hook
     * @return void
     */
    public function enqueueAdminScripts(string $hook): void
    {
        if ($hook !== $this->adminPageHook) {
            return;
        }

        $this->enqueueAdminAssets();
    }

    /**
     * Enqueue admin assets
     * 
     * @return void
     */
    private function enqueueAdminAssets(): void
    {
        $assetFile = $this->getAssetFile('admin');

        if (!$assetFile) {
            return;
        }

        $styleVersion = file_exists(ALLWOOADDONS_PATH . '/build/admin/index.css') 
            ? filemtime(ALLWOOADDONS_PATH . '/build/admin/index.css') 
            : $assetFile['version'];

        \wp_enqueue_style(
            'all-woo-addons-admin-css',
            ALLWOOADDONS_URL . '/build/admin/index.css',
            [],
            $styleVersion
        );

        \wp_enqueue_script(
            'all-woo-addons-admin',
            ALLWOOADDONS_URL . '/build/admin/index.js',
            $assetFile['dependencies'],
            $assetFile['version'],
            true
        );

        \wp_localize_script('all-woo-addons-admin', 'allWooAddonsAdmin', [
            'apiUrl' => \rest_url('all-woo-addons/v1'),
            'nonce' => \wp_create_nonce('wp_rest'),
            'strings' => [
                'dashboard' => \__('Dashboard', 'all-woo-addons'),
                'settings' => \__('Settings', 'all-woo-addons'),
                'blocks' => \__('Blocks', 'all-woo-addons'),
                'save' => \__('Save Settings', 'all-woo-addons'),
                'saved' => \__('Settings Saved!', 'all-woo-addons'),
                'loading' => \__('Loading...', 'all-woo-addons'),
                'error' => \__('Something went wrong', 'all-woo-addons')
            ]
        ]);
    }

    /**
     * Get asset file for a build
     * 
     * @param string $buildName Build name (e.g., 'admin', 'blocks/hello-world')
     * @return array|null Asset file data or null if not found
     */
    private function getAssetFile(string $buildName): ?array
    {
        $assetPath = ALLWOOADDONS_PATH . '/build/' . $buildName . '/index.asset.php';
        
        if (!file_exists($assetPath)) {
            return null;
        }

        return include $assetPath;
    }

    /**
     * Get admin page hook
     * 
     * @return string
     */
    public function getAdminPageHook(): string
    {
        return $this->adminPageHook;
    }
}