<?php

if( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();


$posts = get_posts( array(
	'post_type' => 'logbook',
	'posts_per_page' => -1,
) );

/**
 * @var $log \WP_Post
 */
foreach( $posts as $log ) {
	wp_delete_post( $log->ID, true );
}
