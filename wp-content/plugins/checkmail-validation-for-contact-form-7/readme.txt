=== Checkmail validation for Contact Form 7 ===
Contributors: ipalo
Tags: contact, form, contact form, check, email, double, double check, re-enter email, email validation, email check
Requires at least: 3.2
Tested up to: 3.4.1
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a double email check field to Contact Form 7.

== Description ==

Checkmail Validation for Contact Form 7 add the double check email field to your form and verify email match with the CF7 Ajax validation.

= Double email check =
This plugin add a new field in Contact Form 7 called "Checkmail" that allow to do a double email check when submitting the form. The new field will ask to users to confirm their email by typing it into a second field.

If you want to do this in your form, you only have to add the "Checkmail" field into the CF7 form and enter the email field name you want to check. The validation is done by the CF7 Ajax-powered style: when submitting form CF7 will do the double email check, if not match returns error and ask to users to verify the email addresses.

== Installation ==

1. Upload the entire `cf7-checkmail-validation` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

The Contact Form 7 plugin must be installed and activated for the Checkmail Validation for Contact Form 7 to work. You can download the CF7 plugin [here](http://wordpress.org/extend/plugins/contact-form-7/).

== Screenshots ==

1. The new "Checkmail" field added to the "Generate tag" menu.
2. The "Checkmail" field setup (also added to the form code window).
3. The double email check in the frontend form.

== Changelog ==

= 0.2 =

* Updated to CF7 3.2 fields structure.

== Upgrade Notice ==

The Contact Form 7 plugin must be installed and activated for the Checkmail Validation for Contact Form 7 v3.2 to work. You can download the CF7 plugin [here](http://wordpress.org/extend/plugins/contact-form-7/).