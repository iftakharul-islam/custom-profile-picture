<?php
namespace Ifatwp\CustomProfilePicture;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Frontend Profile Class
 *
 * Provides a [custom_profile_picture] shortcode that lets logged-in users
 * upload and crop their own profile picture on the public-facing site.
 */
class Frontend_Profile {

    /**
     * Whether the shortcode has been rendered on the current page.
     * Used to conditionally enqueue scripts/styles only when needed.
     *
     * @var bool
     */
    private $shortcode_rendered = false;

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
        add_shortcode('custom_profile_picture', array($this, 'render_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_custprofpic_frontend_remove_picture', array($this, 'ajax_remove_picture'));
    }

    /**
     * Enqueue frontend assets.
     *
     * Scripts and styles are only added to pages that contain the shortcode.
     * We use a late priority so that the global $post is available when
     * has_shortcode() is called.
     */
    public function enqueue_assets() {
        global $post;

        if (!$this->shortcode_rendered && (!$post || !has_shortcode($post->post_content, 'custom_profile_picture'))) {
            return;
        }

        $this->enqueue_assets_now();
    }

    /**
     * Register and enqueue the frontend assets.
     *
     * Split out from enqueue_assets() so render_shortcode() can call it
     * directly. In block (FSE) themes the shortcode often lives in a block
     * template, template part, or synced pattern rather than in
     * $post->post_content, so has_shortcode() on the post body returns false
     * and the wp_enqueue_scripts gate never fires. Calling this from the
     * shortcode render guarantees the assets load wherever the shortcode is
     * actually used. WordPress allows enqueuing during the_content because the
     * scripts/styles are printed in the footer.
     */
    public function enqueue_assets_now() {
        wp_enqueue_style(
            'custprofpic-cropper-style',
            CUSTPROFPIC_PLUGIN_URL . 'assets/css/cropper.min.css',
            array(),
            CUSTPROFPIC_PLUGIN_VERSION
        );

        wp_enqueue_style(
            'custprofpic-frontend',
            CUSTPROFPIC_PLUGIN_URL . 'assets/css/frontend-profile.css',
            array('custprofpic-cropper-style'),
            CUSTPROFPIC_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'custprofpic-cropper',
            CUSTPROFPIC_PLUGIN_URL . 'assets/js/cropper.min.js',
            array(),
            CUSTPROFPIC_PLUGIN_VERSION,
            true
        );

        wp_enqueue_script(
            'custprofpic-frontend-upload',
            CUSTPROFPIC_PLUGIN_URL . 'assets/js/frontend-upload.js',
            array('jquery', 'custprofpic-cropper'),
            CUSTPROFPIC_PLUGIN_VERSION,
            true
        );

        wp_localize_script('custprofpic-frontend-upload', 'custprofpicFrontend', array(
            'ajax_url'   => admin_url('admin-ajax.php'),
            'crop_nonce' => wp_create_nonce('custprofpic_crop_nonce'),
            'remove_nonce' => wp_create_nonce('custprofpic_frontend_remove_nonce'),
            'user_id'    => get_current_user_id(),
            'strings'    => array(
                'select_image'    => __('Please select an image file.', 'custom-profile-picture'),
                'upload_error'    => __('An error occurred while uploading. Please try again.', 'custom-profile-picture'),
                'remove_error'    => __('An error occurred while removing the picture. Please try again.', 'custom-profile-picture'),
                'confirm_remove'  => __('Are you sure you want to remove your profile picture?', 'custom-profile-picture'),
            ),
        ));
    }

    /**
     * Render the [custom_profile_picture] shortcode.
     *
     * @return string HTML output.
     */
    public function render_shortcode() {
        if (!is_user_logged_in()) {
            return '<p class="custprofpic-notice">' .
                esc_html__('You must be logged in to manage your profile picture.', 'custom-profile-picture') .
                '</p>';
        }

        // Flag that we need assets on this page, and enqueue immediately so
        // block themes (where the shortcode is not in $post->post_content)
        // still get the scripts/styles.
        $this->shortcode_rendered = true;
        $this->enqueue_assets_now();

        $user_id        = get_current_user_id();
        $profile_picture = get_user_meta($user_id, 'custprofpic_profile_picture', true);
        $attachment_id   = (int) get_user_meta($user_id, 'custprofpic_attachment_id', true);

        if ($attachment_id) {
            $avatar_img = wp_get_attachment_image($attachment_id, array(150, 150), false, array(
                'class' => 'custprofpic-frontend-avatar',
                'id'    => 'custprofpic-current-avatar',
                'alt'   => esc_attr__('Profile Picture', 'custom-profile-picture'),
            ));
        } elseif ($profile_picture) {
            $avatar_img = '<img id="custprofpic-current-avatar" class="custprofpic-frontend-avatar" src="' .
                esc_url($profile_picture) . '" alt="' .
                esc_attr__('Profile Picture', 'custom-profile-picture') . '" width="150" height="150" />';
        } else {
            $gravatar_url = get_avatar_url($user_id, array('size' => 150));
            $avatar_img   = '<img id="custprofpic-current-avatar" class="custprofpic-frontend-avatar" src="' .
                esc_url($gravatar_url) . '" alt="' .
                esc_attr__('Profile Picture', 'custom-profile-picture') . '" width="150" height="150" />';
        }

        ob_start();
        ?>
        <div class="custprofpic-frontend-wrap" id="custprofpic-frontend-wrap">

            <div class="custprofpic-avatar-area">
                <?php echo $avatar_img; // Already escaped above. ?>
            </div>

            <div id="custprofpic-message" class="custprofpic-message" role="alert" aria-live="polite"></div>

            <div class="custprofpic-actions">
                <label class="custprofpic-upload-label button" for="custprofpic-frontend-file">
                    <?php esc_html_e('Upload New Picture', 'custom-profile-picture'); ?>
                </label>
                <input type="file" id="custprofpic-frontend-file" accept="image/*" style="display:none;" />

                <?php if ($profile_picture): ?>
                    <button type="button" id="custprofpic-frontend-remove" class="button custprofpic-remove-btn">
                        <?php esc_html_e('Remove Picture', 'custom-profile-picture'); ?>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Crop modal -->
            <div id="custprofpic-frontend-modal" class="custprofpic-frontend-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="custprofpic-modal-title">
                <div class="custprofpic-frontend-modal-content">
                    <div class="custprofpic-frontend-modal-header">
                        <h3 id="custprofpic-modal-title"><?php esc_html_e('Crop Profile Picture', 'custom-profile-picture'); ?></h3>
                        <button type="button" class="custprofpic-close-modal" aria-label="<?php esc_attr_e('Close', 'custom-profile-picture'); ?>">&times;</button>
                    </div>
                    <div class="custprofpic-frontend-modal-body">
                        <div class="custprofpic-frontend-image-container">
                            <img id="custprofpic-frontend-crop-image" src="" alt="<?php esc_attr_e('Crop Preview', 'custom-profile-picture'); ?>" />
                        </div>
                        <div class="custprofpic-frontend-crop-controls">
                            <button type="button" id="custprofpic-frontend-crop-save" class="button button-primary">
                                <?php esc_html_e('Save Crop', 'custom-profile-picture'); ?>
                            </button>
                            <button type="button" id="custprofpic-frontend-crop-cancel" class="button">
                                <?php esc_html_e('Cancel', 'custom-profile-picture'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX handler: remove the current user's profile picture (frontend).
     */
    public function ajax_remove_picture() {
        check_ajax_referer('custprofpic_frontend_remove_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'custom-profile-picture')));
        }

        $user_id = get_current_user_id();

        delete_user_meta($user_id, 'custprofpic_profile_picture');
        delete_user_meta($user_id, 'custprofpic_attachment_id');

        $gravatar_url = get_avatar_url($user_id, array('size' => 150));

        wp_send_json_success(array(
            'message'    => __('Profile picture removed.', 'custom-profile-picture'),
            'avatar_url' => $gravatar_url,
        ));
    }
}
