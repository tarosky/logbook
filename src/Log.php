<?php

namespace Talog;

class Log
{
	private $log;

	public function __construct() {
		$this->log = new \stdClass;

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

		$this->log->title = '';
		$this->log->content = '';
		$this->log->user = $user_id;
		$this->log->meta = array(
			'label' => 'General',
			'log_level' => Log_Level::DEFAULT_LEVEL,
			'hook' => self::get_current_hook(),
			'is_cli' => $is_cli,
		);
	}

	public function set_title( $title )
	{
		$this->log->title = $title;
	}

	public function set_content( $content )
	{
		$this->log->content = $content;
	}

	public function set_label( $label )
	{
		$this->log->meta['label'] = $label;
	}

	public function set_log_level( $log_level )
	{
		$this->log->meta['log_level'] = Log_Level::get_level( $log_level );
	}

	public function get_log()
	{
		if ( empty( $this->log->title ) || empty( $this->log->meta['hook'] ) ) {
			return new \WP_Error( 'Incorrect log object' );
		} else {
			return $this->log;
		}
	}

	protected static function get_current_hook()
	{
		return current_filter();
	}

	protected static function get_user()
	{
		return wp_get_current_user();
	}
}
