<?php

class Talog_Logger_Test extends \WP_UnitTestCase
{
	public function test_logger_class()
	{
		$logger = new \Talog\Logger();
		$result = $logger->init_log( 'hello' );
		$this->assertTrue( is_a( $result, 'WP_Error' ) );
	}

	public function test_logger_class_with_test_log()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';

		$GLOBALS['test-log'] = false;
		$GLOBALS['test-message'] = false;
		$user_id = $this->set_current_user( 'editor' );

		$logger = new \Talog\Logger();
		$result = $logger->init_log( 'Test_Log' );

		$this->assertTrue( is_array( $result ) );
		$this->assertTrue( is_a( $result[0], 'Talog\Logger\Logger' ) );

		do_action( 'plugins_loaded' );
		$this->assertFalse( $GLOBALS['test-log'] );
		$this->assertFalse( $GLOBALS['test-message'] );

		do_action( 'test_hook', 'foo', 'bar' );
		$this->assertSame( array( 'foo', 'bar' ), $GLOBALS['test-log'] );
		$this->assertSame( array( 'foo', 'bar' ),  $GLOBALS['test-message'] );

		$logger->shutdown();
		$last_log = $this->get_last_log();
		$this->assertSame( 'test log', $last_log->post_title );
		$this->assertSame( 'test message', $last_log->post_content );
		$this->assertSame( "$user_id", $last_log->post_author );
		$this->assertSame( 'publish', $last_log->post_status );

		$meta = get_post_meta( $last_log->ID, '_talog', true );
		$this->assertSame( 'Test', $meta['label'] );
		$this->assertSame( \Talog\Log_Level::DEBUG, $meta['log_level'] );
		$this->assertSame( 'Test', $meta['label'] );
		$this->assertSame( 'test_hook', $meta['hook'] );
		$this->assertSame( false, $meta['is_cli'] );
		$this->assertTrue( is_array( $meta['server_vars'] ) );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_logger_class_with_test_log_with_cli()
	{
		require_once dirname( __FILE__ ) . '/class-test-log.php';
		define( 'WP_CLI', true );

		$GLOBALS['test-log'] = false;
		$GLOBALS['test-message'] = false;
		$user_id = $this->set_current_user( 'editor' );

		$logger = new \Talog\Logger();
		$result = $logger->init_log( 'Test_Log' );

		$this->assertTrue( is_array( $result ) );
		$this->assertTrue( is_a( $result[0], 'Talog\Logger\Logger' ) );

		do_action( 'plugins_loaded' );
		$this->assertFalse( $GLOBALS['test-log'] );
		$this->assertFalse( $GLOBALS['test-message'] );

		do_action( 'test_hook', 'foo', 'bar' );
		$this->assertSame( array( 'foo', 'bar' ), $GLOBALS['test-log'] );
		$this->assertSame( array( 'foo', 'bar' ),  $GLOBALS['test-message'] );

		$logger->shutdown();
		$last_log = $this->get_last_log();
		$this->assertSame( 'test log', $last_log->post_title );
		$this->assertSame( 'test message', $last_log->post_content );
		$this->assertSame( "$user_id", $last_log->post_author );
		$this->assertSame( 'publish', $last_log->post_status );

		$meta = get_post_meta( $last_log->ID, '_talog', true );
		$this->assertSame( 'Test', $meta['label'] );
		$this->assertSame( \Talog\Log_Level::DEBUG, $meta['log_level'] );
		$this->assertSame( 'Test', $meta['label'] );
		$this->assertSame( 'test_hook', $meta['hook'] );
		$this->assertSame( true, $meta['is_cli'] );
		$this->assertTrue( is_array( $meta['server_vars'] ) );
	}

	/**
	 * Get the last post from the talog post-type.
	 *
	 * @return mixed An WP_Post object.
	 */
	private function get_last_log()
	{
		$posts = get_posts( array(
			'post_type' => 'talog',
			'order' => 'DESC',
			'orderby' => 'post_date_gmt',
			'posts_per_page' => 1,
		) );

		return $posts[0];
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
