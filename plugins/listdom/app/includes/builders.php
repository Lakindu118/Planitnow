<?php
// no direct access
defined('ABSPATH') || die();

if(!class_exists('LSD_Builders')):

/**
 * Listdom Page Builders Class.
 *
 * @class LSD_Builders
 * @version	1.0.0
 */
class LSD_Builders extends LSD_Base
{
    /**
     * @var LSD_PTypes_Listing_Single
     */
    public $single;

    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}

    public function single($single)
    {
        $this->single = $single;
        return $this;
	}

    public function build($template_id)
    {
        $template = get_post($template_id);

        // Elementor
        if (
            class_exists('LSDADDELM_Base') &&
            class_exists('Elementor\Plugin') &&
            $template->post_type === LSDADDELM_Base::PTYPE_DETAILS
        ) return $this->elementor($template_id);

        // Anything
        return $this->content($template_id);
    }

    public function elementor($template_id)
    {
        // Payload
        LSD_Payload::set('single', $this->single);

        // Build Content
        return Elementor\Plugin::instance()
            ->frontend
            ->get_builder_content_for_display($template_id, true);
    }

    public function content($template_id)
    {
        // Template Content
        $content = get_the_content(null, false, $template_id);

        // Apply Filters
        $content = apply_filters('the_content', $content);
        return str_replace(']]>', ']]&gt;', $content);
    }
}

endif;