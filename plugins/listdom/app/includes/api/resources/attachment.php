<?php
// no direct access
defined('ABSPATH') || die();

if(!class_exists('LSD_API_Resources_Attachment')):

/**
 * Listdom API Attachment Resource Class.
 *
 * @class LSD_API_Resources_Attachment
 * @version	1.0.0
 */
class LSD_API_Resources_Attachment extends LSD_API_Resource
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}

    public static function get($id)
    {
        return apply_filters('lsd_api_resource_attachment', array(
            'id' => $id,
            'url' => wp_get_attachment_url($id)
        ), $id);
	}

    public static function collection($ids)
    {
        $items = [];
        foreach($ids as $id) $items[] = self::get($id);

        return $items;
    }
}

endif;