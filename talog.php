<?php
/**
 * Plugin Name:     Talog
 * Plugin URI:      https://github.com/tarosky/talog
 * Description:     A logging plugin.
 * Author:          Takayuki Miyauchi
 * Author URI:
 * Text Domain:     talog
 * Domain Path:     /languages
 * Version:         100
 *
 * @package         Talog
 */

namespace Talog;
use \Miya\WP as WP;

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

add_action( 'init', 'Talog\activate_auto_update' );

function activate_auto_update() {
	$plugin_slug = plugin_basename( __FILE__ ); // e.g. `hello/hello.php`.
	$gh_user = 'tarosky';                      // The user name of GitHub.
	$gh_repo = 'talog';       // The repository name of your plugin.

	// Activate automatic update.
	new WP\GH_Auto_Updater( $plugin_slug, $gh_user, $gh_repo );
}

function plugins_loaded() {
	// Creates an instance of logger.
	$GLOBALS['talog'] = new Event();

	// Registers post type `talog`.
	$post_type = new Post_Type();
	$post_type->register();

	// Registers admin panel.
	if ( is_admin() ) {
		$admin = new Admin();
		$admin->register();
	}

	/**
	 * Filters the array of default loggers.
	 */
	$loggers = apply_filters( 'talog_default_loggers', array(
		'Talog\Logger\Activated_Plugin',
		'Talog\Logger\Delete_Post',
		'Talog\Logger\Last_Error',
		'Talog\Logger\Post_Updated',
		'Talog\Logger\Updated_Core',
		'Talog\Logger\Updated_Plugins',
		'Talog\Logger\WP_Login',
	) );

	foreach ( $loggers as $logger ) {
		init_log( $logger );
	}
}

add_action( 'plugins_loaded', 'Talog\plugins_loaded' );

/**
 * Registers the logger to the specific hooks.
 *
 * @param string $logger_class The `Talog\Logger\Logger` class.
 */
function init_log( $logger_class ) {
	if ( class_exists( $logger_class ) ) {
		$result = $GLOBALS['talog']->init_log( $logger_class );
		if ( is_wp_error( $result ) ) {
			wp_die( 'Incorrect `Talog\Logger` object.' );
		}
	} else {
		wp_die( '`' . $logger_class . '` not found.' );
	}
}
