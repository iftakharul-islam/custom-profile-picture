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

        if (is_numeric($id_or_email)) {
            $user = get_userdata($id_or_email);
        } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
            $user = get_userdata($id_or_email->user_id);
        } elseif (is_string($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
        }

        if ($user) {
            $profile_picture = get_user_meta($user->ID, 'custprofpic_profile_picture', true);
            if ($profile_picture) {
                $attachment_id = attachment_url_to_postid($profile_picture);
                if ($attachment_id) {
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