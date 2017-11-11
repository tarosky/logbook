<?php
/**
 * Plugin Name:     LogBook
 * Plugin URI:      https://github.com/tarosky/logbook
 * Description:     A logging plugin.
 * Author:          Takayuki Miyauchi
 * Author URI:      https://tarosky.co.jp/
 * Text Domain:     logbook
 * Domain Path:     /languages
 * Version:         nightly
 *
 * @package         LogBook
 */

namespace LogBook;

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

function plugins_loaded() {
	// Registers post type `logbook`.
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
	$loggers = apply_filters( 'logbook_default_loggers', array(
		'LogBook\Logger\Activated_Extensions',
		'LogBook\Logger\Delete_Post',
		'LogBook\Logger\Last_Error',
		'LogBook\Logger\Post_Updated',
		'LogBook\Logger\Updated_Core',
		'LogBook\Logger\Updated_Extensions',
		'LogBook\Logger\WP_Login',
		'LogBook\Logger\XML_RPC',
	) );

	foreach ( $loggers as $logger ) {
		init_log( $logger );
	}

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		\WP_CLI::add_command( 'log', 'LogBook\CLI' );
	}
}

add_action( 'plugins_loaded', 'LogBook\plugins_loaded' );

/**
 * Registers the logger to the specific hooks.
 *
 * @param string $logger_class The `LogBook\Logger\Logger` class.
 */
function init_log( $logger_class ) {
	if ( class_exists( $logger_class ) ) {
		$result = Event::get_instance()->init_log( $logger_class );
		if ( is_wp_error( $result ) ) {
			wp_die( 'Incorrect `LogBook\Logger` object.' );
		}
	} else {
		wp_die( '`' . $logger_class . '` not found.' );
	}
}
