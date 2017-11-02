<?php

class Talog_Logger_Test extends \WP_UnitTestCase
{
	public function test_logger_class_with_test_log()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		$logger = new Hello\Test_Log();

		$this->assertSame( 'debug', $logger->get_log_level() );
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
