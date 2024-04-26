<?php
// no direct access
defined('ABSPATH') || die();

if(!class_exists('LSD_API_Resources_User')):

/**
 * Listdom API User Resource Class.
 *
 * @class LSD_API_Resources_User
 * @version	1.0.0
 */
class LSD_API_Resources_User extends LSD_API_Resource
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}

    public static function get($user)
    {
        // Get User by ID
        if(is_numeric($user)) $user = get_user_by('id', $user);

        // Resource
        $resource = new LSD_API_Resource();

        // Meta Values
        $metas = $resource->get_user_meta($user->ID);

        return apply_filters('lsd_api_resource_user', array(
            'id' => $user->ID,
            'data' => array(
                'ID' => $user->ID,
                'username' => $user->data->user_login,
                'email' => $user->data->user_email,
                'registered_at' => $user->data->user_registered,
                'display_name' => $user->data->display_name,
                'first_name' => (isset($metas['first_name']) ? $metas['first_name'] : null),
                'last_name' => (isset($metas['last_name']) ? $metas['last_name'] : null),
                'description' => (isset($metas['description']) ? $metas['description'] : null),
                'phone' => (isset($metas['lsd_phone']) ? $metas['lsd_phone'] : null),
                'mobile' => (isset($metas['lsd_mobile']) ? $metas['lsd_mobile'] : null),
                'fax' => (isset($metas['lsd_fax']) ? $metas['lsd_fax'] : null),
                'job_title' => (isset($metas['lsd_job_title']) ? $metas['lsd_job_title'] : null),
                'linkedin' => (isset($metas['lsd_linkedin']) ? $metas['lsd_linkedin'] : null),
                'twitter' => (isset($metas['lsd_twitter']) ? $metas['lsd_twitter'] : null),
                'facebook' => (isset($metas['lsd_facebook']) ? $metas['lsd_facebook'] : null),
                'pinterest' => (isset($metas['lsd_pinterest']) ? $metas['lsd_pinterest'] : null),
            ),
            'media' => array(
                'avatar' => get_avatar_url($user->ID),
            ),
            'roles' => $user->roles,
            'capabilities' => $user->allcaps,
        ), $user);
	}

    public static function minify($user)
    {
        // Get Full Data
        $data = self::get($user);

        // Minify it
        unset($data['roles']);
        unset($data['capabilities']);

        // Return Minified Data
        return $data;
    }
}

endif;