<?php
// no direct access
defined('ABSPATH') || die();

if(!class_exists('LSD_API_Controllers_Register')):

/**
 * Listdom API Register Controller Class.
 *
 * @class LSD_API_Controllers_Register
 * @version	1.0.0
 */
class LSD_API_Controllers_Register extends LSD_API_Controller
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}

    public function perform(WP_REST_Request $request)
    {
        $vars = $request->get_params();

        $name = isset($vars['name']) ? base64_decode($vars['name']) : null;
        $email = isset($vars['email']) ? base64_decode($vars['email']) : null;
        $password = isset($vars['password']) ? base64_decode($vars['password']) : null;

        // Required Data
        if(trim($email) == '' or trim($password) == '') return new WP_Error('email_password_required', esc_html__('Email and Password are required!', 'listdom'));

        // Invalid Email
        if(!is_email($email)) return new WP_Error('invalid_email', esc_html__('Email is not valid!', 'listdom'));

        // Password is too Short
        if(strlen($password) < 6) return new WP_Error('short_password', esc_html__('Password should be at-least 6 characters!', 'listdom'));

        // Registration
        $response = wp_insert_user(array(
            'display_name' => $name,
            'user_login' => sanitize_user($email),
            'user_email '=> sanitize_email($email),
            'user_pass' => $password
        ));

        // Invalid Credentials
        if(is_wp_error($response)) return $response;

        // Trigger Action
        do_action('lsd_api_user_registered', $response, $request);

        // Response
        return $this->response(array(
            'data' => array(
                'success' => 1,
                'id' => $response,
                'token' => $this->getUserToken($response),
            ),
            'status' => 200
        ));
	}
}

endif;