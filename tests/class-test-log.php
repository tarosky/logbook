<?php

class Test_Log extends Talog\Logger
{
	protected $label = 'Test';
	protected $hooks = array( 'test_hook' );
	protected $log_level = Talog\Log_Level::DEBUG;
	protected $priority = 10;
	protected $accepted_args = 2;

	public function get_log( $additional_args ) {
		$GLOBALS['test-log'] = $additional_args;
		return 'test log';
	}

	public function get_message( $additional_args ) {
		$GLOBALS['test-message'] = $additional_args;
		return 'test message';
	}
}
