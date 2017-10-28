<?php

class Talog_Default_Logger_Test extends WP_UnitTestCase
{
	public function test_log_insert_post()
	{
		$user = $this->set_current_user( 'editor' );
		$post = $this->factory()->post->create_and_get( array(
			'post_author' => $user->ID
		) );
		$last_log = $this->get_last_log();

		$this->assertSame( $user->ID, intval( $last_log->post_author ) );
		$this->assertRegExp( "/#{$post->ID}/", $last_log->post_title );
		$this->assertRegExp( "/\"{$post->post_title}\"/", $last_log->post_title );
		$meta = get_post_meta( $last_log->ID, '_talog', true );
		$this->assertSame( 'info', $meta['log_level'] );
		$this->assertSame( null, $meta['last_error'] );
		$this->assertSame( 'publish_post', $meta['hook'] );
		$this->assertSame( false, $meta['is_cli'] );
		$this->assertSame( 'publish_post', get_post_meta( $last_log->ID, '_talog_hook', true ) );
		$this->assertSame( 'info', get_post_meta( $last_log->ID, '_talog_log_level', true ) );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_log_insert_post_with_cli()
	{
		define( "WP_CLI", true );

		$user = $this->set_current_user( 'editor' );
		$post = $this->factory()->post->create_and_get( array(
			'post_author' => $user->ID
		) );
		$last_log = $this->get_last_log();

		$this->assertSame( $user->ID, intval( $last_log->post_author ) );
		$this->assertRegExp( "/#{$post->ID}/", $last_log->post_title );
		$this->assertRegExp( "/\"{$post->post_title}\"/", $last_log->post_title );
		$meta = get_post_meta( $last_log->ID, '_talog', true );
		$this->assertSame( 'info', $meta['log_level'] );
		$this->assertSame( null, $meta['last_error'] );
		$this->assertSame( 'publish_post', $meta['hook'] );
		$this->assertSame( true, $meta['is_cli'] );
	}

//	public function test_log_updated_option()
//	{
//		$user = $this->set_current_user( 'editor' );
//		$post = $this->factory()->post->create_and_get( array(
//			'post_author' => $user->ID
//		) );
//		$last_log = $this->get_last_log();
//
//		$this->assertSame( $user->ID, intval( $last_log->post_author ) );
//		$this->assertRegExp( "/#{$post->ID}/", $last_log->post_title );
//		$this->assertRegExp( "/\"{$post->post_title}\"/", $last_log->post_title );
//		$meta = get_post_meta( $last_log->ID, '_talog', true );
//		$this->assertSame( 'normal', $meta['log_level'] );
//		$this->assertSame( null, $meta['last_error'] );
//		$this->assertSame( 'publish_post', $meta['hook'] );
//		$this->assertSame( true, $meta['is_cli'] );
//	}

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
