=== Custom Profile Picture ===
Contributors: ifatwp
Tags: profile picture, avatar, user profile, resize avatar picture
Requires PHP: 7.4
Requires at least: 5.6
Tested up to: 6.8
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enhance user profiles with custom profile pictures featuring an intuitive image cropping interface.

== Description ==

Profile allows users to easily upload and crop custom profile pictures. The plugin provides a seamless image cropping experience directly within the WordPress admin interface.

Key Features:

* Easy profile picture upload
* Interactive image cropping tool
* Maintains aspect ratio for consistent display
* Works on both user profile and user edit pages
* Mobile-friendly interface
* Real-time crop preview
* Object-oriented architecture with proper namespacing

== Installation ==

1. Upload the `custom-profile-profile` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to your user profile or edit any user's profile to use the custom profile picture feature

== Frequently Asked Questions ==

= What image formats are supported? =

The plugin supports standard web image formats including JPEG, PNG, and GIF.

= Can I adjust the crop area? =

Yes, the interactive cropping tool allows you to resize and move the crop area while maintaining a square aspect ratio for optimal display.

= Where can I see the uploaded profile picture? =

The profile picture will be displayed on your user profile page and anywhere that uses WordPress's default avatar system.

== Screenshots ==

1. Profile picture upload interface
2. Image cropping modal
3. Profile picture display

== Changelog ==

= v1.0.2 30 July 2025 =

= v1.0.1 21 July 2025 =
* Added proper namespacing for better code organization
* Improved error handling for image uploads
* Enhanced security with additional nonce verification
* Added responsive styling for mobile devices
* Fixed image preview display issues
* Improved cropping interface usability
* Added proper sanitization for AJAX requests
* Added alt text for better accessibility


= 1.0.0 =
* Initial release
* Added image upload functionality
* Implemented interactive image cropping
* Added AJAX-based image saving
* Included responsive modal design
* Added security improvements with nonce verification
* Enhanced input sanitization and validation

== Upgrade Notice ==

= 1.0.0 =
Initial release of CPP Profile plugin with image upload and cropping functionality.

== Additional Information ==

The plugin uses the Cropper.js library for image manipulation and provides a user-friendly interface for managing profile pictures within WordPress.