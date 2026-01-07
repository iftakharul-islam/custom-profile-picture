<?php
namespace Ifatwp\CustomProfilePicture;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save Profile Picture Class
 */
class Save_Profile_Picture {
    
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
        add_action('personal_options_update', array($this, 'save_profile_picture'));
        add_action('edit_user_profile_update', array($this, 'save_profile_picture'));
    }
    
    /**
     * Save the profile picture
     */
    public function save_profile_picture($user_id) {
        // Verify nonce for security
        $nonce = sanitize_text_field(wp_unslash($_POST['_wpnonce'] ?? ''));
        if (!wp_verify_nonce($nonce, 'update-user_' . $user_id)) {
            return false;
        }

        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        // Check if user selected from media library
        if (isset($_POST['custprofpic_attachment_id']) && !empty($_POST['custprofpic_attachment_id'])) {
            $attachment_id = absint($_POST['custprofpic_attachment_id']);

            // Verify the attachment exists and is an image
            if (wp_attachment_is_image($attachment_id)) {
                $attachment_url = wp_get_attachment_url($attachment_id);

                if ($attachment_url) {
                    update_user_meta($user_id, 'custprofpic_profile_picture', esc_url($attachment_url));
                    update_user_meta($user_id, 'custprofpic_attachment_id', $attachment_id);
                }
            }
        }
        // Otherwise, check if user uploaded a new file
        elseif (isset($_FILES['custprofpic_profile_picture']) && !empty($_FILES['custprofpic_profile_picture']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Handle the file upload
            $uploaded_file = wp_handle_upload($_FILES['custprofpic_profile_picture'], array('test_form' => false));

            if (isset($uploaded_file['error'])) {
                return false;
            }

            if (isset($uploaded_file['file'])) {
                $attachment_id = wp_insert_attachment(array(
                    'post_mime_type' => $uploaded_file['type'],
                    'post_title'     => sanitize_file_name($uploaded_file['file']),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ), $uploaded_file['file']);

                if (is_wp_error($attachment_id)) {
                    return false;
                }

                $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);

                $attachment_url = wp_get_attachment_url($attachment_id);
                if ($attachment_url) {
                    update_user_meta($user_id, 'custprofpic_profile_picture', esc_url($attachment_url));
                    update_user_meta($user_id, 'custprofpic_attachment_id', $attachment_id);
                }
            }
        }
    }
}  