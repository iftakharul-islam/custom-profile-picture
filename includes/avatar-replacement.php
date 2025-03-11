<?php
if (!defined('ABSPATH')) {
    exit;
}

// Replace the avatar with the custom profile picture
add_filter('get_avatar', 'cpp_custom_avatar', 10, 5);

function cpp_custom_avatar($avatar, $id_or_email, $size, $default, $alt) {
    $user = false;

    if (is_numeric($id_or_email)) {
        $user = get_userdata($id_or_email);
    } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
        $user = get_userdata($id_or_email->user_id);
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
    }

    if ($user) {
        $profile_picture = get_user_meta($user->ID, 'cpp_profile_picture', true);
        if ($profile_picture) {
            return '<img src="' . esc_url($profile_picture) . '" alt="' . esc_attr($alt) . '" class="avatar avatar-' . esc_attr($size) . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" />';
        }
    }

    return $avatar;
}
