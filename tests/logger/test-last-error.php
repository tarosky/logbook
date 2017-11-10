<?php

class LogBook_Last_Error_Test extends \WP_UnitTestCase
{
	public function test_length_should_be_31()
	{
		$file = array(
			'file' => ABSPATH . 'wp-includes/functions.php',
			'line' => 20,
		);

		$result = LogBook\Logger\Last_Error::get_a_part_of_file( $file );
		$this->assertSame( 31, count( explode( "\n", trim( $result ) ) ) );
	}

	public function test_length_should_be_16()
	{
		$file = array(
			'file' => ABSPATH . 'wp-includes/functions.php',
			'line' => 1,
		);

		$result = LogBook\Logger\Last_Error::get_a_part_of_file( $file );
		$this->assertSame( 16, count( explode( "\n", trim( $result ) ) ) );
	}

	public function test_length_should_be_16_last()
	{
		$file_length = count( file( ABSPATH . 'wp-includes/functions.php' ) );
		$file = array(
			'file' => ABSPATH . 'wp-includes/functions.php',
			'line' => $file_length,
		);

		$result = LogBook\Logger\Last_Error::get_a_part_of_file( $file );
		$this->assertSame( 16, count( explode( "\n", trim( $result ) ) ) );
	}

	public function test_log()
	{
		$obj = new LogBook\Logger\Last_Error();

		echo @$e; // error;

		$GLOBALS['wp_current_filter'] = array( 'test' ); // Force `test` hook.

		$log = new LogBook\Log();
		$obj->set_log( $log );
		$obj->log( array() );

		$res = $log->get_log();
		$this->assertSame( 'Undefined variable: e', $res->title );
		$this->assertTrue( !! $res->content );
	}
}
