<?php
if (!defined('ABSPATH')) {
    exit;
}

// Add the profile picture field
add_action('show_user_profile', 'cpp_add_profile_picture_field');
add_action('edit_user_profile', 'cpp_add_profile_picture_field');

function cpp_add_profile_picture_field($user) {
    $profile_picture = get_user_meta($user->ID, 'cpp_profile_picture', true);
    ?>
    <h3><?php esc_html_e('Profile Picture', 'custom-profile-profile'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="cpp_profile_picture"><?php esc_html_e('Upload Profile Picture', 'custom-profile-picture'); ?></label></th>
            <td>
                <?php if ($profile_picture): ?>
                    <?php 
                    $attachment_id = attachment_url_to_postid($profile_picture);
                    if ($attachment_id) {
                        echo wp_get_attachment_image($attachment_id, array(100, 100), false, array(
                            'style' => 'max-width:100px;max-height:100px;border-radius:50%;',
                            'alt' => 'Profile Picture'
                        ));
                    } else {
                        echo '<img src="' . esc_url($profile_picture) . '" style="max-width:100px;max-height:100px;border-radius:50%;" alt="Profile Picture" />';
                    }
                    ?>
                <?php endif; ?>
                <br>
                <input type="file" name="cpp_profile_picture" id="cpp_profile_picture" />
                <br>
                <span class="description"><?php esc_html_e('Upload a custom profile picture. Recommended size: 150x150px.', 'custom-profile-picture') ?></span>
            </td>
        </tr>
    </table>
    <?php
}
