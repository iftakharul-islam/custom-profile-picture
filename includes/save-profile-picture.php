<?php
if (!defined('ABSPATH')) {
    exit;
}

// Save the profile picture
add_action('personal_options_update', 'cpp_save_profile_picture');
add_action('edit_user_profile_update', 'cpp_save_profile_picture');

function cpp_save_profile_picture($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (isset($_FILES['cpp_profile_picture']) && !empty($_FILES['cpp_profile_picture']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Handle the file upload
        $uploaded_file = wp_handle_upload($_FILES['cpp_profile_picture'], array('test_form' => false));

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
                update_user_meta($user_id, 'cpp_profile_picture', esc_url($attachment_url));
            }
        }
    }
}
