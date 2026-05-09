<?php
namespace Ifatwp\CustomProfilePicture;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Plugin Class
 */
class Plugin {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Components
     */
    private $form_handler;
    private $profile_field;
    private $save_profile_picture;
    private $avatar_replacement;
    private $image_cropping;
    private $admin_page;
    private $frontend_profile;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_components();
        $this->init_hooks();
    }
    
    /**
     * Initialize plugin components
     */
    private function init_components() {
        $this->form_handler = new Form_Handler();
        $this->profile_field = new Profile_Field();
        $this->save_profile_picture = new Save_Profile_Picture();
        $this->avatar_replacement = new Avatar_Replacement();
        $this->image_cropping = new Image_Cropping();
        $this->admin_page = new Admin_Page();
        $this->frontend_profile = new Frontend_Profile();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Avatar URL filter — accepts 3 params; second is $id_or_email (int/string/object)
        add_filter('get_avatar_url', array($this, 'custom_avatar_url'), 10, 3);

        // Avatar data filter — second param is $id_or_email, not a WP_User object
        add_filter('pre_get_avatar_data', array($this, 'custom_avatar_data'), 10, 2);

        // set option for installed date
        if (!get_option('custprofpic_installed_date')) {
            update_option('custprofpic_installed_date', current_time('mysql'));
        }

        // ask for reviews if user used it for more than 30 days
        $installed_date = get_option('custprofpic_installed_date');
        if ($installed_date) {
            $installed_timestamp = strtotime($installed_date);
            $current_timestamp = current_time('timestamp');
            $days_since_install = ($current_timestamp - $installed_timestamp) / (60 * 60 * 24);
            
            if ($days_since_install > 30 && !get_option('custprofpic_review_asked')) {
                add_action('admin_notices', array($this, 'review_notice'));
            }
        }
       
        add_action('admin_notices', array($this, 'review_notice'));


    }

    /**
     * Display review notice
     */
    public function review_notice() {
        if (current_user_can('manage_options') && !get_option('custprofpic_review_asked')) {
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p>' . esc_html__('If you enjoy using Custom Profile Picture, please consider leaving a review!', 'custom-profile-picture') . '</p>';
            echo "<div class='review-buttons' style='margin-top:10px; display:flex; gap:10px;'>";
            echo '<p><a href="https://wordpress.org/support/plugin/custom-profile-picture/reviews/#new-post" class="button button-primary">' . esc_html__('Leave a Review', 'custom-profile-picture') . '</a></p>';
            echo '<p><a href="' . esc_url(add_query_arg('dismiss_review_notice', 'true')) . '" class="button button-secondary">' . esc_html__('Dismiss Notice', 'custom-profile-picture') . '</a></p>';
            echo "</div>";
            echo '</div>';
        }

        // add a dismissible notice
        if (isset($_GET['dismiss_review_notice']) && $_GET['dismiss_review_notice'] == 'true') {
            update_option('custprofpic_review_asked', true);
        }

    }
    /**
     * Custom avatar URL handler.
     *
     * Resolves $id_or_email (int ID, email string, or WP_Comment object) to a
     * WP_User so get_user_meta() is always called with a valid integer user ID.
     *
     * @param string     $url         The avatar URL.
     * @param mixed      $id_or_email User ID, email address, or WP_Comment object.
     * @param array      $args        Arguments passed to get_avatar_data().
     * @return string
     */
    public function custom_avatar_url($url, $id_or_email, $args) {
        $user = false;

        if (is_numeric($id_or_email)) {
            $user = get_userdata((int) $id_or_email);
        } elseif (is_string($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
        } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
            $user = get_userdata((int) $id_or_email->user_id);
        }

        if ($user) {
            $profile_picture = get_user_meta($user->ID, 'custprofpic_profile_picture', true);
            if ($profile_picture) {
                return esc_url($profile_picture);
            }
        }

        return $url;
    }
    
    /**
     * Custom avatar data handler.
     *
     * Resolves $id_or_email to a WP_User and injects the custom picture URL
     * before WordPress queries Gravatar.
     *
     * @param array $avatar_data Avatar data array.
     * @param mixed $id_or_email User ID, email address, or WP_Comment object.
     * @return array
     */
    public function custom_avatar_data($avatar_data, $id_or_email) {
        $user = false;

        if (is_numeric($id_or_email)) {
            $user = get_userdata((int) $id_or_email);
        } elseif (is_string($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
        } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
            $user = get_userdata((int) $id_or_email->user_id);
        }

        if ($user) {
            $profile_picture = get_user_meta($user->ID, 'custprofpic_profile_picture', true);
            if ($profile_picture) {
                $avatar_data['url']   = esc_url($profile_picture);
                $avatar_data['found'] = true;
            }
        }

        return $avatar_data;
    }
    
    /**
     * Get plugin instance (singleton pattern)
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}