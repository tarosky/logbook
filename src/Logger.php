<?php

namespace Talog;

class Logger
{
	const post_type = 'talog';

	private $loggers = array();
	private $logs = array();

	public function __construct()
	{
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 11 );
		register_shutdown_function( array( $this, 'shutdown' ) );
	}

	/**
	 * Registers the logger to the specific hooks.
	 *
	 * @param string|array $hooks      An array of hooks to save log.
	 * @param callable $log     The callback function to return log message.
	 * @param callable $message The callback function to return long message of the log.
	 * @param string $log_level The Log level.
	 * @param int    $priority  An int value passed to `add_action()`.
	 * @param int    $accepted_args An int value passed to `add_action()`.
	 */
	public function register( $hooks, $log, $message = null,
								$log_level = null, $priority = 10, $accepted_args = 1 )
	{
		$this->loggers[] = array( $hooks, $log, $message, $log_level, $priority, $accepted_args );
	}

	public function plugins_loaded()
	{
		$self = $this;

		foreach ( $this->loggers as $logger ) {

			list( $hooks, $log, $message, $log_level, $priority, $accepted_args ) = $logger;

			/**
			 * Filters the log levels array to save logs.
			 *
			 * @param array $log_levels An array of the log levels.
			 *
			 * @reurn array
			 */
			$log_levels = apply_filters( 'talog_log_levels', Log_Level::get_all_levels() );
			if ( ! in_array( Log_Level::get_level( $log_level ), $log_levels ) ) {
				return;
			}

			if ( ! is_array( $hooks ) ) {
				$hooks = array( $hooks );
			}

			foreach ( $hooks as $hook ) {
				add_filter( $hook, function () use ( $self, $log, $message, $log_level ) {
					$args = func_get_args();

					$return = null;
					if ( ! empty( $args[0] ) ) {
						$return = $args[0];
					}

					if ( 'save_post' === current_filter() && 'talog' === get_post_type( $args[0] ) ) {
						return $return; // To prevent infinite loop.
					}

					$self->get_log( $log, $message, $log_level, $args );

					return $return;
				}, $priority, $accepted_args );
			}
		}
	}

	/**
	 * Callback function to save log.
	 *
	 * @param callable $log     The callback function to return log message.
	 * @param callable $message The callback function to return long message of the log.
	 * @param string $log_level The log level.
	 * @param array $additional_args An array which is passed from the callback function of the hook.
	 *
	 * @return int|\WP_Error
	 */
	public function get_log( $log, $message = null, $log_level = null, $additional_args = array() )
	{
		$log_level = Log_Level::get_level( $log_level );

		if ( defined('WP_CLI') && WP_CLI ) {
			$is_cli = true;
		} else {
			$is_cli = false;
		}

		$user = self::get_user();
		if ( empty( $user->ID ) ) {
			$user_id = 0;
		} else {
			$user_id = $user->ID;
		}

		$last_error = self::error_get_last();
		$current_hook = self::get_current_hook();

		$log_text = '';
		if ( ! empty( $log ) && is_callable( $log ) ) {
			$log_text = call_user_func( $log, array(
				'log_level' => $log_level,
				'last_error' => $last_error,
				'current_hook' => $current_hook,
				'user' => $user,
				'is_cli' => $is_cli,
				'additional_args' => $additional_args,
			) );
		}

		if ( empty( $log_text ) ) {
			return 0;
		}

		$message_text = '';
		if ( ! empty( $message ) && is_callable( $message ) ) {
			$message_text = call_user_func( $message, array(
				'log_level' => $log_level,
				'last_error' => $last_error,
				'current_hook' => $current_hook,
				'user' => $user,
				'is_cli' => $is_cli,
				'additional_args' => $additional_args,
			) );
		}

		$log = new Log();
		$log->set_title( $log_text );
		$log->set_content( $message_text );
		$log->set_user( intval( $user_id ) );
		$log->set_log_level( $log_level );
		$log->set_last_error( $last_error );
		$log->set_current_hook( $current_hook );
		$log->set_is_cli( $is_cli );

		$this->logs[] = $log;

		do_action( 'talog_after_hook', $log );
	}

	public function shutdown()
	{
		foreach ( $this->logs as $log_object ) {
			self::save_log( $log_object );
		}

		do_action( 'talog_after_save_log', $this->logs );
	}

	protected static function save_log( $log_object )
	{
		$log = $log_object->get_log();
		$post_id = wp_insert_post( array(
			'post_type'    => self::post_type,
			'post_title'   => $log->title,
			'post_content' => $log->content,
			'post_status'  => 'publish',
			'post_author'  => $log->user
		) );

		// Followings will be used for `orderby` for query.
		update_post_meta( $post_id, '_talog_log_level', $log->meta['log_level'] );
		update_post_meta( $post_id, '_talog_hook', $log->meta['hook'] );

		$log->meta['server_vars'] = $_SERVER;
		update_post_meta( $post_id, '_talog', $log->meta );
	}

	protected static function get_current_hook()
	{
		return current_filter();
	}

	protected static function get_user()
	{
		return wp_get_current_user();
	}

	protected static function error_get_last()
	{
		return error_get_last();
	}
}
