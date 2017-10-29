<?php

namespace Talog;

class Talog_Log_Level_Test extends \WP_UnitTestCase
{
	public function test_get_log_level()
	{
		$level = Log_Level::get_level();
		$this->assertSame( 'info', $level );

		$level = Log_Level::get_level( Log_Level::INFO );
		$this->assertSame( 'info', $level );

		$level = Log_Level::get_level( 'not-found' );
		$this->assertSame( 'info', $level );

		$level = Log_Level::get_level( Log_Level::TRACE );
		$this->assertSame( 'trace', $level );

		$level = Log_Level::get_level( Log_Level::DEBUG );
		$this->assertSame( 'debug', $level );

		$level = Log_Level::get_level( Log_Level::WARN );
		$this->assertSame( 'warn', $level );

		$level = Log_Level::get_level( Log_Level::ERROR );
		$this->assertSame( 'error', $level );

		$level = Log_Level::get_level( Log_Level::FATAL );
		$this->assertSame( 'fatal', $level );
	}
}
