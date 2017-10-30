<?php

namespace Talog;

class Log
{
	private $log;

	public function __construct() {
		$this->log = new \stdClass;

		$this->log->title = '';
		$this->log->content = '';
		$this->log->user = 0;
		$this->log->meta = array();
	}

	public function set_title( $title )
	{
		$this->log->title = $title;
	}

	public function set_content( $content )
	{
		$this->log->content = $content;
	}

	public function set_user( $user )
	{
		$this->log->user = intval( $user );
	}

	public function set_label( $label )
	{
		$this->log->meta['label'] = $label;
	}

	public function set_log_level( $log_level )
	{
		$this->log->meta['log_level'] = Log_Level::get_level( $log_level );
	}

	public function set_last_error( $last_error )
	{
		$this->log->meta['last_error'] = $last_error;
	}

	public function set_current_hook( $current_hook )
	{
		$this->log->meta['hook'] = $current_hook;
	}

	public function set_is_cli( $is_cli )
	{
		$this->log->meta['is_cli'] = (bool) $is_cli;
	}

	public function get_log()
	{
		return $this->log;
	}
}
