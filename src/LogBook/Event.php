<?php

namespace LogBook;

class Event
{
	const post_type = Post_Type::post_type;

	private $loggers = array();
	private $logs = array();

	public function __construct()
	{
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 11 );
		add_action( 'shutdown', array( $this, 'shutdown' ), 11 );
	}

	public static function get_instance()
	{
		static $instance;

		if ( ! $instance ) {
			$instance = new Event();
		}

		return $instance;
	}

	public function init_log( $logger_class )
	{
		if ( class_exists( $logger_class ) ) {
			/**
			 * @var Logger $logger
			 */
			$logger = new $logger_class();
			if ( is_a( $logger, 'LogBook\Logger' ) ) {
				$this->loggers[] = $logger;
				return $this->loggers;
			}
		}

		return new \WP_Error( 'Incorrect `LogBook\Logger` object' );
	}

	public function plugins_loaded()
	{
		$self = $this;

		/**
		 * @var Logger $logger
		 */
		foreach ( $this->loggers as $logger ) {
			foreach ( $logger->get_hooks() as $hook ) {
				add_filter( $hook, function () use ( $self, $logger ) {
					$args = func_get_args();

					$return = null;
					if ( ! empty( $args[0] ) ) {
						$return = $args[0];
					}

					if ( 'save_post' === current_filter()
					            && 'logbook' === get_post_type( $args[0] ) ) {
						return $return; // To prevent infinite loop.
					}

					$self->watch( $logger, $args );

					return $return;
				}, $logger->get_priority(), $logger->get_accepted_args() );
			}
		}
	}

	/**
	 * Callback function to save log.
	 *
	 * @param Logger $logger          LogBook\Logger object.
	 * @param array  $additional_args An array that is passed from WordPress hook.
	 */
	public function watch( Logger $logger, $additional_args = array() )
	{
		$log = new Log();

		call_user_func_array(
			array( $logger, 'set_log' ),
			array( $log )
		);

		call_user_func_array(
			array( $logger, 'log' ),
			array( $additional_args )
		);

		if ( ! is_a( $log, 'LogBook\Log' ) || ! $log->is_log() ) {
			return;
		}

		/**
		 * Filters the log levels that will be saved to log.
		 *
		 * @param array $active_levels
		 */
		$active_levels = apply_filters( 'logbook_active_levels', array(
			'fatal',
			'error',
			'warn',
			'info',
		) );
		if ( ! in_array( $log->get_log_level(), $active_levels ) ) {
			if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
				return;
			}
		}

		if ( $log->has_command_log() ) {
			$log->add_content( 'WP-CLI Command', sprintf(
				'<pre class="terminal">$ %s</pre>',
				esc_html( $log->get_command_log() )
			) );
		} else {
			$server_variables = array(
				'REMOTE_ADDR',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_CLIENT_IP',
				'HTTP_USER_AGENT',
				'HTTP_HOST',
				'REQUEST_URI',
			);
			/**
			 * Filters the keys of `$_SERVER` for the log.
			 *
			 * @param array $server_variables An array of the keys of `$_SERVER`.
			 */
			$server_variables = apply_filters(
				"logbook_log_server_variables",
				$server_variables
			);
			$log->add_content(
				'Environment Variables',
				$logger->get_server_variables_table( $server_variables )
			);
		}

		$this->logs[] = $log;
	}

	public function shutdown()
	{
		// Skip to save logs if `define( 'SKIP_LOGGING', true );` exists.
		if ( defined( 'SKIP_LOGGING' ) && true === SKIP_LOGGING ) {
			return;
		}

		foreach ( $this->logs as $log_object ) {
			$this->save_log( $log_object );
		}

		do_action( 'logbook_after_save_log', $this->logs );
	}

	private function save_log( Log $log_object )
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
		update_post_meta( $post_id, '_logbook_label', $log->meta['label'] );
		update_post_meta( $post_id, '_logbook_log_level', $log->meta['log_level'] );
		update_post_meta( $post_id, '_logbook_ip', $log->meta['ip'] );

		update_post_meta( $post_id, '_logbook', $log->meta );
	}
}
