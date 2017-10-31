<?php
/**
 * Plugin Name:     Talog
 * Plugin URI:      https://github.com/tarosky/talog
 * Description:     A logging plugin.
 * Author:          Takayuki Miyauchi
 * Author URI:
 * Text Domain:     talog
 * Domain Path:     /languages
 * Version:         nightly
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
	$GLOBALS['talog'] = new Logger();

	// Registers post type `talog`.
	$post_type = new Post_Type();
	$post_type->register();

	// Registers admin panel.
	if ( is_admin() ) {
		$admin = new Admin();
		$admin->register();
	}

	$GLOBALS['talog']->init_log( 'Talog\Logger\Last_Error' );
}

add_action( 'plugins_loaded', 'Talog\plugins_loaded' );

///**
// * Registers the logger to the specific hooks.
// *
// * @param string       $label         The label of the log.
// * @param string|array $hooks         An array of hooks to save log.
// * @param callable     $log           The callback function to return log message.
// * @param callable     $message       The callback function to return long message of the log.
// * @param string       $log_level     The Log level like `Talog\Log_Level::INFO`. See `Talog\Log_Level`.
// * @param int          $priority      An int value passed to `add_action()`.
// * @param int          $accepted_args An int value passed to `add_action()`.
// */
//function watch( $label, $hooks, $log, $message = null, $log_level = null,
//									$priority = 10, $accepted_args = 1 ) {
//	call_user_func_array( array( $GLOBALS['talog'], 'register' ), func_get_args() );
//}
