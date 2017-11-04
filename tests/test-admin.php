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

		$values = self::getStaticMethod( 'get_meta_values', array( '_test' ) );
		sort( $values );

		$this->assertSame( array( 'apple', 'banana', 'orange' ), $values );
	}

	function test_get_level_name()
	{
		$result = self::getMethod( 'get_level_name', array( 'info' ) );
		$this->assertSame( 'info', $result );

		$result = self::getMethod( 'get_level_name', array( 'debug' ) );
		$this->assertSame( 'debug', $result );
	}

	protected static function getMethod( $method_name, $args = array() )
	{
		$class = new \ReflectionClass( '\Talog\Admin' );

		$method = $class->getMethod( $method_name );
		$method->setAccessible( true );

		$obj = new \Talog\Admin();
		return $method->invokeArgs( $obj, $args );
	}

	protected static function getStaticMethod( $method_name, $args = array() )
	{
		$class = new \ReflectionClass( '\Talog\Admin' );

		$method = $class->getMethod( $method_name );
		$method->setAccessible( true );

		return $method->invokeArgs( null, $args );
	}
}
