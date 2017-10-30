<?php

namespace Talog;

class Talog_Logger_Test extends \WP_UnitTestCase
{
	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_error_get_last()
	{
		$class = new \ReflectionClass('Talog\Logger');
		$method = $class->getMethod( 'error_get_last' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( null, array() );
		$this->assertSame( null, $result );

		echo @$error; // There is an error.
		$result = $method->invokeArgs( null, array() );
		$this->assertSame( 'Undefined variable: error', $result['message'] );
	}

	public function test_get_user()
	{
		$class = new \ReflectionClass('Talog\Logger');
		$method = $class->getMethod( 'get_user' );
		$method->setAccessible( true );

		// anonymous user
		$result = $method->invokeArgs( null, array() );
		$this->assertSame( 0, $result->ID );

		$this->set_current_user( 'administrator' );
		$result = $method->invokeArgs( null, array() );
		$this->assertSame( 'administrator', $result->roles[0] );

		$this->set_current_user( 'subscriber' );
		$result = $method->invokeArgs( null, array() );
		$this->assertSame( 'subscriber', $result->roles[0] );
	}

	public function test_get_current_hook()
	{
		$class = new \ReflectionClass('Talog\Logger');
		$method = $class->getMethod( 'get_current_hook' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( null, array() );
		$this->assertFalse( $result );

		add_action( 'test_hook', function() use ( $method ) {
			$result = $method->invokeArgs( null, array() );
			$this->assertSame( 'test_hook', $result );
			$GLOBALS['worked'] = 'OK';
		} );

		do_action( 'test_hook' );
		$this->assertSame( 'OK', $GLOBALS['worked'] );
	}

	public function test_save_log()
	{
		$talog = new Logger();
		$talog->register( array( 'test_hook-1', 'test_hook-2' ), function() {
			return 'Test hook was fired!';
		} );

		do_action( "plugins_loaded" );
		do_action( 'test_hook-1' );
		$post = $this->get_last_log();

		$this->assertSame( 'Test hook was fired!', $post->post_title );
		$this->assertSame( '0', $post->post_author );

		$meta = get_post_meta( $post->ID, '_talog', true );
		$this->assertSame( Log_Level::DEFAULT_LEVEL, $meta['log_level'] );
		$this->assertSame( 'test_hook-1', $meta['hook'] );

		do_action( 'test_hook-2' );
		$post = $this->get_last_log();

		$this->assertSame( 'Test hook was fired!', $post->post_title );
		$this->assertSame( '0', $post->post_author );

		$meta = get_post_meta( $post->ID, '_talog', true );
		$this->assertSame( Log_Level::DEFAULT_LEVEL, $meta['log_level'] );
		$this->assertSame( 'test_hook-2', $meta['hook'] );
	}

	public function test_save_log_simple()
	{
		$talog = new Logger();
		$talog->register(
			'custom_hook',
			function() {
				return 'Test hook was fired!';
			},
			function() {
				return 'Error message.';
			},
			'not-found'
		);

		do_action( "plugins_loaded" );
		do_action( 'custom_hook' );

		$post = $this->get_last_log();

		$this->assertSame( 'Test hook was fired!', $post->post_title );
		$this->assertSame( 'Error message.', $post->post_content );
		$this->assertSame( '0', $post->post_author );

		$meta = get_post_meta( $post->ID, '_talog', true );
		$this->assertSame( Log_Level::DEFAULT_LEVEL, $meta['log_level'] );
		$this->assertSame( 'custom_hook', $meta['hook'] );
	}

	public function test_save_log_simple_with_log_level()
	{
		$talog = new Logger();
		$talog->register(
			'custom_hook',
			function() {
				return 'Test hook was fired!';
			},
			function() {
				return 'Error message.';
			},
			Log_Level::DEBUG
		);

		do_action( "plugins_loaded" );
		do_action( 'custom_hook' );

		$post = $this->get_last_log();

		$this->assertSame( 'Test hook was fired!', $post->post_title );
		$this->assertSame( 'Error message.', $post->post_content );
		$this->assertSame( '0', $post->post_author );

		$meta = get_post_meta( $post->ID, '_talog', true );
		$this->assertSame( Log_Level::DEBUG, $meta['log_level'] );
		$this->assertSame( 'custom_hook', $meta['hook'] );
	}

	public function test_save_log_with_the_current_user()
	{
		$user = $this->set_current_user( 'administrator' );

		$talog = new Logger();
		$talog->register(
			'custom_hook',
			function() {
				return 'Test hook was fired!';
			},
			function() {
				return 'Error message.';
			},
			Log_Level::DEBUG
		);

		do_action( "plugins_loaded" );
		do_action( 'custom_hook' );

		$post = $this->get_last_log();

		$this->assertSame( 'Test hook was fired!', $post->post_title );
		$this->assertSame( 'Error message.', $post->post_content );
		$this->assertSame( "$user->ID", $post->post_author );

		$meta = get_post_meta( $post->ID, '_talog', true );
		$this->assertSame( Log_Level::DEBUG, $meta['log_level'] );
		$this->assertSame( 'custom_hook', $meta['hook'] );
	}

	public function test_filter_should_be_as_expected()
	{
		$talog = new Logger();
		$talog->register( array( 'custom_filter' ), function( $args ) {
			return json_encode( $args );
		} );

		do_action( "plugins_loaded" );
		$custom_filter = apply_filters( 'custom_filter', 'hello' );

		$this->assertSame( 'hello', $custom_filter );

		$post = $this->get_last_log();

		$this->assertArrayHasKey( 'log_level', json_decode( $post->post_title, true ) );
		$this->assertArrayHasKey( 'last_error', json_decode( $post->post_title, true ) );
		$this->assertArrayHasKey( 'current_hook', json_decode( $post->post_title, true ) );
	}

	public function test_logger_should_not_fired_with_empty_log()
	{
		$talog = new Logger();
		$talog->register( array( 'custom_filter' ), function( $args ) {
			return '';
		} );

		do_action( "plugins_loaded" );
		$custom_filter = apply_filters( 'custom_filter', 'hello' );

		$this->assertSame( 'hello', $custom_filter );

		$post = $this->get_last_log();

		$this->assertSame( array(), $post ); // Log should be empty.
	}

	public function test_logger_should_not_fired_with_filter_hook()
	{
		$talog = new Logger();
		$talog->register( array( 'custom_filter' ), function( $args ) {
			return '';
		}, null, Log_Level::WARN );

		add_filter( 'talog_log_levels', function( $args ) {
			return array( Log_Level::INFO );
		} );

		do_action( "plugins_loaded" );
		do_action( 'my_custom_hook' );

		$post = $this->get_last_log();
		$this->assertSame( array(), $post ); // Log should be empty.
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

		if ( ! empty( $posts ) ) {
			return $posts[0];
		} else {
			return array();
		}

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
