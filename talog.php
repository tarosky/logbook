<?php
/**
 * Plugin Name:     Talog
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
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
