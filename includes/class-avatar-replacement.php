<?php
namespace Ifatwp\CustomProfilePicture;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Avatar Replacement Class
 */
class Avatar_Replacement {
    
    /**
     * Cache for user avatars to prevent repeated queries
     */
    private static $user_cache = array();
    
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
        add_filter('get_avatar', array($this, 'custom_avatar'), 10, 5);
    }
    
    /**
     * Replace the avatar with the custom profile picture
     */
    public function custom_avatar($avatar, $id_or_email, $size, $default, $alt) {
        $user = false;

        // Resolve every shape WordPress may pass: numeric ID, email string,
        // WP_User, WP_Post (post author), or WP_Comment (comment author).
        if (is_numeric($id_or_email)) {
            $user = get_userdata((int) $id_or_email);
        } elseif ($id_or_email instanceof \WP_User) {
            $user = $id_or_email;
        } elseif ($id_or_email instanceof \WP_Post) {
            $user = get_userdata((int) $id_or_email->post_author);
        } elseif ($id_or_email instanceof \WP_Comment) {
            if (!empty($id_or_email->user_id)) {
                $user = get_userdata((int) $id_or_email->user_id);
            } elseif (!empty($id_or_email->comment_author_email)) {
                $user = get_user_by('email', $id_or_email->comment_author_email);
            }
        } elseif (is_string($id_or_email) && is_email($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
        }

        if ($user) {
            // Check cache first to avoid repeated queries
            if (!isset(self::$user_cache[$user->ID])) {
                self::$user_cache[$user->ID] = array(
                    'picture' => get_user_meta($user->ID, 'custprofpic_profile_picture', true),
                    'attachment_id' => get_user_meta($user->ID, 'custprofpic_attachment_id', true)
                );
            }
            
            $profile_picture = self::$user_cache[$user->ID]['picture'];
            $attachment_id = self::$user_cache[$user->ID]['attachment_id'];
            
            if ($profile_picture) {
                // Use cached attachment ID to avoid expensive attachment_url_to_postid() query
                if ($attachment_id && wp_attachment_is_image($attachment_id)) {
                    return wp_get_attachment_image($attachment_id, array($size, $size), false, array(
                        'class' => 'custprofpic-profile-avatar avatar avatar-' . esc_attr($size),
                        'alt' => esc_attr($alt),
                        'width' => esc_attr($size),
                        'height' => esc_attr($size)
                    ));
                } else {
                    return '<img class="custprofpic-profile-avatar avatar avatar-' . esc_attr($size) . '" src="' . esc_url($profile_picture) . '" alt="' . esc_attr($alt) . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" />';
                }
            }
        }

        return $avatar;
    }
} 