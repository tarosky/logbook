<?php

namespace Talog;

use function foo\func;

class Logger
{
	const post_type = 'talog';

	public function __construct()
	{
		// Nothing to do for now.
	}

	/**
	 * Registers the logger to the specific hooks.
	 *
	 * @param string|array $hooks      An array of hooks to save log.
	 * @param string|callable $log       The log message.
	 * @param string|callable $message   The long message of the log.
	 * @param string $log_level The Log level.
	 * @param int    $priority  An int value passed to `add_action()`.
	 * @param int    $accepted_args An int value passed to `add_action()`.
	 */
	public function watch( $hooks, $log, $message = '', $log_level = 'info', $priority = 10, $accepted_args = 1 )
	{
		if ( ! is_array( $hooks ) ) {
			$hooks = array( $hooks );
		}

		foreach ( $hooks as $hook ) {
			add_action( $hook, function() use ( $log, $message, $log_level ) {
				$this->save( $log, $message, $log_level, func_get_args() );
			}, $priority, $accepted_args );
		}
	}

	/**
	 * Callback function to save log.
	 *
	 * @param string|callable $log The log message or callback function that returns the log.
	 * @param string|callable $message   The long message of the log.
	 * @param string $log_level The log level.
	 * @param array $additional_args An array which is passed from the callback function of the hook.
	 *
	 * @return int|\WP_Error
	 */
	public function save( $log, $message = '', $log_level = 'info', $additional_args = array() )
	{
		if ( defined('WP_CLI') && WP_CLI ) {
			$is_cli = true;
		} else {
			$is_cli = false;
		}

		$user = $this->get_user();
		if ( empty( $user->ID ) ) {
			$user_id = 0;
		} else {
			$user_id = $user->ID;
		}

		$last_error = $this->error_get_last();
		$current_hook = $this->get_current_hook();

		if ( is_callable( $log ) ) {
			$log = call_user_func( $log, array(
				'log_level' => $log_level,
				'last_error' => $last_error,
				'current_hook' => $current_hook,
				'user' => $user,
				'is_cli' => $is_cli,
				'additional_args' => $additional_args,
			) );
		}

		if ( empty( $log ) ) {
			return 0;
		}

		if ( is_callable( $message ) ) {
			$message = call_user_func( $message, array(
				'log_level' => $log_level,
				'last_error' => $last_error,
				'current_hook' => $current_hook,
				'user' => $user,
				'is_cli' => $is_cli,
				'additional_args' => $additional_args,
			) );
		}

		$post_id = wp_insert_post( array(
			'post_type' => self::post_type,
			'post_title' => $log,
			'post_content' => $message,
			'post_status' => 'publish',
			'post_author' => intval( $user_id )
		) );

		// Followings will be used for `orderby` for query.
		update_post_meta( $post_id, '_talog_log_level', $log_level );
		update_post_meta( $post_id, '_talog_hook', $current_hook );

		update_post_meta( $post_id, '_talog', array(
			'log_level' => $log_level,
			'last_error' => $last_error,
			'hook' => $current_hook,
			'is_cli' => $is_cli,
		) );

		return $post_id;
	}

	protected function get_current_hook()
	{
		return current_filter();
	}

	protected function get_user()
	{
		return wp_get_current_user();
	}

	protected function error_get_last()
	{
		return error_get_last();
	}
}
