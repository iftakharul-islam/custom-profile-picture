<?php
namespace Ifatwp\CustomProfilePicture;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Page Class - Manage all user profile pictures from one place
 */
class Admin_Page {

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
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_custprofpic_update_user_picture', array($this, 'ajax_update_user_picture'));
        add_action('wp_ajax_custprofpic_remove_user_picture', array($this, 'ajax_remove_user_picture'));
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Profile Pictures', 'custom-profile-picture'),
            __('Profile Pictures', 'custom-profile-picture'),
            'edit_users',
            'custprofpic-manager',
            array($this, 'render_admin_page'),
            'dashicons-admin-users',
            71
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'toplevel_page_custprofpic-manager') {
            return;
        }

        // Enqueue WordPress media uploader
        wp_enqueue_media();

        // Enqueue admin CSS
        wp_enqueue_style(
            'custprofpic-admin',
            CUSTPROFPIC_PLUGIN_URL . 'assets/css/admin-page.css',
            array(),
            CUSTPROFPIC_PLUGIN_VERSION
        );

        // Enqueue admin JavaScript
        wp_enqueue_script(
            'custprofpic-admin',
            CUSTPROFPIC_PLUGIN_URL . 'assets/js/admin-page.js',
            array('jquery'),
            CUSTPROFPIC_PLUGIN_VERSION,
            true
        );

        // Localize script for AJAX
        wp_localize_script('custprofpic-admin', 'custprofpicAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('custprofpic_admin_nonce'),
            'strings' => array(
                'confirm_remove' => __('Are you sure you want to remove this profile picture?', 'custom-profile-picture'),
                'success' => __('Profile picture updated successfully!', 'custom-profile-picture'),
                'error' => __('Error updating profile picture. Please try again.', 'custom-profile-picture'),
                'removed' => __('Profile picture removed successfully!', 'custom-profile-picture'),
            )
        ));
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        if (!current_user_can('edit_users')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Get all users with pagination
        $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $per_page = 20;
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        $args = array(
            'number' => $per_page,
            'paged' => $paged,
            'orderby' => 'display_name',
            'order' => 'ASC',
        );

        if (!empty($search)) {
            $args['search'] = '*' . $search . '*';
            $args['search_columns'] = array('user_login', 'user_email', 'display_name');
        }

        $user_query = new \WP_User_Query($args);
        $users = $user_query->get_results();
        $total_users = $user_query->get_total();
        $total_pages = ceil($total_users / $per_page);

        ?>
        <div class="wrap custprofpic-admin-wrap">
            <h1><?php esc_html_e('Manage User Profile Pictures', 'custom-profile-picture'); ?></h1>

            <div class="custprofpic-header">
                <form method="get" action="">
                    <input type="hidden" name="page" value="custprofpic-manager" />
                    <p class="search-box">
                        <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Search users...', 'custom-profile-picture'); ?>" />
                        <button type="submit" class="button"><?php esc_html_e('Search', 'custom-profile-picture'); ?></button>
                    </p>
                </form>
            </div>

            <div class="custprofpic-users-grid">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <?php
                        $profile_picture = get_user_meta($user->ID, 'custprofpic_profile_picture', true);
                        $attachment_id = get_user_meta($user->ID, 'custprofpic_attachment_id', true);
                        $avatar_url = $profile_picture ? $profile_picture : get_avatar_url($user->ID, array('size' => 150));
                        ?>
                        <div class="custprofpic-user-card" data-user-id="<?php echo esc_attr($user->ID); ?>">
                            <div class="custprofpic-avatar-container">
                                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($user->display_name); ?>" class="custprofpic-avatar" />
                                <div class="custprofpic-avatar-overlay">
                                    <button type="button" class="button button-primary custprofpic-change-picture" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                        <span class="dashicons dashicons-camera"></span>
                                        <?php esc_html_e('Change', 'custom-profile-picture'); ?>
                                    </button>
                                    <?php if ($profile_picture): ?>
                                        <button type="button" class="button custprofpic-remove-picture" data-user-id="<?php echo esc_attr($user->ID); ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                            <?php esc_html_e('Remove', 'custom-profile-picture'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="custprofpic-user-info">
                                <h3><?php echo esc_html($user->display_name); ?></h3>
                                <p class="custprofpic-user-email"><?php echo esc_html($user->user_email); ?></p>
                                <p class="custprofpic-user-role"><?php echo esc_html(implode(', ', $user->roles)); ?></p>
                            </div>
                            <input type="hidden" class="custprofpic-attachment-id" value="<?php echo esc_attr($attachment_id); ?>" />
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><?php esc_html_e('No users found.', 'custom-profile-picture'); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="custprofpic-pagination">
                    <?php
                    $pagination_args = array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'current' => $paged,
                        'total' => $total_pages,
                        'prev_text' => '&laquo; ' . __('Previous', 'custom-profile-picture'),
                        'next_text' => __('Next', 'custom-profile-picture') . ' &raquo;',
                    );
                    echo paginate_links($pagination_args);
                    ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * AJAX handler to update user profile picture
     */
    public function ajax_update_user_picture() {
        check_ajax_referer('custprofpic_admin_nonce', 'nonce');

        if (!current_user_can('edit_users')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'custom-profile-picture')));
        }

        $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;
        $attachment_id = isset($_POST['attachment_id']) ? absint($_POST['attachment_id']) : 0;

        if (!$user_id || !$attachment_id) {
            wp_send_json_error(array('message' => __('Invalid parameters.', 'custom-profile-picture')));
        }

        // Verify the attachment exists and is an image
        if (!wp_attachment_is_image($attachment_id)) {
            wp_send_json_error(array('message' => __('Invalid image.', 'custom-profile-picture')));
        }

        $attachment_url = wp_get_attachment_url($attachment_id);
        if (!$attachment_url) {
            wp_send_json_error(array('message' => __('Could not get image URL.', 'custom-profile-picture')));
        }

        // Update user meta
        update_user_meta($user_id, 'custprofpic_profile_picture', esc_url($attachment_url));
        update_user_meta($user_id, 'custprofpic_attachment_id', $attachment_id);

        wp_send_json_success(array(
            'message' => __('Profile picture updated successfully!', 'custom-profile-picture'),
            'image_url' => $attachment_url,
        ));
    }

    /**
     * AJAX handler to remove user profile picture
     */
    public function ajax_remove_user_picture() {
        check_ajax_referer('custprofpic_admin_nonce', 'nonce');

        if (!current_user_can('edit_users')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'custom-profile-picture')));
        }

        $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;

        if (!$user_id) {
            wp_send_json_error(array('message' => __('Invalid user ID.', 'custom-profile-picture')));
        }

        // Remove user meta
        delete_user_meta($user_id, 'custprofpic_profile_picture');
        delete_user_meta($user_id, 'custprofpic_attachment_id');

        $avatar_url = get_avatar_url($user_id, array('size' => 150));

        wp_send_json_success(array(
            'message' => __('Profile picture removed successfully!', 'custom-profile-picture'),
            'image_url' => $avatar_url,
        ));
    }
}
