<?php
// no direct access
defined('ABSPATH') || die();

if(!class_exists('LSD_Dashboard')):

/**
 * Listdom Dashboard Class.
 *
 * @class LSD_Dashboard
 * @version	1.0.0
 */
class LSD_Dashboard extends LSD_Base
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
        // Dashboard Shortcode
        $Dashboard = new LSD_Shortcodes_Dashboard();
        $Dashboard->init();
    }

    public function modules()
    {
        $modules = array(
            array('label' => esc_html__('Address / Map', 'listdom'), 'key' => 'address'),
            array('label' => esc_html__('Price Options', 'listdom'), 'key' => 'price'),
            array('label' => esc_html__('Work Hours', 'listdom'), 'key' => 'availability'),
            array('label' => esc_html__('Contact Details', 'listdom'), 'key' => 'contact'),
            array('label' => esc_html__('Remark', 'listdom'), 'key' => 'remark'),
            array('label' => esc_html__('Gallery', 'listdom'), 'key' => 'gallery'),
            array('label' => esc_html__('Attributes', 'listdom'), 'key' => 'attributes'),
            array('label' => esc_html__('Locations', 'listdom'), 'key' => 'locations'),
            array('label' => esc_html__('Tags', 'listdom'), 'key' => 'tags'),
            array('label' => esc_html__('Features', 'listdom'), 'key' => 'features'),
            array('label' => esc_html__('Labels', 'listdom'), 'key' => 'labels'),
            array('label' => esc_html__('Featured Image', 'listdom'), 'key' => 'image'),
            array('label' => esc_html__('Embed Codes', 'listdom'), 'key' => 'embed')
        );

        return apply_filters('lsd_dashboard_modules', $modules);
    }

    public function fields()
    {
        $fields = array(
            'title' => array('label' => esc_html__('Listing Title', 'listdom'), 'always_enabled' => true),
            'content' => array('label' => esc_html__('Listing Description', 'listdom')),
            'guest_email' => array('label' => esc_html__('Guest Email', 'listdom'), 'always_enabled' => true, 'guest' => true),
            'guest_password' => array('label' => esc_html__('Guest Password', 'listdom'), 'always_enabled' => true, 'guest' => true),
            'address' => array('label' => esc_html__('Address', 'listdom'), 'module' => 'address'),
            'remark' => array('label' => esc_html__('Remark', 'listdom'), 'module' => 'remark'),
            'price' => array('label' => esc_html__('Price', 'listdom'), 'module' => 'price'),
            'price_max' => array('label' => esc_html__('Price (Max)', 'listdom'), 'module' => 'price'),
            'price_after' => array('label' => esc_html__('Price Description', 'listdom'), 'module' => 'price'),
            'email' => array('label' => esc_html__('Email', 'listdom'), 'module' => 'contact'),
            'phone' => array('label' => esc_html__('Phone', 'listdom'), 'module' => 'contact'),
            'website' => array('label' => esc_html__('Website', 'listdom'), 'module' => 'contact'),
            'contact_address' => array('label' => esc_html__('Contact Address', 'listdom'), 'module' => 'contact'),
            '_gallery' => array('label' => esc_html__('Gallery', 'listdom'), 'module' => 'gallery', 'capability' => 'upload_files'),
            '_embeds' => array('label' => esc_html__('Embed', 'listdom'), 'module' => 'embed'),
            'listing_category' => array('label' => esc_html__('Category', 'listdom')),
            'tags' => array('label' => esc_html__('Tags', 'listdom'), 'module' => 'locations'),
            LSD_Base::TAX_LOCATION => array('label' => esc_html__('Locations', 'listdom'), 'module' => 'tags'),
            LSD_Base::TAX_FEATURE => array('label' => esc_html__('Features', 'listdom'), 'module' => 'features'),
            LSD_Base::TAX_LABEL => array('label' => esc_html__('Labels', 'listdom'), 'module' => 'labels'),
            'featured_image' => array('label' => esc_html__('Featured Image', 'listdom'), 'module' => 'image', 'capability' => 'upload_files'),
        );

        $SN = new LSD_Socials();
        $networks = LSD_Options::socials();

        foreach($networks as $network=>$values)
        {
            $obj = $SN->get($network, $values);
            $fields[$obj->key()] = array('label' => $obj->label(), 'module' => 'contact');
        }

        return apply_filters('lsd_dashboard_fields', $fields);
    }
}

endif;