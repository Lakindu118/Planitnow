<?php
// no direct access
defined('ABSPATH') || die();

if(!class_exists('LSD_API_Resource')):

/**
 * Listdom API Resource Class.
 *
 * @class LSD_API_Resource
 * @version	1.0.0
 */
class LSD_API_Resource extends LSD_API
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
        return [];
	}

    public static function collection($ids)
    {
        $items = [];
        foreach($ids as $id) $items[] = self::get($id);

        return $items;
    }
}

endif;