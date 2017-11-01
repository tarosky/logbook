<?php

namespace Talog;

class Log
{
	private $log;

	public function __construct()
	{
		$this->log = new \stdClass;
		$this->log->title = '';
		$this->log->content = '';
		$this->log->user = self::get_user_id();
		$this->log->meta = array(
			'label' => 'General',
			'log_level' => Log_Level::DEFAULT_LEVEL,
			'hook' => self::get_current_hook(),
			'is_cli' => self::is_cli(),
			'server_vars' => $_SERVER,
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
		if ( ! empty( $label ) ) {
			$this->log->meta['label'] = $label;
		}
	}

	public function set_log_level( $log_level )
	{
		$this->log->meta['log_level'] = Log_Level::get_level( $log_level );
	}

	public function update_meta( $key, $value )
	{
		$this->log->meta[ $key ] = $value;
	}

	public function delete_meta( $key )
	{
		unset( $this->log->meta[ $key ] );
	}

	public function get_log()
	{
		if ( empty( $this->log->title ) || empty( $this->log->meta['hook'] ) ) {
			return new \WP_Error( 'Incorrect log object' );
		} else {
			return $this->log;
		}
	}

	public function is_log()
	{
		if ( empty( $this->log->title ) || empty( $this->log->meta['hook'] ) ) {
			return false;
		} else {
			return true;
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

	protected static function is_cli()
	{
		if ( defined('WP_CLI') && WP_CLI ) {
			return true;
		} else {
			return false;
		}
	}

	protected function get_user_id()
	{
		$user = self::get_user();
		if ( empty( $user->ID ) ) {
			return 0;
		} else {
			return $user->ID;
		}
	}
}
