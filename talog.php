<?php
/**
 * Plugin Name:     Talog
 * Plugin URI:      https://github.com/tarosky/talog
 * Description:     A logging plugin.
 * Author:          Takayuki Miyauchi
 * Author URI:
 * Text Domain:     talog
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Talog
 */

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

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
