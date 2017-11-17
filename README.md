# logbook

[![Build Status](https://travis-ci.org/tarosky/logbook.svg?branch=master)](https://travis-ci.org/tarosky/logbook)

A logging plugin for WordPress. You can see what changed and who changed it.

Download: https://wordpress.org/plugins/logbook/

## It is logging:

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

## Customizing

### Add your custom log events

1. Create a class that extends the `\LogBook\Logger` class.
2. Load the class by `\LogBook\init_log()` like following.

```
add_action( 'plugins_loaded', function() {
	require_once dirname( __FILE__ ) . '/path/to/example.php';
	\LogBook\init_log( 'Hello\Example' );
} );
```

* [The example class is here.](https://github.com/tarosky/logbook/blob/master/example/Example.php)
* See also [defaults](https://github.com/tarosky/logbook/tree/master/src/Logger).

## Screenshot

### List of logs

![](https://www.evernote.com/l/ABUg-wL0wbtAFoQ8dTuN-206ZVeKmSk2NwgB/image.png)

### Detail screen of the log

Updated post:

![](https://www.evernote.com/l/ABWIQoGQcxdAnaPmKKVHawUxZ3UIJfTs64EB/image.png)

Last error of PHP:

![](https://www.evernote.com/l/ABW7wljExtpNLq2XZ5p72-zKkH7PQ6FBYxQB/image.png)
