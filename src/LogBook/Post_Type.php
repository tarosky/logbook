<?php

namespace LogBook;

final class Post_Type
{
	const post_type = 'logbook';

	public function register()
	{
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init()
	{
		register_post_type( self::post_type, array(
			'labels'            => array(
				'name'                => __( "Logs", 'logbook' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'menu_icon'         => 'dashicons-list-view',
			'show_in_rest'      => false,
			'capability_type'   => 'page',
			'capabilities'      => self::get_caps(),
			'map_meta_cap'      => false,
		) );
	}

	public static function get_caps()
	{
		$capabilities = array(
			'create_posts' => 'do_not_allow',
			'delete_posts' => 'do_not_allow',
		);

		/**
		 * Filters the capabilities for the logbook post type.
		 *
		 * @params array $capabilities An array of the capabilities.
		 * @return array An array of the filtered capabilities.
		 */
		return apply_filters( 'logbook_log_capabilities', $capabilities );
	}
}
