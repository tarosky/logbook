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
	 * @param array $hooks      An array of hooks to save log.
	 * @param string $log       The log message.
	 * @param string $log_level The Log level.
	 * @param int    $priority  An int value passed to `add_action()`.
	 * @param int    $accepted_args An int value passed to `add_action()`.
	 */
	public function watch( $hooks, $log, $log_level = 'normal', $priority = 10, $accepted_args = 1 )
	{
		foreach ( $hooks as $hook ) {
			add_action( $hook, function() use ( $log, $log_level ) {
				$this->save( $log, $log_level, func_get_args() );
			}, $priority, $accepted_args );
		}
	}

	/**
	 * Callback function to save log.
	 *
	 * @param string|callable $log The log message or callback function that returns the log.
	 * @param string $log_level The log level.
	 * @param array $additional_args An array which is passed from the callback function of the hook.
	 *
	 * @return int|\WP_Error
	 */
	public function save( $log, $log_level = 'normal', $additional_args = array() )
	{
		$user = $this->get_user();
		$last_error = $this->error_get_last();
		$current_hook = $this->get_current_hook();
		if ( defined('WP_CLI') && WP_CLI ) {
			$is_cli = true;
		} else {
			$is_cli = false;
		}

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

		$post_id = wp_insert_post( array(
			'post_type' => self::post_type,
			'post_title' => $log,
			'post_status' => 'publish',
			'post_author' => intval( $user->ID )
		) );

		// `_talog_log_level` will be used for `orderby` for query.
		update_post_meta( $post_id, '_talog_log_level', $log_level );

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
