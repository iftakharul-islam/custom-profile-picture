It's time to move forward with the plugin review "ifatwp"!

Your plugin is not yet ready to be approved, you are receiving this email because the volunteers have manually checked it and have found some issues in the code / functionality of your plugin.

Please check this email thoroughly, address any issues listed, test your changes, and upload a corrected version of your code if all is well.

The manual review process
Your plugin is checked by a volunteer who will send you the issues found in this email.
You will read this email in its entirety, checking each issue as well as the links to the documentation and examples provided. In case of any doubt, you will reply to this email asking for clarification.
Then you will thoroughly fix any issues, test your plugin, upload a corrected version of your plugin and reply to this email.
As soon as the volunteer is able, they/she/he will check your corrected plugin again. Please, be patient waiting for a reply.
If there are no further issues, the plugin will be approved 🎉
If there are still issues, the process will go back to step 1 until all the issues have been addressed 🫷This is the current step.

⚠️ When you reply we will be reviewing your entire plugin again, so please do not reply until you are sure you have addressed all of the issues listed, otherwise your submission will be delayed and eventually rejected.

List of issues found



## Internationalization: Text domain does not match plugin slug.

In order to make a string translatable in your plugin you are using a set of special functions. These functions collectively are known as "gettext".

These functions have a parameter called "text domain", which is a unique identifier for retrieving translated strings.

This "text domain" must be the same as your plugin slug so that the plugin can be translated by the community using the tools provided by the directory. As for example, if this plugin slug is "custom-profile-picture" the Internationalization functions should look like:
esc_html__( 'Hello', 'custom-profile-picture' );

From your plugin, you have set your text domain as follows:
# This plugin is using the domain "custom-profile-profile" for 1 element(s).


However, the current plugin slug is this:
custom-profile-picture



## Generic function/class/define/namespace/option names

All plugins must have unique function names, namespaces, defines, class and option names. This prevents your plugin from conflicting with other plugins or themes. We need you to update your plugin to use more unique and distinct names.

A good way to do this is with a prefix. For example, if your plugin is called "Custom Profile Picture" then you could use names like these:
function custprpi_save_post(){ ... }
class CUSTPRPI_Admin { ... }
update_option( 'custprpi_options', $options );
register_setting( 'custprpi_settings', 'custprpi_user_id', ... );
define( 'CUSTPRPI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
global $custprpi_options;
namespace ifatwp\customprofilepicture;

Disclaimer: These are just examples that may have been self-generated from your plugin name, we trust you can find better options. If you have a good alternative, please use it instead, this is just an example.

Don't try to use two (2) or three (3) letter prefixes anymore. We host nearly 100-thousand plugins on WordPress.org alone. There are tens of thousands more outside our servers. Believe us, you’re going to run into conflicts.

You also need to avoid the use of __ (double underscores), wp_ , or _ (single underscore) as a prefix. Those are reserved for WordPress itself. You can use them inside your classes, but not as stand-alone function.

Please remember, if you're using _n() or __() for translation, that's fine. We're only talking about functions you've created for your plugin, not the core functions from WordPress. In fact, those core features are why you need to not use those prefixes in your own plugin! You don't want to break WordPress for your users.

Related to this, using if (!function_exists('NAME')) { around all your functions and classes sounds like a great idea until you realize the fatal flaw. If something else has a function with the same name and their code loads first, your plugin will break. Using if-exists should be reserved for shared libraries only.

Remember: Good prefix names are unique and distinct to your plugin. This will help you and the next person in debugging, as well as prevent conflicts.

Analysis result:
# This plugin is using the prefix "cpp" for 14 element(s).

# The prefix "cpp" is too short, we require prefixes to be over 4 characters.
includes/avatar-replacement.php:9 function cpp_custom_avatar
includes/form-handler.php:12 function cpp_add_enctype_to_form
includes/profile-field.php:10 function cpp_add_profile_picture_field
includes/image-cropping.php:19 wp_localize_script('cpp-custom-cropping', 'cpp_ajax', array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('cpp_crop_nonce')));
includes/image-cropping.php:9 function cpp_enqueue_cropping_scripts
includes/image-cropping.php:30 function cpp_add_cropping_modal
includes/image-cropping.php:59 function cpp_handle_cropped_image
includes/save-profile-picture.php:10 function cpp_save_profile_picture
custom-profile-picture.php:22 define('CPP_PLUGIN_VERSION', '1.0.0');
custom-profile-picture.php:23 define('CPP_PLUGIN_DIR', plugin_dir_path(__FILE__));
custom-profile-picture.php:24 define('CPP_PLUGIN_FILE', __FILE__);
custom-profile-picture.php:25 define('CPP_PLUGIN_URL', plugin_dir_url(__FILE__));
custom-profile-picture.php:37 function cpp_custom_avatar_url
custom-profile-picture.php:54 function cpp_custom_avatar_data

# Looks like there are elements not using common prefixes.
includes/form-handler.php:5 $enc_status;
includes/form-handler.php:13 $enc_status;


👉 Your next steps

Please, before replying make sure to perform the following actions:
Read this email.
Take the time to understand the issues shared, check the included examples, check the documentation, research the issue on internet, and gain a better understanding of what's happening and how you can fix it. We want you to thoroughly understand these issues so that you can take them into account when maintaining your plugin in the future.
Note that there may be false positives - we are humans and make mistakes, we apologize if there is anything we have gotten wrong.
If you have doubts you can ask us for clarification, when asking us please be clear, concise, direct and include an example.
You can make use of tools like PHPCS or Plugin Check to further help you with finding all the issues.
Fix your plugin.
Test your plugin on a clean WordPress installation. You can try Playground.
Go to "Add your plugin" and upload an updated version of this plugin. You can update the code there whenever you need to, along the review process, and we will check the latest version.
Reply to this email telling us that you have updated it, and let us know if there is anything we need to know or have in mind.
Please do not list the changes made as we will review the whole plugin again, just share anything you want to clarify.

ℹ️ To make this process as quick as possible and to avoid burden on the volunteers devoting their time to review this plugin's code, we ask you to thoroughly check all shared issues and fix them before sending the code back to us. I know we already asked you to do so, and it is because we are really trying to make it very clear.

While we try to make our reviews as exhaustive as possible we, like you, are humans and may have missed things. We appreciate your patience and understanding.

Review ID: R custom-profile-picture/ifatwp/21Mar25/T2 20Jul25/3.4