<?php
if (!defined('ABSPATH')) {
    exit;
}
global $enc_status;
$enc_status = false;

// Add enctype to user profile form
add_action('user_edit_form_tag', 'cpp_add_enctype_to_form');
add_action('show_user_profile', 'cpp_add_enctype_to_form'); // For the current user's profile page

function cpp_add_enctype_to_form() {
    global $enc_status;
   if(!$enc_status){
    $enc_status = true;
       echo ' enctype="multipart/form-data"';
   }
}
