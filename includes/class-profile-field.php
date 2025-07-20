<?php
namespace Ifatwp\CustomProfilePicture;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Profile Field Class
 */
class Profile_Field {
    
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
        add_action('show_user_profile', array($this, 'add_profile_picture_field'));
        add_action('edit_user_profile', array($this, 'add_profile_picture_field'));
    }
    
    /**
     * Add the profile picture field
     */
    public function add_profile_picture_field($user) {
        $profile_picture = get_user_meta($user->ID, 'custprofpic_profile_picture', true);
        ?>
        <h3><?php esc_html_e('Profile Picture', 'custom-profile-picture'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="custprofpic_profile_picture"><?php esc_html_e('Upload Profile Picture', 'custom-profile-picture'); ?></label></th>
                <td>
                    <div id="custprofpic-profile-picture-display">
                        <?php if ($profile_picture): ?>
                            <?php 
                            $attachment_id = attachment_url_to_postid($profile_picture);
                            if ($attachment_id) {
                                echo wp_get_attachment_image($attachment_id, array(100, 100), false, array(
                                    'style' => 'max-width:100px;max-height:100px;border-radius:50%;',
                                    'alt' => 'Profile Picture'
                                ));
                            } else {
                                // Create a placeholder image using WordPress functions
                                $image_data = array(
                                    'src' => esc_url($profile_picture),
                                    'width' => 100,
                                    'height' => 100,
                                    'alt' => 'Profile Picture'
                                );
                                echo '<div class="custprofpic-profile-image" style="width:100px;height:100px;background-image:url(' . esc_url($profile_picture) . ');background-size:cover;background-position:center;border-radius:50%;" title="Profile Picture"></div>';
                            }
                            ?>
                        <?php endif; ?>
                    </div>
                    <br>
                    <input type="file" name="custprofpic_profile_picture" id="custprofpic_profile_picture" />
                    <br>
                    <span class="description"><?php esc_html_e('Upload a custom profile picture. Recommended size: 150x150px.', 'custom-profile-picture'); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }
} 