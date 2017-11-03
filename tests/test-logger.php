<?php

class Talog_Logger_Test extends \WP_UnitTestCase
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

		$log = new Talog\Log();
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

		$log = new Talog\Log();
		$logger->set_log( $log );
		$logger->log( array() );
		$log_data = $log->get_log();

		$this->assertSame( 'debug', $logger->get_log_level() );
		$this->assertSame( 'debug', $log_data->meta['log_level'] );

		$logger->set_log_level_by_class( 'Talog\Level\Error' );
		$this->assertSame( 'error', $log_data->meta['log_level'] );
	}

	/**
	 * Add user and set the user as current user.
	 *
	 * @param  string $role administrator, editor, author, contributor ...
	 * @return none
	 */
	private function set_current_user( $role )
	{
		$user = $this->factory->user->create_and_get( array(
			'role' => $role,
		) );

		wp_set_current_user( $user->ID, $user->user_login );

		return $user->ID;
	}
}
