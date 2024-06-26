<?php
// no direct access
defined('ABSPATH') || die();

if(!class_exists('LSD_Shortcodes_Dashboard')):

/**
 * Listdom Dashboard Shortcode Class.
 *
 * @class LSD_Shortcodes_Dashboard
 * @version	1.0.0
 */
class LSD_Shortcodes_Dashboard extends LSD_Shortcodes
{
    public $atts = [];
    public $page;
    public $url;
    public $mode;
    public $alert;
    public $settings;
    public $guest_status;
    public $guest_registration;
    public $listings;
    public $post;
    public $limit;

    /**
     * @var WP_Query
     */
    public $q;

    /**
	 * Constructor method
	 */
	public function __construct()
    {
        parent::__construct();
	}

    public function init()
    {
        // WP Libraries
        if(!function_exists('wp_terms_checklist')) include ABSPATH . 'wp-admin/includes/template.php';

        // Settings
        $this->settings = LSD_Options::settings();

        // Shortcode
        add_shortcode('listdom-dashboard', [$this, 'output']);

        // Save Listing
        add_action('wp_ajax_lsd_dashboard_listing_save', [$this, 'save']);
        add_action('wp_ajax_nopriv_lsd_dashboard_listing_save', [$this, 'save']);

        // Upload Featured Image
        add_action('wp_ajax_lsd_dashboard_listing_upload_featured_image', [$this, 'upload']);
        add_action('wp_ajax_nopriv_lsd_dashboard_listing_upload_featured_image', [$this, 'upload']);

        // Delete Listing
        add_action('wp_ajax_lsd_dashboard_listing_delete', [$this, 'delete']);
        add_action('wp_ajax_nopriv_lsd_dashboard_listing_delete', [$this, 'delete']);

        // Upload Gallery
        add_action('wp_ajax_lsd_dashboard_listing_upload_gallery', [$this, 'gallery']);
        add_action('wp_ajax_nopriv_lsd_dashboard_listing_upload_gallery', [$this, 'gallery']);
	}

    public function output($atts = [])
    {
        if($this->isLite())
        {
            return $this->alert($this->missFeatureMessage(esc_html__('Dashboard', 'listdom')), 'error');
        }

        // Listdom Pre Shortcode
        $pre = apply_filters('lsd_pre_shortcode', '', $atts, 'listdom-dashboard');
        if(trim($pre)) return $pre;

        // Include WordPress Media
        LSD_Assets::media();

        // Shortcode attributes
        $this->atts = is_array($atts) ? $atts : [];

        // Dashboard Page
        global $post;
        $this->page = $post;

        // Dashboard URL
        $this->url = get_permalink($this->page);

        // Mode
        $this->mode = isset($_GET['mode']) ? sanitize_text_field($_GET['mode']) : 'manage';

        // Guest Status
        $this->guest_status = isset($this->settings['submission_guest']) && $this->settings['submission_guest'];

        // Registration Method
        $this->guest_registration = $this->settings['submission_guest_registration'] ?? 'approval';

        // Payload
        LSD_Payload::set('dashboard', $this);

        // Dashboard
        if($this->mode == 'manage') return $this->manage();
        // Form
        elseif($this->mode == 'form') return $this->form();
        // Other Modes
        else return apply_filters('lsd_dashboard_modes', $this->alert(esc_html__('Not found!', 'listdom'), 'error'), $this);
	}

    public function manage()
    {
        if(!get_current_user_id() && !$this->guest_status)
        {
            return $this->alert(sprintf(esc_html__("Unfortunately you don't have permission to view this page. Please %s first.", 'listdom'), '<a href="'.wp_login_url($this->current_url()).'">'.esc_html__('login', 'listdom').'</a>'), 'error');
        }

        // Listing Per Page
        $this->limit = 20;

        // Current Page
        $paged = max(1, get_query_var('paged'));

        // Get Listings
        $query = [
            'post_type' => LSD_Base::PTYPE_LISTING,
            'posts_per_page' => $this->limit,
            'post_status' => ['publish', 'pending', 'draft', 'trash', LSD_Base::STATUS_HOLD, LSD_Base::STATUS_EXPIRED],
            'paged' => $paged,
        ];

        // Filter by Author
        if(!current_user_can('edit_others_posts')) $query['author'] = get_current_user_id();

        // Apply Filters
        $query = apply_filters('lsd_dashboard_manage_query', $query);

        // Query
        $this->q = new WP_Query($query);

        // Search Listings
        if(get_current_user_id() && $this->q->have_posts())
        {
            while($this->q->have_posts())
            {
                $this->q->the_post();
                $this->listings[] = get_post();
            }

            wp_reset_postdata();
        }
        else $this->listings = [];

        // Dashboard
        ob_start();
        include lsd_template('dashboard/manage.php');
        return ob_get_clean();
	}

    public function item($listing)
    {
        include lsd_template('dashboard/item.php');
	}

    public function form()
    {
        if(!current_user_can('edit_posts') && !$this->guest_status)
        {
            return $this->alert(esc_html__("Unfortunately you don't have permission to create or edit listings.", 'listdom'), 'error');
        }

        $id = isset($_GET['id']) ? (int) sanitize_text_field($_GET['id']) : 0;

        // Selected post is not a listing
        if($id > 0 and get_post_type($id) != LSD_Base::PTYPE_LISTING)
        {
            return $this->alert(esc_html__("Sorry! Selected post is not a listing.", 'listdom'), 'error');
        }

        // Show a warning to current user if modification of post is not possible for him/her
        if($id > 0 and !current_user_can('edit_post', $id))
        {
            return $this->alert(esc_html__("Sorry! You don't have access to modify this listing.", 'listdom'), 'error');
        }

        // Get Post Data
        $this->post = get_post($id);

        if($id <= 0)
        {
            $this->post = new stdClass();
            $this->post->ID = 0;
        }

        // Dashboard
        ob_start();
        include lsd_template('dashboard/form.php');
        return ob_get_clean();
    }

    public function is_enabled($module)
    {
        $enabled = true;

        // Module is disabled
        if(isset($this->settings['submission_module']) and isset($this->settings['submission_module'][$module]) and !$this->settings['submission_module'][$module]) $enabled = false;

        // Module is enabled only for admin and editor
        if(isset($this->settings['submission_module']) and isset($this->settings['submission_module'][$module]) and $this->settings['submission_module'][$module] == 2 and !current_user_can('edit_others_pages')) $enabled = false;

        // Apply Filters
        return apply_filters('lsd_dashboard_modules_status', $enabled, $module);
    }

    public function is_required($field)
    {
        $required = false;

        // Field is required
        if(isset($this->settings['submission_fields']) and isset($this->settings['submission_fields'][$field]) and isset($this->settings['submission_fields'][$field]['required']) and $this->settings['submission_fields'][$field]['required']) $required = true;

        // Apply Filters
        return apply_filters('lsd_dashboard_field_required', $required, $field);
    }

    public function required_html($field)
    {
        echo ($this->is_required($field) ? ' '.LSD_Base::REQ_HTML : '');
    }

    public function menus()
    {
        // Default Menus
        $menus = [
            'manage' => ['label' => esc_html__('Dashboard', 'listdom'), 'id' => 'lsd_dashboard_menus_manage', 'url' => $this->url, 'icon' => 'fas fa-tachometer-alt'],
        ];

        // Add Listing Menu
        if(current_user_can('edit_posts') or $this->guest_status) $menus['form'] = ['label' => esc_html__('Add Listing', 'listdom'), 'id' => 'lsd_dashboard_menus_form', 'url' => $this->add_qs_var('mode', 'form', $this->url), 'icon' => 'far fa-plus-square'];

        // Logout Menu
        if(get_current_user_id()) $menus['logout'] = ['label' => esc_html__('Logout', 'listdom'), 'id' => 'lsd_dashboard_menus_logout', 'url' => wp_logout_url(), 'icon' => 'fas fa-sign-out-alt'];

        // Apply Filters
        $menus = apply_filters('lsd_dashboard_menus', $menus, $this);

        // Current Page
        $current = isset($_GET['mode']) ? sanitize_text_field($_GET['mode']) : 'manage';

        $output = '<ul class="lsd-dashboard-menus">';
        foreach($menus as $key => $menu)
        {
            $target = $menu['target'] ?? '_self';
            $icon = $menu['icon'] ?? 'fas fa-tachometer-alt';
            $id = $menu['id'] ?? 'lsd_dashboard_menus_' . $key;

            $output .= '<li id="'.esc_attr($id).'" '.($current == $key ? 'class="lsd-active"' : '').'><i class="lsd-icon '.esc_attr($icon).'"></i><a href="'.esc_url($menu['url']).'" target="'.esc_attr($target).'">'.esc_html($menu['label']).'</a></li>';
        }

        $output .= '</ul>';
        return $output;
    }

	protected function get_form_link($listing_id = null)
    {
        $url = $this->add_qs_var('mode', 'form', $this->url);

        // Edit Mode
        if($listing_id) $url = $this->add_qs_var('id', $listing_id, $url);

        return $url;
    }

    public function save()
    {
        // Nonce is not set!
        if(!isset($_POST['_wpnonce'])) $this->response(['success' => 0, 'message' => esc_html__('Security nonce is missing!', 'listdom')]);

        // Nonce is not valid!
        if(!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'lsd_dashboard')) $this->response(['success' => 0, 'message' => esc_html__('Security nonce is not valid!', 'listdom')]);

        $g_recaptcha_response = isset($_POST['g-recaptcha-response']) ? sanitize_text_field($_POST['g-recaptcha-response']) : null;
        if(!LSD_Main::grecaptcha_check($g_recaptcha_response)) $this->response(['success' => 0, 'message' => esc_html__("Google recaptcha is invalid.", 'listdom')]);

        // Registration Method
        $guest_registration = $this->settings['submission_guest_registration'] ?? 'approval';

        // Additional Validations
        $valid = apply_filters('lsd_dashboard_validate_request', true);
        if($valid !== true) $this->response(['success' => 0, 'message' => $valid]);

        $id = isset($_POST['id']) ? (int) sanitize_text_field($_POST['id']) : 0;
        $lsd = $_POST['lsd'] ?? [];
        $social = $lsd['sc'] ?? []; // Social
        $tax = isset($_POST['tax_input']) && is_array($_POST['tax_input']) ? $_POST['tax_input'] : [];

        $post_title = isset($lsd['title']) ? sanitize_text_field($lsd['title']) : '';
        $post_content = $lsd['content'] ?? '';

        if(!trim($post_title)) $this->response(['success' => 0, 'message' => esc_html__('Please fill listing title field!', 'listdom')]);

        $guest_email = $lsd['guest_email'] ?? '';
        if(!get_current_user_id() && !trim($guest_email)) $this->response(['success' => 0, 'message' => esc_html__('Please insert your email!', 'listdom')]);

        // Password Validation
        $guest_password = $lsd['guest_password'] ?? '';
        if(!get_current_user_id() && $guest_registration === 'submission' && strlen($guest_password) < 8) $this->response(['success' => 0, 'message' => esc_html__('Please insert your password! It should be at-least 8 characters.', 'listdom')]);

        // Required Fields
        $fields = (new LSD_Dashboard())->fields();

        // Validate
        $errors = [];
        foreach($fields as $f => $field)
        {
            // Not Required
            if(!$this->is_required($f)) continue;

            // Field module
            $module = $field['module'] ?? null;

            // Related module is not enabled
            if($module && !$this->is_enabled($module)) continue;

            // Needed Capability
            $capability = $field['capability'] ?? null;

            // Current user is not permitted
            if($capability && !current_user_can($capability)) continue;

            $value = null;
            if(isset($lsd[$f])) $value = $lsd[$f];
            elseif(isset($tax[$f])) $value = $tax[$f];
            elseif(isset($social[$f])) $value = $social[$f];
            elseif(isset($_POST[$f])) $value = $_POST[$f];

            if(is_array($value) and !count($value)) $errors[] = sprintf(esc_html__('At-least one value for %s field is required!', 'listdom'), strtolower($field['label']));
            elseif(trim($value) === '') $errors[] = sprintf(esc_html__('%s field is required!', 'listdom'), $field['label']);
        }

        // There are some Errors
        if(count($errors)) $this->response(['success' => 0, 'message' => '<ul><li>'.implode('</li><li>', $errors).'</li></ul>']);

        // Trigger Event
        do_action('lsd_dashboard_validation', $lsd, $id);

        // Post Status
        $status = 'pending';
        if(current_user_can('publish_posts')) $status = 'publish';

        // Filter Listing Status
        $status = apply_filters('lsd_default_listing_status', $status, $lsd);
        $status = apply_filters('lsd_dashboard_listing_status', $status, $lsd);

        // Create New Listing
        if($id <= 0)
        {
            $post = ['post_title'=>$post_title, 'post_content'=>$post_content, 'post_type'=>LSD_Base::PTYPE_LISTING, 'post_status'=>$status];
            $id = wp_insert_post($post);
        }

        wp_update_post(['ID'=>$id, 'post_title'=>$post_title, 'post_content'=>$post_content]);

        // Tags
        if($this->is_enabled('tags'))
        {
            $tags = isset($_POST['tags']) ? sanitize_text_field($_POST['tags']) : '';
            wp_set_post_terms($id, $tags, LSD_Base::TAX_TAG);
        }

        // Locations
        if($this->is_enabled('locations'))
        {
            $locations = isset($tax[LSD_Base::TAX_LOCATION]) && is_array($tax[LSD_Base::TAX_LOCATION]) ? $tax[LSD_Base::TAX_LOCATION] : [];
            wp_set_post_terms($id, $locations, LSD_Base::TAX_LOCATION);
        }

        // Features
        if($this->is_enabled('features'))
        {
            $features = isset($tax[LSD_Base::TAX_FEATURE]) && is_array($tax[LSD_Base::TAX_FEATURE]) ? $tax[LSD_Base::TAX_FEATURE] : [];
            wp_set_post_terms($id, LSD_Taxonomies::name($features, LSD_Base::TAX_FEATURE), LSD_Base::TAX_FEATURE);
        }

        // Labels
        if($this->is_enabled('labels'))
        {
            $labels = isset($tax[LSD_Base::TAX_LABEL]) && is_array($tax[LSD_Base::TAX_LABEL]) ? $tax[LSD_Base::TAX_LABEL] : [];
            wp_set_post_terms($id, LSD_Taxonomies::name($labels, LSD_Base::TAX_LABEL), LSD_Base::TAX_LABEL);
        }

        // Featured Image
        if($this->is_enabled('image'))
        {
            $featured_image = isset($lsd['featured_image']) ? sanitize_text_field($lsd['featured_image']) : '';

            // Save Featured Image
            if($featured_image) set_post_thumbnail($id, $featured_image);
            // Delete Featured Image
            elseif(has_post_thumbnail($id)) delete_post_thumbnail($id);
        }

        // Gallery
        if(isset($lsd['_gallery']) && is_array($lsd['_gallery']))
        {
            $lsd['gallery'] = $lsd['_gallery'];
            unset($lsd['_gallery']);
        }

        // Embeds
        if(isset($lsd['_embeds']) && is_array($lsd['_embeds']))
        {
            $lsd['embeds'] = $lsd['_embeds'];
            unset($lsd['_embeds']);
        }

        // Publish Listing
        if($status == 'publish' && get_post_status($id) != 'published') wp_publish_post($id);

        // Sanitization
        array_walk_recursive($lsd, 'sanitize_text_field');

        // Save the Data
        $entity = new LSD_Entity_Listing($id);
        $entity->save($lsd, true);

        if($status == 'publish') $message = sprintf(esc_html__('The listing has been published. %s', 'listdom'), '<a href="'.get_permalink($id).'" target="_blank">'.esc_html__('View Listing', 'listdom').'</a>');
        else $message = esc_html__('The listing has been submitted. It will be reviewed as soon as possible.', 'listdom');

        // Trigger Event
        do_action('lsd_dashboard_save', $id, $lsd);

        // Response
        $this->response(['success' => 1, 'message' => $message, 'data' => ['id' => $id]]);
    }

    public function upload()
    {
        // User is not allowed to upload files
        if(!current_user_can('upload_files')) $this->response(['success' => 0, 'message' => esc_html__('You are not allowed to upload files!', 'listdom')]);

        // Nonce is not set!
        if(!isset($_POST['_wpnonce'])) $this->response(['success' => 0, 'message' => esc_html__('Security nonce is missing!', 'listdom')]);

        // Nonce is not valid!
        if(!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'lsd_dashboard')) $this->response(['success' => 0, 'message' => esc_html__('Security nonce is not valid!', 'listdom')]);

        // Include the function
        if(!function_exists('wp_handle_upload')) require_once ABSPATH.'wp-admin/includes/file.php';

        $image = $_FILES['file'] ?? null;

        // No file
        if(!$image) $this->response(['success' => 0, 'message' => esc_html__('Please upload an image!', 'listdom')]);

        $allowed = ['jpeg', 'jpg', 'png', 'webp'];

        $ex = explode('.', $image['name']);
        $extension = end($ex);

        // Invalid Extension
        if(!in_array(strtolower($extension), $allowed)) $this->response(['success' => 0, 'message' => esc_html__('Only JPG and PNG images are allowed!', 'listdom')]);

        $uploaded = wp_handle_upload($image, ['test_form' => false]);

        $success = 0;
        $data = [];

        if($uploaded and !isset($uploaded['error']))
        {
            $success = 1;
            $message = esc_html__('The image is uploaded!', 'listdom');

            $attachment = [
                'post_mime_type' => $uploaded['type'],
                'post_title' => '',
                'post_content' => '',
                'post_status' => 'inherit'
            ];

            // Add as Attachment
            $attachment_id = wp_insert_attachment($attachment, $uploaded['file']);

            // Update Metadata
            require_once ABSPATH.'wp-admin/includes/image.php';
            wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $uploaded['file']));

            $data['attachment_id'] = $attachment_id;
            $data['url'] = $uploaded['url'];
        }
        else
        {
            $message = $uploaded['error'];
        }

        $this->response(['success'=>$success, 'message'=>$message, 'data'=>$data]);
    }

    public function delete()
    {
        // Nonce is not set!
        if(!isset($_POST['_lsdnonce'])) $this->response(['success' => 0, 'message' => esc_html__('Security nonce is missing!', 'listdom')]);

        // Nonce is not valid!
        if(!wp_verify_nonce(sanitize_text_field($_POST['_lsdnonce']), 'lsd_dashboard')) $this->response(['success' => 0, 'message' => esc_html__('Security nonce is not valid!', 'listdom')]);

        $id = isset($_POST['id']) ? (int) sanitize_text_field($_POST['id']) : 0;
        $listing = get_post($id);

        // Listing not Found!
        if(!isset($listing->ID)) $this->response(['success' => 0]);

        // Current User Cannot Remove Listing of Others
        if($listing->post_author != get_current_user_id() and !current_user_can('delete_others_posts')) $this->response(['success' => 0]);

        // Delete The Post
        wp_delete_post($id);

        // Response
        $this->response(['success' => 1]);
    }

    public function gallery()
    {
        // User is not allowed to upload files
        if(!current_user_can('upload_files')) $this->response(['success' => 0, 'message' => esc_html__('You are not allowed to upload files!', 'listdom')]);

        // Nonce is not set!
        if(!isset($_POST['_wpnonce'])) $this->response(['success' => 0, 'message' => esc_html__('Security nonce is missing!', 'listdom')]);

        // Nonce is not valid!
        if(!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'lsd_dashboard')) $this->response(['success' => 0, 'message' => esc_html__('Security nonce is not valid!', 'listdom')]);

        // Include the function
        if(!function_exists('wp_handle_upload')) require_once ABSPATH.'wp-admin/includes/file.php';

        $images = (isset($_FILES['files']) and is_array($_FILES['files'])) ? $_FILES['files'] : [];

        // No images
        if(!count($images)) $this->response(['success' => 0, 'message' => esc_html__('Please upload an image!', 'listdom')]);

        // Allowed Extensions
        $allowed = ['jpeg', 'jpg', 'png', 'webp'];

        $success = 0;
        $data = [];

        $count = count($images['name']);
        for($i = 0; $i < $count; $i++)
        {
            $image = [
                'name' => $images['name'][$i],
                'type' => $images['type'][$i],
                'tmp_name' => $images['tmp_name'][$i],
                'error' => $images['error'][$i],
                'size' => $images['size'][$i],
            ];

            $ex = explode('.', $image['name']);
            $extension = end($ex);

            // Invalid Extension
            if(!in_array(strtolower($extension), $allowed)) continue;

            $uploaded = wp_handle_upload($image, ['test_form' => false]);

            if($uploaded and !isset($uploaded['error']))
            {
                $success = 1;
                $attachment = [
                    'post_mime_type' => $uploaded['type'],
                    'post_title' => '',
                    'post_content' => '',
                    'post_status' => 'inherit'
                ];

                // Add as Attachment
                $attachment_id = wp_insert_attachment($attachment, $uploaded['file']);

                // Update Metadata
                require_once ABSPATH.'wp-admin/includes/image.php';
                wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $uploaded['file']));

                $data[] = [
                    'id' => $attachment_id,
                    'url' => $uploaded['url']
                ];
            }
        }

        $message = $success ? esc_html__('The images are uploaded!', 'listdom') : esc_html__('An error occurred!', 'listdom');
        $this->response(['success' => $success, 'message' => $message, 'data' => $data]);
    }
}

endif;