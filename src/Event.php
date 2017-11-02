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
			$logger->add_filter();
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
		$log = new Log();
		$log->set_label( $logger->get_label() );
		$log->set_log_level( $logger->get_log_level() );
		$log->update_meta( 'filter', $logger->get_hook_name() );

		call_user_func_array(
			array( $logger, 'log' ),
			array( $log, $additional_args )
		);

		if ( ! is_a( $log, 'Talog\Log' ) || ! $log->is_log() ) {
			return 0;
		}

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

		update_post_meta( $post_id, '_talog', $log->meta );
	}
}
