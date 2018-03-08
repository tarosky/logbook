<?php

if( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

define( 'SKIP_LOGGING', true );

$posts = get_posts( array(
	'post_type' => 'logbook'
) );

/**
 * @var $log \WP_Post
 */
foreach( $posts as $log ) {
	wp_delete_post( $log->ID, true );
}
