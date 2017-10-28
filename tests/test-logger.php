<?php

class Talog_Logger_Test extends WP_UnitTestCase
{
	public function test_error_get_last()
	{
		$logger = new Talog\Logger();
		$method = new \ReflectionMethod( get_class( $logger ), 'error_get_last' );
		$method->setAccessible( true );

		$result = $method->invoke( $logger );
		$this->assertSame( null, $result );

		echo @$error; // There is an error.
		$result = $method->invoke( $logger );
		$this->assertSame( 'Undefined variable: error', $result['message'] );
	}

	public function test_get_user()
	{
		$logger = new Talog\Logger();
		$method = new \ReflectionMethod( get_class( $logger ), 'get_user' );
		$method->setAccessible( true );

		// anonymous user
		$result = $method->invoke( $logger );
		$this->assertSame( 0, $result->ID );

		$this->set_current_user( 'administrator' );
		$result = $method->invoke( $logger );
		$this->assertSame( 'administrator', $result->roles[0] );

		$this->set_current_user( 'subscriber' );
		$result = $method->invoke( $logger );
		$this->assertSame( 'subscriber', $result->roles[0] );
	}

	public function test_get_current_hook()
	{
		$logger = new Talog\Logger();
		$method = new \ReflectionMethod( get_class( $logger ), 'get_current_hook' );
		$method->setAccessible( true );

		$result = $method->invoke( $logger );
		$this->assertFalse( $result );

		add_action( 'test_hook', function() use ( $logger, $method ) {
			$result = $method->invoke( $logger );
			$this->assertSame( 'test_hook', $result );
			$GLOBALS['worked'] = 'OK';
		} );

		do_action( 'test_hook' );
		$this->assertSame( 'OK', $GLOBALS['worked'] );
	}

	public function test_save_log()
	{
		$logger = new Talog\Logger();
		$logger->watch( array( 'test_hook-1', 'test_hook-2' ), 'Test hook was fired!' );

		do_action( 'test_hook-1' );
		$post = $this->get_last_log();

		$this->assertSame( 'Test hook was fired!', $post->post_title );
		$this->assertSame( '0', $post->post_author );

		$meta = get_post_meta( $post->ID, '_talog', true );
		$this->assertSame( 'normal', $meta['log_level'] );
		$this->assertSame( 'Undefined variable: error', $meta['last_error']['message'] );
		$this->assertSame( 'test_hook-1', $meta['hook'] );

		do_action( 'test_hook-2' );
		$post = $this->get_last_log();

		$this->assertSame( 'Test hook was fired!', $post->post_title );
		$this->assertSame( '0', $post->post_author );

		$meta = get_post_meta( $post->ID, '_talog', true );
		$this->assertSame( 'normal', $meta['log_level'] );
		$this->assertSame( 'Undefined variable: error', $meta['last_error']['message'] );
		$this->assertSame( 'test_hook-2', $meta['hook'] );
	}

	public function test_save_log_with_log_level()
	{
		$logger = new Talog\Logger();
		$logger->watch( array( 'test_hook-1', 'test_hook-2' ), 'Test hook was fired!', 'critical' );

		do_action( 'test_hook-1' );
		$post = $this->get_last_log();

		$this->assertSame( 'Test hook was fired!', $post->post_title );
		$this->assertSame( '0', $post->post_author );

		$meta = get_post_meta( $post->ID, '_talog', true );
		$this->assertSame( 'critical', $meta['log_level'] );
		$this->assertSame( 'Undefined variable: error', $meta['last_error']['message'] );
		$this->assertSame( 'test_hook-1', $meta['hook'] );
	}

	public function test_save_log_with_the_current_user()
	{
		$user = $this->set_current_user( 'administrator' );

		$logger = new Talog\Logger();
		$logger->watch( array( 'test_hook-1', 'test_hook-2' ), 'Test hook was fired!', 'critical' );

		do_action( 'test_hook-1' );
		$post = $this->get_last_log();

		$this->assertSame( 'Test hook was fired!', $post->post_title );
		$this->assertSame( $user->ID, intval( $post->post_author ) );

		$meta = get_post_meta( $post->ID, '_talog', true );
		$this->assertSame( 'critical', $meta['log_level'] );
		$this->assertSame( 'Undefined variable: error', $meta['last_error']['message'] );
		$this->assertSame( 'test_hook-1', $meta['hook'] );
	}

	public function test_save_log_with_callback_function()
	{
		$user = $this->set_current_user( 'administrator' );

		$logger = new Talog\Logger();
		$logger->watch( array( 'test_hook-1', 'test_hook-2' ), function( $args ) {
			return json_encode( $args );
		}, 'critical' );

		do_action( 'test_hook-1' );
		$post = $this->get_last_log();

		$this->assertArrayHasKey( 'log_level', json_decode( $post->post_title, true ) );
		$this->assertArrayHasKey( 'last_error', json_decode( $post->post_title, true ) );
		$this->assertArrayHasKey( 'current_hook', json_decode( $post->post_title, true ) );
		$this->assertSame( $user->ID, intval( $post->post_author ) );

		$meta = get_post_meta( $post->ID, '_talog', true );
		$this->assertSame( 'critical', $meta['log_level'] );
		$this->assertSame( 'Undefined variable: error', $meta['last_error']['message'] );
		$this->assertSame( 'test_hook-1', $meta['hook'] );
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

		/*
		 * Set $user as the current user
		 */
		wp_set_current_user( $user->ID, $user->user_login );

		return $user;
	}
}
