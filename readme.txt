=== Logbook ===
Contributors: miyauchi, tarosky
Tags: comments, spam
Requires at least: 4.8
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: nightly
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin is for logging users' activities. You can check anytime who and what has changed.

== Description ==

Once you have installed and activated it, you can check the following activities.


=== Features ===

* Super lightweight and it doesn't affect site's performances.
* It has passed over 100 patterns unit test, you can use it in the enterprise use.
* The event saving logs is extensive, so you can develop own add-ons for saving it.
* Logging following activities on WordPress
    * Publish, update or delete published posts.
    * Activate or deactivate plugins or themes.
    * Updating WordPress core, plugins, language files.
    * Users login action
    * Login and posting via XML-RPC
    * PHP errors. (On the debug mode, it also save Warning and Notice)

=== Detail of saving logs ===

* WordPress
    * Core updates
    * Plugin/Theme updates
    * Language updates
* Post/Page/Attachment
    * Created
    * Updated
    * Deleted
* Plugin
    * Activated
    * Deactivated
* Theme
    * Switched
* User
    * Logged in
* XML-RPC
    * Authenticated
    * Created
    * Updated
    * Deleted
* PHP
    * Errors
    * Warnings (WP_DEBUG only)
    * Notices (WP_DEBUG only)

== Installation ==

1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==


== Changelog ==

= 1.0 =
* The first releasae.
