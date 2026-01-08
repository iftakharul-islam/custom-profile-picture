<?php
/*
 * Plugin Name: Custom Profile Picture
 * Description: Allows users to upload their own profile pictures in the profile section.
 * Plugin URI: https://ifatwp.wordpress.com/2025/05/07/custom-profile-picture/
 * Version: 1.0.2
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Author: ifatwp
 * Author URI:  https://ifatwp.wordpress.com/
 * Text Domain: custom-profile-picture
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants with longer prefix
define('CUSTPROFPIC_PLUGIN_VERSION','1.0.2');
define('CUSTPROFPIC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CUSTPROFPIC_PLUGIN_FILE', __FILE__);
define('CUSTPROFPIC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader for the plugin classes
spl_autoload_register(function ($class) {
    // Check if the class belongs to our namespace
    if (strpos($class, 'Ifatwp\\CustomProfilePicture\\') !== 0) {
        return;
    }

    // Remove the namespace prefix
    $class = str_replace('Ifatwp\\CustomProfilePicture\\', '', $class);
    
    // Convert class name to file path (handle both underscore and hyphen cases)
    $file = CUSTPROFPIC_PLUGIN_DIR . 'includes/class-' . strtolower(str_replace('_', '-', $class)) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Manual includes as fallback
require_once CUSTPROFPIC_PLUGIN_DIR . 'includes/class-plugin.php';
require_once CUSTPROFPIC_PLUGIN_DIR . 'includes/class-form-handler.php';
require_once CUSTPROFPIC_PLUGIN_DIR . 'includes/class-profile-field.php';
require_once CUSTPROFPIC_PLUGIN_DIR . 'includes/class-save-profile-picture.php';
require_once CUSTPROFPIC_PLUGIN_DIR . 'includes/class-avatar-replacement.php';
require_once CUSTPROFPIC_PLUGIN_DIR . 'includes/class-image-cropping.php';
require_once CUSTPROFPIC_PLUGIN_DIR . 'includes/class-admin-page.php';

// Initialize the plugin
add_action('plugins_loaded', function() {
    \Ifatwp\CustomProfilePicture\Plugin::get_instance();
});