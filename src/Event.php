<?php

namespace Talog;

class Event
{
	const post_type = 'talog';

	private $loggers = array();
	private $logs = array();

	public function __construct()
	{
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 11 );
		add_action( 'shutdown', array( $this, 'shutdown' ), 11 );
	}

	public function init_log( $logger_class )
	{
		if ( class_exists( $logger_class ) ) {
			$logger = new $logger_class();
			if ( is_a( $logger, 'Talog\Logger' ) ) {
				$this->loggers[] = $logger;
				return $this->loggers;
			}
		}

		return new \WP_Error( 'Incorrect `Talog\Logger` object' );
	}

	public function plugins_loaded()
	{
		$self = $this;

		foreach ( $this->loggers as $logger ) {
			/**
			 * Filters the log levels array to save logs.
			 *
			 * @param array $log_levels An array of the log levels.
			 *
			 * @reurn array
			 */
			$log_levels = apply_filters( 'talog_log_levels', Log_Level::get_all_levels() );

			if ( ! in_array( Log_Level::get_level( $logger->get_log_level() ), $log_levels ) ) {
				return;
			}

			foreach ( $logger->get_hooks() as $hook ) {
				add_filter( $hook, function () use ( $self, $logger ) {
					$args = func_get_args();

					$return = null;
					if ( ! empty( $args[0] ) ) {
						$return = $args[0];
					}

					if ( 'save_post' === current_filter() && 'talog' === get_post_type( $args[0] ) ) {
						return $return; // To prevent infinite loop.
					}

					$self->get_log( $logger, $args );

					return $return;
				}, $logger->get_priority(), $logger->get_accepted_args() );
			}
		}
	}

	/**
	 * Callback function to save log.
	 *
	 * @param Logger $logger          Talog\Logger object.
	 * @param array  $additional_args An array that is passed from WordPress hook.
	 *
	 * @return int|\WP_Error
	 */
	public function get_log( Logger $logger, $additional_args = array() )
	{
		$log_text = call_user_func_array(
			array( $logger, 'get_log' ),
			array( $additional_args )
		);

		if ( empty( $log_text ) ) {
			return 0;
		}

		$message_text = call_user_func_array(
			array( $logger, 'get_message' ),
			array( $additional_args )
		);

		$log = new Log();
		$log->set_label( $logger->get_label() );
		$log->set_title( $log_text );
		$log->set_content( $message_text );
		$log->set_log_level( Log_Level::get_level( $logger->get_log_level() ) );

		$this->logs[] = $log;

		do_action( 'talog_after_hook', $log );
	}

	public function shutdown()
	{
		foreach ( $this->logs as $log_object ) {
			$this->save_log( $log_object );
		}

		do_action( 'talog_after_save_log', $this->logs );
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
		update_post_meta( $post_id, '_talog_label', $log->meta['label'] );
		update_post_meta( $post_id, '_talog_log_level', $log->meta['log_level'] );

		$log->meta['server_vars'] = $_SERVER;
		update_post_meta( $post_id, '_talog', $log->meta );
	}
}
