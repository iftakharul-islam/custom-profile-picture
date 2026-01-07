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
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Avatar URL filter
        add_filter('get_avatar_url', array($this, 'custom_avatar_url'), 10, 2);
        
        // Avatar data filter
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
            echo '<p><a href="https://wordpress.org/support/plugin/custom-profile-picture/reviews/#new-post" class="button button-primary">' . esc_html__('Leave a Review', 'custom-profile-picture') . '</a></p>';
            echo '<p><a href="' . esc_url(add_query_arg('dismiss_review_notice', 'true')) . '" class="button button-secondary">' . esc_html__('Dismiss Notice', 'custom-profile-picture') . '</a></p>';
            echo '</div>';
        }

        // add a dismissible notice
        if (isset($_GET['dismiss_review_notice']) && $_GET['dismiss_review_notice'] == 'true') {
            update_option('custprofpic_review_asked', true);
        }

    }
    /**
     * Custom avatar URL handler
     */
    public function custom_avatar_url($url, $user_id) {
        $profile_picture = get_user_meta($user_id, 'custprofpic_profile_picture', true);
        return $profile_picture ? esc_url($profile_picture) : $url;
    }
    
    /**
     * Custom avatar data handler
     */
    public function custom_avatar_data($avatar_data, $args) {
        if (!empty($args->ID)) {
            $user_id = $args->ID;
            $profile_picture = get_user_meta($user_id, 'custprofpic_profile_picture', true);
            
            if ($profile_picture) {
                $avatar_data['url'] = esc_url($profile_picture);
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