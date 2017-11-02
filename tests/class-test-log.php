<?php

namespace Hello;
use Talog;

class Test_Log extends Talog\Logger
{
	protected $label = 'Test';
	protected $hooks = array( 'test_hook' );
	protected $log_level = '\Talog\Level\Debug';
	protected $priority = 10;
	protected $accepted_args = 2;

	public function log( $additional_args ) {
		$GLOBALS['test-log'] = $additional_args;

		$this->set_title( 'hello' );
		$this->add_content( 'test', 'this is test!' );
	}
}
