=== Logbook ===
Contributors: miyauchi, tarosky
Tags: log, event, security
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

* Super lightweight and it doesn't affect site's performances.
* It has passed over 100 patterns unit test, you can use it with confidence in the enterprise.
* Extensible event saving logs, so you can develop own add-ons for collecting it.
* Saving logs the following activities on WordPress
    * Publish, update or delete published posts.
    * Activate or deactivate plugins or themes.
    * Updating WordPress core, plugins, language files.
    * Users login action
    * Login and posting via XML-RPC
    * PHP errors (On the debug mode, it also save Warning and Notice)
* WP-CLI command ready.

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

=== WP-CLI ===

Get a list of logs.

`
$ wp log list --posts_per_page=5
`

Get a list of specific level of logs.

`
$ wp list --level=error --posts_per_page=5
`

=== Issues ===

[https://github.com/tarosky/logbook](https://github.com/tarosky/logbook)

== Installation ==

1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. List of logs.
2. Diff of post.
3. Error of plugin.

== Changelog ==

You can see all changelog on GitHub.

https://github.com/tarosky/logbook/releases
