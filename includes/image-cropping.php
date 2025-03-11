<?php
if (!defined('ABSPATH')) {
    exit;
}

// Add scripts and styles for image cropping
add_action('admin_enqueue_scripts', 'cpp_enqueue_cropping_scripts');

function cpp_enqueue_cropping_scripts($hook) {
    if ($hook == 'profile.php' || $hook == 'user-edit.php') {
        wp_enqueue_script('jquery');
        wp_enqueue_script('cpp-cropper', CPP_PLUGIN_URL . 'assets/js/cropper.min.js', array(), '1.5.12');
        wp_enqueue_style('cpp-cropper-style', CPP_PLUGIN_URL . 'assets/css/cropper.min.css', array(), '1.5.12');
        
        wp_enqueue_script('cpp-custom-cropping', CPP_PLUGIN_URL . 'assets/js/image-cropping.js', array('jquery', 'cpp-cropper'), '1.0.0');
        wp_enqueue_style('cpp-custom-style', CPP_PLUGIN_URL . 'assets/css/image-cropping.css', array(), '1.0.0');
    }
}

// Add cropping modal to profile page
add_action('admin_footer-profile.php', 'cpp_add_cropping_modal');
add_action('admin_footer-user-edit.php', 'cpp_add_cropping_modal');

function cpp_add_cropping_modal() {
    ?>
    <div id="cpp-cropping-modal" style="display: none;">
        <div class="cpp-modal-content">
            <div class="cpp-modal-header">
                <h3><?php esc_html_e('Crop Profile Picture', ''); ?></h3>
                <span class="cpp-close-modal">&times;</span>
            </div>
            <div class="cpp-modal-body">
                <div class="cpp-image-container">
                    <img id="cpp-crop-image" src="" alt="Crop Preview">
                </div>
                <div class="cpp-crop-controls">
                    <button type="button" class="button button-primary" id="cpp-crop-save">
                        <?php esc_html_e('Save Crop', ''); ?>
                    </button>
                    <button type="button" class="button" id="cpp-crop-cancel">
                        <?php esc_html_e('Cancel', ''); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Handle cropped image upload
add_action('wp_ajax_cpp_save_cropped_image', 'cpp_handle_cropped_image');

function cpp_handle_cropped_image() {
    if (!current_user_can('upload_files')) {
        wp_send_json_error('Permission denied');
    }

    if (!isset($_POST['image']) || !isset($_POST['user_id'])) {
        wp_send_json_error('Invalid request');
    }

    $image_data = $_POST['image'];
    $user_id = intval($_POST['user_id']);
    
    // Verify nonce and user permissions here
    
    // Remove header from base64 string
    $image_array = explode(';', $image_data);
    $image_array = explode(',', $image_array[1]);
    $image_data = base64_decode($image_array[1]);
    
    $upload_dir = wp_upload_dir();
    $filename = 'profile-picture-' . $user_id . '-' . time() . '.png';
    $upload_path = $upload_dir['path'] . '/' . $filename;
    
    file_put_contents($upload_path, $image_data);
    
    $attachment = array(
        'post_mime_type' => 'image/png',
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    
    $attachment_id = wp_insert_attachment($attachment, $upload_path);
    
    if (is_wp_error($attachment_id)) {
        wp_send_json_error('Failed to save image');
    }
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_path);
    wp_update_attachment_metadata($attachment_id, $attachment_data);
    
    $image_url = wp_get_attachment_url($attachment_id);
    update_user_meta($user_id, 'cpp_profile_picture', esc_url($image_url));
    
    wp_send_json_success(array('url' => $image_url));
}