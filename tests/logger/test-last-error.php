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
}
