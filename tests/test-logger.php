<?php

class LogBook_Logger_Test extends \WP_UnitTestCase
{
	public function test_logger_class_with_test_log()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		$logger = new Hello\Test_Log();

		$this->assertSame( 'debug', $logger->get_log_level() );
	}

	public function test_logger_class_with_test_log_with_log_class()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		$logger = new Hello\Test_Log();

		$GLOBALS['wp_current_filter'] = array( 'activated_plugin' );

		$log = new LogBook\Log();
		$logger->set_log( $log );
		$logger->log( array() );

		$this->assertSame( 'debug', $logger->get_log_level() );

		$log_data = $log->get_log();

		$this->assertSame( 'hello', $log_data->title );
		$content = json_decode( urldecode( $log_data->content ), true );
		$this->assertSame( array(
			array(
				'title' => 'test',
				'content' => 'this is test!'
			),
		), $content );
	}

	public function test_logger_class_with_test_log_level()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		$logger = new Hello\Test_Log();

		$GLOBALS['wp_current_filter'] = array( 'activated_plugin' );

		$log = new LogBook\Log();
		$logger->set_log( $log );
		$logger->log( array() );
		$log_data = $log->get_log();

		$this->assertSame( 'debug', $logger->get_log_level() );
		$this->assertSame( 'debug', $log_data->meta['log_level'] );

		$logger->set_level( LogBook::ERROR );
		$this->assertSame( 'error', $log_data->meta['log_level'] );
	}

	public function test_logger_class_get_label()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		$logger = new Hello\Test_Log();

		$GLOBALS['wp_current_filter'] = array( 'activated_plugin' );

		$log = new LogBook\Log();
		$logger->set_log( $log );
		$logger->log( array() );
		$log_data = $log->get_log();

		$this->assertSame( $logger->get_label(), $log_data->meta['label'] );
	}

	public function test_logger_class_get_hooks()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		$logger = new Hello\Test_Log();

		$this->assertSame( array( 'test_hook' ), $logger->get_hooks() );
	}

	public function test_logger_class_get_log_level()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		$logger = new Hello\Test_Log();

		$this->assertSame( 'debug', $logger->get_log_level() );
	}

	public function test_logger_class_get_priority()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		$logger = new Hello\Test_Log();

		$this->assertSame( 10, $logger->get_priority() );
	}

	public function test_logger_class_get_server_variables_table()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		$logger = new Hello\Test_Log();

		$this->assertSame( null, $logger->get_server_variables_table( array() ) );
		$table = $logger->get_server_variables_table( array(
			'HTTP_HOST',
			'UNDEFINED',
			'SCRIPT_FILENAME',
		) );

		$this->assertTrue( 0 < strpos( $table, 'HTTP_HOST' ) );
		$this->assertTrue( 0 < strpos( $table, 'SCRIPT_FILENAME' ) );
		$this->assertTrue( 0 < strpos( $table, 'phpunit' ) );
		$this->assertTrue( 0 < strpos( $table, 'UNDEFINED' ) );
	}

	public function test_logger_class_get_table()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		$logger = new Hello\Test_Log();

		$this->assertSame( null, $logger->get_server_variables_table( array() ) );
		$table = $logger->get_table( array(
			'key1' => 'val1',
			'key2' => '<a href="#hello">val2</a>',
			'key3' => '<script>val3</script>',
		) );

		$this->assertTrue( 0 < strpos( $table,
				'<th>key1</th><td>val1</td>' ) );
		$this->assertTrue( 0 < strpos( $table,
				'<th>key2</th><td><a href="#hello">val2</a></td>' ) );
		$this->assertTrue( 0 < strpos( $table,
				'<th>key3</th><td>val3</td>' ) );
	}
}
