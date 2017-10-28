<?php

namespace Talog;

class Default_Logger
{
	public function __construct() {}

	public function get_loggers()
	{
		// Arrays that will be passed to `Talog\Logger\watch()`.
		$loggers = array(
			array(
				array( 'publish_post', 'publish_page' ), // Hooks.
				array( $this, 'publish_post' ),          // Callback function.
				'normal',                                // Log level.
				10,                                      // Priority.
				2,                                       // Number of accepted args.
			),
			array(
				array( 'activated_plugin', 'deactivated_plugin' ),
				array( $this, 'activated_plugin' ),
				'high',
				10,
				1,
			),
		);

		return apply_filters( 'talog_default_loggers', $loggers );
	}

	public function publish_post( $args )
	{
		$post_id = $args['additional_args'][0];
		$post = $args['additional_args'][1];
		return 'Published "' . $post->post_title . '" #' . $post_id . '.';
	}

	public function activated_plugin( $args )
	{
		$plugin = $args['additional_args'][0];
		if ( 'activated_plugin' === $args['current_hook'] ) {
			return 'Plugin "' . dirname( $plugin ) . '" had been activated.';
		} else {
			return 'Plugin "' . dirname( $plugin ) . '" had been deactivated.';
		}
	}
}
