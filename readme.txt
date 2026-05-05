=== Custom Profile Picture – Replace Gravatar with Your Own Images ===
Contributors: ifatwp
Donate link: https://ifatwp.wordpress.com/
Tags: avatar, profile picture, gravatar, user profile, custom avatar
Requires PHP: 7.4
Requires at least: 5.6
Tested up to: 6.9
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Replace default Gravatars with custom profile pictures! Upload from media library or device. Bulk manage all users from one beautiful admin page.

== Description ==

**Custom Profile Picture** is the easiest way to replace WordPress default Gravatar avatars with custom profile pictures. Upload images directly or choose from your media library – managing user avatars has never been this simple!

= 🚀 Why Choose Custom Profile Picture? =

Tired of relying on Gravatar for user avatars? Want complete control over your site's user profile pictures? This plugin gives you the freedom to upload custom profile pictures without depending on external services.

= ✨ Key Features =

**📸 Dual Upload Methods**
* Upload images directly from your device
* Choose from WordPress Media Library
* Both methods available on individual user profiles

**⚡ Centralized Management Dashboard**
* Manage ALL user profile pictures from one page
* Beautiful grid view of all users
* Hover to change or remove pictures instantly
* AJAX-powered updates – no page refresh needed
* Search users by name, email, or username
* Pagination for sites with many users

**🎨 Professional Image Cropping**
* Built-in interactive cropping tool
* Real-time crop preview
* Maintains perfect square aspect ratio
* Mobile-friendly interface

**🔒 Security First**
* Proper nonce verification
* Capability checks for user permissions
* Secure AJAX handling
* Sanitized and validated inputs

**📱 Fully Responsive**
* Works perfectly on desktop, tablet, and mobile
* Touch-friendly controls
* Responsive admin interface

**⚙️ Developer Friendly**
* Clean, object-oriented code
* Proper WordPress coding standards
* Namespaced classes
* Well-documented code
* Extensible architecture

= 💡 Perfect For =

* Community websites
* Membership sites
* Corporate intranets
* Educational platforms
* Any site wanting custom user avatars
* Sites not wanting to depend on Gravatar

= 🎯 How It Works =

**Method 1: Individual User Profiles**
1. Go to Users <span aria-hidden="true" class="wp-exclude-emoji">→</span> Your Profile (or edit any user)
2. Scroll to "Profile Picture" section
3. Click "Choose from Media Library" OR upload directly
4. Image automatically replaces Gravatar everywhere

**Method 2: Bulk Management (Admin)**
1. Go to "Profile Pictures" in admin menu
2. See all users in a beautiful grid layout
3. Hover over any user's avatar
4. Click "Change" to select from media library
5. Click "Remove" to revert to Gravatar

= 🌟 What Makes This Plugin Special? =

Unlike other avatar plugins, Custom Profile Picture offers:
* **No configuration needed** – works out of the box
* **Dual interface** – individual and bulk management
* **Media library integration** – use existing images
* **Beautiful UX** – modern, intuitive design
* **Lightweight** – no bloat, just what you need
* **100% WordPress compatible** – uses standard avatar system

= 🔧 Technical Details =

* Integrates seamlessly with WordPress avatar system
* Works with `get_avatar()` function
* Replaces avatars site-wide automatically
* Stores images in WordPress media library
* Clean database structure with user meta
* No external dependencies (except Cropper.js for cropping)

= 📚 Documentation & Support =

Need help? Check out our:
* [Documentation](https://ifatwp.wordpress.com/)
* [Support Forum](https://wordpress.org/support/plugin/custom-profile-picture/)

== Installation ==

= Automatic Installation =

1. Go to Plugins <span aria-hidden="true" class="wp-exclude-emoji">→</span> Add New
2. Search for "Custom Profile Picture"
3. Click "Install Now"
4. Activate the plugin
5. Done! Start uploading profile pictures

= Manual Installation =

1. Download the plugin ZIP file
2. Go to Plugins <span aria-hidden="true" class="wp-exclude-emoji">→</span> Add New <span aria-hidden="true" class="wp-exclude-emoji">→</span> Upload Plugin
3. Choose the downloaded file
4. Click "Install Now"
5. Activate the plugin

= Usage =

**For Individual Users:**
1. Navigate to Users <span aria-hidden="true" class="wp-exclude-emoji">→</span> Your Profile
2. Find "Profile Picture" section
3. Upload or choose from media library
4. Save changes

**For Administrators:**
1. Go to "Profile Pictures" in WordPress admin menu
2. View all users in grid layout
3. Hover over user avatars to manage
4. Use search to find specific users

== Frequently Asked Questions ==

= Does this work with Gravatar? =

Yes! The plugin replaces Gravatar with custom images. If no custom image is uploaded, the default Gravatar will still show.

= What image formats are supported? =

All standard web formats: JPEG, JPG, PNG, and GIF.

= What's the recommended image size? =

We recommend 150×150 pixels for optimal display across all devices.

= Can users upload their own profile pictures? =

Yes! Users can upload their own pictures from their profile page. Admins can manage all user pictures from the centralized dashboard.

= Will this affect my site's performance? =

No! The plugin is lightweight and only loads on relevant admin pages. Images are stored in WordPress media library using WordPress's own optimization.

= Can I bulk upload profile pictures? =

Yes! Use the "Profile Pictures" admin page to manage multiple users quickly from one interface.

= Does it work with BuddyPress or other profile plugins? =

Yes, if they use WordPress's standard `get_avatar()` function. The plugin integrates with WordPress's core avatar system.

= Can I remove a custom profile picture? =

Absolutely! Just click the "Remove" button on the admin page or delete it from the user's profile page. The avatar will revert to Gravatar.

= Is the plugin translation ready? =

Yes! All strings are translatable using WordPress's translation system.

= Does it work with multisite? =

Yes! The plugin is multisite compatible.

== Screenshots ==

1. Centralized admin page showing all users in a beautiful grid layout
2. Hover over avatars to reveal change/remove buttons
3. Individual user profile upload interface with media library option
4. Interactive image cropping modal for perfect avatars
5. Search and filter users easily
6. Mobile-responsive interface

== Changelog ==

= 1.0.2 – January 8, 2026 =
**Major Update – New Features & Improvements**

* **NEW:** Centralized admin page for bulk profile picture management
* **NEW:** Beautiful grid view of all users with avatars
* **NEW:** Media library integration on user profiles
* **NEW:** Hover actions – change/remove pictures instantly
* **NEW:** User search functionality by name, email, username
* **NEW:** AJAX-powered updates without page refresh
* **NEW:** Pagination support for large user bases
* **IMPROVED:** Smaller, more compact user cards for better overview
* **IMPROVED:** Better responsive design for mobile/tablet
* **IMPROVED:** Fixed header alignment and search box layout
* **IMPROVED:** Enhanced UX with toast notifications
* **IMPROVED:** Better attachment ID tracking for media library images
* **ADDED:** Real-time image preview updates
* **ADDED:** Success/error notification system
* **ADDED:** Loading states for better user feedback
* **OPTIMIZED:** CSS for better performance and smaller file size
* **FIXED:** Email display now shows on hover to save space
* **FIXED:** Responsive search form for mobile devices

= 1.0.1 – July 21, 2025 =
* **IMPROVED:** Added proper namespacing for better code organization
* **IMPROVED:** Enhanced error handling for image uploads
* **ENHANCED:** Security with additional nonce verification
* **ADDED:** Responsive styling for mobile devices
* **FIXED:** Image preview display issues
* **IMPROVED:** Cropping interface usability
* **ENHANCED:** Proper sanitization for AJAX requests
* **ADDED:** Alt text for better accessibility

= 1.0.0 – Initial Release =
* **ADDED:** Image upload functionality
* **ADDED:** Interactive image cropping with Cropper.js
* **ADDED:** AJAX-based image saving
* **ADDED:** Responsive modal design
* **ADDED:** Security with nonce verification
* **ADDED:** Input sanitization and validation
* **ADDED:** Avatar replacement system
* **ADDED:** User profile integration

== Upgrade Notice ==

= 1.0.2 =
Major update! New centralized admin page for managing all user profile pictures from one place. Upload from media library, search users, and manage avatars with beautiful UX. Highly recommended upgrade!

= 1.0.1 =
Important security and usability improvements. Recommended upgrade for all users.

= 1.0.0 =
Initial release with profile picture upload and cropping functionality.

== Additional Information ==

= Credits =

* Cropper.js library for image manipulation
* WordPress core team for the amazing platform

= Privacy Policy =

This plugin does not collect, store, or transmit any user data outside your WordPress installation. All images are stored in your WordPress media library. No external services are used.
Custom Profile Picture – Replace Gravatar with Your Own Images uses [Appsero](https://appsero.com) SDK to collect some telemetry data upon user's confirmation. This helps us to troubleshoot problems faster & make product improvements.

Appsero SDK **does not gather any data by default.** The SDK only starts gathering basic telemetry data **when a user allows it via the admin notice**. We collect the data to ensure a great user experience for all our users. 

Integrating Appsero SDK **DOES NOT IMMEDIATELY** start gathering data, **without confirmation from users in any case.**

Learn more about how [Appsero collects and uses this data](https://appsero.com/privacy-policy/).

= Want to Contribute? =

We welcome contributions! Visit our [GitHub repository](https://github.com/ifatwp/custom-profile-picture) to report issues or submit pull requests.

= Love This Plugin? =

* [Leave a review](https://wordpress.org/support/plugin/custom-profile-picture/reviews/#new-post) – it helps others find this plugin!
* [Donate](https://ifatwp.wordpress.com/) – support continued development

= For Developers =

Custom Profile Picture is built with:
* Object-oriented PHP
* WordPress coding standards
* Proper namespacing (Ifatwp\CustomProfilePicture)
* Action and filter hooks
* AJAX for dynamic updates
* WordPress Media Library API
* Clean, documented code

Filters available:
* Custom hooks coming in future versions

For questions, customizations, or feature requests, please visit the support forum.`