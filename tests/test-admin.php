<?php

class Talog_Admin_Test extends \WP_UnitTestCase
{
	function test_get_all_meta_values()
	{
		$post = $this->factory()->post->create_and_get( array( 'post_type' => 'talog' ) );
		update_post_meta( $post->ID, '_test', 'apple');

		$post = $this->factory()->post->create_and_get( array( 'post_type' => 'talog' ) );
		update_post_meta( $post->ID, '_test', 'orange');

		$post = $this->factory()->post->create_and_get( array( 'post_type' => 'talog' ) );
		update_post_meta( $post->ID, '_test', 'banana');

		$post = $this->factory()->post->create_and_get( array( 'post_type' => 'talog' ) );
		update_post_meta( $post->ID, '_test', 'banana');

		$values = Talog\Admin::get_meta_values( '_test' );
		sort( $values );

		$this->assertSame( array( 'apple', 'banana', 'orange' ), $values );
	}
}
