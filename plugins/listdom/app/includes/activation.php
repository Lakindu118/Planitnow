<?php
// no direct access
defined('ABSPATH') || die();

if(!class_exists('LSD_Activation')):

/**
 * Listdom License Activation Class.
 *
 * @class LSD_Activation
 * @version	1.0.0
 */
class LSD_Activation extends LSD_Base
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}
    
    public function init()
    {
        add_action('lsd_admin_dashboard_tabs', [$this, 'tab']);
        add_action('lsd_admin_dashboard_contents', [$this, 'content']);

        // Activate
        add_action('wp_ajax_lsd_activation', [$this, 'activate']);
    }

    public function tab($tab)
    {
        // List of Products
        $products = LSD_Base::products();

        // No products
        if(!count($products)) return;

        echo '<a class="nav-tab '.($tab == 'activation' ? 'nav-tab-active' : '').'" href="'.esc_url(admin_url('admin.php?page=listdom&tab=activation')).'">'.esc_html__('Activation', 'listdom').'</a>';
    }

    public function content($tab)
    {
        // It's not Activation Tab
        if($tab !== 'activation') return;

        // List of Products
        $products = LSD_Base::products();

        // No products
        if(!count($products)) return;

        $this->include_html_file('menus/dashboard/tabs/activation.php', [
            'parameters' => [
                'products' => $products
            ]
        ]);
    }

    /**
     * @return void
     */
    public function activate()
    {
        $wpnonce = isset($_POST['_wpnonce']) ? sanitize_text_field($_POST['_wpnonce']) : '';

        // Check if nonce is not set
        if(!trim($wpnonce)) $this->response(['success' => 0, 'code' => 'NONCE_MISSING']);

        // List of Products
        $products = LSD_Base::products();

        // No products
        if(!count($products)) $this->response(['success' => 0, 'code' => 'NO_PRODUCTS']);

        // Product Key
        $key = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, $key.'_activation_form')) $this->response(['success' => 0, 'code' => 'NONCE_IS_INVALID']);

        // Data
        $license_key = isset($_POST['license_key']) ? sanitize_text_field($_POST['license_key']) : '';
        $basename = isset($_POST['basename']) ? sanitize_text_field($_POST['basename']) : '';

        // Licensing Handler
        $licensing = new LSD_Plugin_Licensing([
            'basename' => $basename,
            'prefix' => $key
        ]);

        // Activation
        list($status, $message) = $licensing->activate($license_key);

        // Print the response
        $this->response(['success' => $status, 'message' => $message]);
    }
}

endif;