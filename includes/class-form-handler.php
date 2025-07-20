<?php
namespace Ifatwp\CustomProfilePicture;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Form Handler Class
 */
class Form_Handler {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('user_edit_form_tag', array($this, 'add_enctype_to_form'));
        add_action('show_user_profile', array($this, 'add_enctype_to_form'));
    }
    
    /**
     * Add enctype to user profile form
     */
    public function add_enctype_to_form() {
        static $custprofpic_enc_status = false;
        
        if (!$custprofpic_enc_status) {
            $custprofpic_enc_status = true;
            echo ' enctype="multipart/form-data"';
        }
    }
} 