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
				array( $this, 'publish_post' ),          // Callback function for log.
				'',                                      // Callback function for long message.
				'normal',                                // Log level.
				10,                                      // Priority.
				2,                                       // Number of accepted args.
			),
			array(
				array( 'activated_plugin', 'deactivated_plugin' ),
				array( $this, 'activated_plugin' ),
				'',
				'high',
				10,
				1,
			),
			array(
				array( 'updated_option' ),
				array( $this, 'updated_option_log' ),
				array( $this, 'updated_option_message' ),
				'normal',
				10,
				3,
			),
			array(
				array( 'shutdown' ),
				array( $this, 'shutdown' ),
				'',
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

	public function updated_option_log( $args )
	{
		$key = $args['additional_args'][0];
		$old = $args['additional_args'][1];
		$new = $args['additional_args'][2];

		if ( is_array( $old ) || is_array( $new )
		            || is_object( $old ) || is_object( $new ) ) {
			return sprintf(
				'Option "%s" had been updated.',
				$key
			);
		} else {
			return sprintf(
				'Option "%s" had been updated from "%s" to "%s".',
				$key,
				$old,
				$new
			);
		}
	}

	public function updated_option_message( $args )
	{
		$old = $args['additional_args'][1];
		$new = $args['additional_args'][2];

		if ( is_array( $old ) || is_array( $new )
				     || is_object( $old ) || is_object( $new ) ) {
			$old = json_encode( $old, JSON_PRETTY_PRINT );
			$new = json_encode( $new, JSON_PRETTY_PRINT );

			return wp_text_diff( $old, $new );
		}

		return "";
	}

	public function shutdown( $args )
	{
		if ( $args['last_error'] ) {
			return $last_error = $args['last_error']['message'];
		} else {
			return null;
		}
	}
}
