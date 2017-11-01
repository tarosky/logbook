<?php

class Talog_Last_Error_Test extends \WP_UnitTestCase
{
	public function test_length_should_be_31()
	{
		$file = array(
			'file' => ABSPATH . 'wp-includes/functions.php',
			'line' => 20,
		);

		$result = Talog\Logger\Last_Error::get_a_part_of_file( $file );
		$this->assertSame( 31, count( explode( "\n", trim( $result ) ) ) );
	}

	public function test_length_should_be_16()
	{
		$file = array(
			'file' => ABSPATH . 'wp-includes/functions.php',
			'line' => 1,
		);

		$result = Talog\Logger\Last_Error::get_a_part_of_file( $file );
		$this->assertSame( 16, count( explode( "\n", trim( $result ) ) ) );
	}

	public function test_length_should_be_16_last()
	{
		$file_length = count( file( ABSPATH . 'wp-includes/functions.php' ) );
		$file = array(
			'file' => ABSPATH . 'wp-includes/functions.php',
			'line' => $file_length,
		);

		$result = Talog\Logger\Last_Error::get_a_part_of_file( $file );
		$this->assertSame( 16, count( explode( "\n", trim( $result ) ) ) );
	}

	public function test_log()
	{
		$obj = new Talog\Logger\Last_Error();

		echo @$e; // error;

		$GLOBALS['wp_current_filter'] = array( 'test' ); // Force `test` hook.

		$log = new Talog\Log();
		$obj->log( $log, array() );

		$res = $log->get_log();
		$this->assertSame( 'Undefined variable: e', $res->title );
		$this->assertSame( __FILE__, $res->meta['error']['file'] );
	}

	public function test_admin()
	{
		$obj = new Talog\Logger\Last_Error();
		$post = $this->factory()->post->create_and_get( array(
			'post_content' => '',
		) );

		echo @$e; // error;
		$error = error_get_last();

		$this->assertFalse( !! $post->post_content );

		$obj->admin( $post, array( 'error' => $error, 'error-file' => 'file' ) );
		$this->assertTrue( !! $post->post_content );
	}
}
