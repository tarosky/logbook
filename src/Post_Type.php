<?php

namespace Talog;

class Post_Type
{
	const post_type = 'talog';

	public function register()
	{
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init()
	{
		register_post_type( self::post_type, array(
			'labels'            => array(
				'name'                => "Logs",
			),
			'public'            => false,
			'show_ui'           => true,
			'menu_icon'         => 'dashicons-list-view',
			'show_in_rest'      => true,
			'rest_base'         => 'log',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
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
		 * Filters the capabilities for the talog post type.
		 *
		 * @params array $capabilities An array of the capabilities.
		 * @return array An array of the filtered capabilities.
		 */
		return apply_filters( 'talog_log_capabilities', $capabilities );
	}
}
