=== Contact Form with Mailman ===
Contributors: grumpytoaster
Tags: contact,contact us,comment,mailman,form,contact form,comment form,Post,plugin,posts,shortcode,page
Requires at least: 3.1.0
Tested up to: 4.3.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates a simple contact form with a check box to subscribe to a mailman mailing list.

== Description ==
This plugin creates a simple contact form with a check box to subscribe to a mailman mailing list. The config is in the main file. Future versions will include the settings directly in the Wordpress admin.

== Installation ==
1. Upload the contents of the zip to the '/wp-content/plugins/' directory.
2. Navigate to the plugins dashboard
3. Search for 'Contact Form with Mailman'
4. Click 'Activate'
5. Click on 'Mailman Settings' on the left.
6. Fill in the 3 fields to match your mailman mailing list.
7. Click 'Update'
8. Add the short code '[mailman_contact_form]' to any page or post you would like the form to display on.

You're done!

== Frequently Asked Questions ==
Q: Can you update the mailing list settings in the admin?
A: At the moment no but this will be coming in a future release.

Q: Will upgrading from version 1.x to version 2.x break it?
A: Yes! In version 1.x the mailman list information was stored in the main php file. With version 2.x the mailman list information is now accessable through the Wordpress Admin menu. Because version 2.x is looking for the list info in a different spot you will need to update the settings in the Wordpress Admin.

== Screenshots ==
1. This is what the output looks like.

== Upgrade Notice ==
= 2.0.0 =
In version 1.x the mailman list information was stored in the main php file. With version 2.x the mailman list information is now accessable through the Wordpress Admin menu. Because version 2.x is looking for the list info in a different spot you will need to update the settings in the Wordpress Admin.

== Changelog ==
= 2.1.2 =
* Commented out print_r() which was causing unwanted output.

= 2.1.1 =
* Updated error message to be more descriptive.

= 2.1.0 =
* Saving settings now indicates to the user the settings were saved. Updated the look of the settings page form and some wording changes.

= 2.0.0 =
* All config options are now available in the Wordpress Admin

= 1.1.2 =
* Subscribe to mailing list checkbox is now checked by default.

= 1.1.1 =
* I honestly don't remember what I did for this update.

= 1.1.0 =
* Added prefix to each function to eliminate conflicts

= 1.0.0 =
* Inital release

== Upgrade Notice ==
The update will clear your settings!! Please make a copy of your settings before you update.