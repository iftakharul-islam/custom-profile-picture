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