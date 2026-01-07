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
        add_action('admin_enqueue_scripts', array($this, 'enqueue_media_uploader'));
    }

    /**
     * Enqueue media uploader scripts
     */
    public function enqueue_media_uploader($hook) {
        // Only load on user profile pages
        if ($hook !== 'profile.php' && $hook !== 'user-edit.php') {
            return;
        }

        // Enqueue WordPress media uploader
        wp_enqueue_media();

        // Enqueue our custom script
        wp_enqueue_script(
            'custprofpic-media-uploader',
            CUSTPROFPIC_PLUGIN_URL . 'assets/js/media-uploader.js',
            array('jquery'),
            CUSTPROFPIC_PLUGIN_VERSION,
            true
        );
    }
    
    /**
     * Add the profile picture field
     */
    public function add_profile_picture_field($user) {
        $profile_picture = get_user_meta($user->ID, 'custprofpic_profile_picture', true);
        $attachment_id = get_user_meta($user->ID, 'custprofpic_attachment_id', true);
        ?>
        <h3><?php esc_html_e('Profile Picture', 'custom-profile-picture'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="custprofpic_profile_picture"><?php esc_html_e('Upload Profile Picture', 'custom-profile-picture'); ?></label></th>
                <td>
                    <div id="custprofpic-profile-picture-display">
                        <?php if ($profile_picture): ?>
                            <?php
                            $current_attachment_id = $attachment_id ? $attachment_id : attachment_url_to_postid($profile_picture);
                            if ($current_attachment_id) {
                                echo wp_get_attachment_image($current_attachment_id, array(100, 100), false, array(
                                    'style' => 'max-width:100px;max-height:100px;border-radius:50%;',
                                    'alt' => 'Profile Picture'
                                ));
                            } else {
                                // Display profile picture as background image
                                echo '<div class="custprofpic-profile-image" style="width:100px;height:100px;background-image:url(' . esc_url($profile_picture) . ');background-size:cover;background-position:center;border-radius:50%;" title="Profile Picture"></div>';
                            }
                            ?>
                        <?php endif; ?>
                    </div>
                    <br>
                    <input type="hidden" name="custprofpic_attachment_id" id="custprofpic_attachment_id" value="<?php echo esc_attr($attachment_id); ?>" />
                    <button type="button" class="button" id="custprofpic_media_button"><?php esc_html_e('Choose from Media Library', 'custom-profile-picture'); ?></button>
                    <br><br>
                    <strong><?php esc_html_e('OR', 'custom-profile-picture'); ?></strong>
                    <br><br>
                    <input type="file" name="custprofpic_profile_picture" id="custprofpic_profile_picture" />
                    <br>
                    <span class="description"><?php esc_html_e('Upload a custom profile picture. Recommended size: 150x150px.', 'custom-profile-picture'); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }
} 