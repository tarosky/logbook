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

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

add_action( 'init', 'activate_autoupdate' );

function activate_autoupdate() {
	$plugin_slug = plugin_basename( __FILE__ ); // e.g. `hello/hello.php`.
	$gh_user = 'tarosky';                      // The user name of GitHub.
	$gh_repo = 'talog';       // The repository name of your plugin.

	// Activate automatic update.
	new Miya\WP\GH_Auto_Updater( $plugin_slug, $gh_user, $gh_repo );
}

$post_type = new Talog\Post_Type();
$post_type->register();

if ( is_admin() ) {
	$admin = new Talog\Admin();
	$admin->register();
}

$logger = new Talog\Logger();

$default_loggers = new Talog\Default_Logger();
$loggers = $default_loggers->get_loggers();

foreach ( $loggers as $log ) {
	call_user_func_array( array( $logger, 'watch' ), $log );
}
