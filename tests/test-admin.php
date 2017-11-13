<?php

class LogBook_Admin_Test extends \WP_UnitTestCase
{
	public function test_get_all_meta_values()
	{
		$post = $this->factory()->post->create_and_get( array( 'post_type' => 'logbook' ) );
		update_post_meta( $post->ID, '_test', 'apple');

		$post = $this->factory()->post->create_and_get( array( 'post_type' => 'logbook' ) );
		update_post_meta( $post->ID, '_test', 'orange');

		$post = $this->factory()->post->create_and_get( array( 'post_type' => 'logbook' ) );
		update_post_meta( $post->ID, '_test', 'banana');

		$post = $this->factory()->post->create_and_get( array( 'post_type' => 'logbook' ) );
		update_post_meta( $post->ID, '_test', 'banana');

		$values = self::getStaticMethod( 'get_meta_values', array( '_test' ) );
		sort( $values );

		$this->assertSame( array( 'apple', 'banana', 'orange' ), $values );
	}

	public function test_get_level_name()
	{
		$result = self::getMethod( 'get_level_name', array( 'info' ) );
		$this->assertSame( 'info', $result );

		$result = self::getMethod( 'get_level_name', array( 'debug' ) );
		$this->assertSame( 'debug', $result );
	}

	public function test_token()
	{
		self::getStaticMethod( 'generate_token' );

		$this->assertTrue( !! get_option( 'logbook-api-token' ) );
		$this->assertTrue( !! get_option( 'logbook-tmp-token' ) );
		$this->assertSame(
			get_option( 'logbook-api-token' ),
			sha1( get_option( 'logbook-tmp-token' ) )
		);

		$settings = new LogBook\Admin\Settings();
		ob_start();
		$settings->display();
		ob_end_clean();
		$this->assertFalse( get_option( 'logbook-tmp-token' ) );
	}

	protected static function getMethod( $method_name, $args = array() )
	{
		$class = new \ReflectionClass( '\LogBook\Admin' );

		$method = $class->getMethod( $method_name );
		$method->setAccessible( true );

		$obj = new \LogBook\Admin();
		return $method->invokeArgs( $obj, $args );
	}

	protected static function getStaticMethod( $method_name, $args = array() )
	{
		$class = new \ReflectionClass( '\LogBook\Admin' );

		$method = $class->getMethod( $method_name );
		$method->setAccessible( true );

		return $method->invokeArgs( null, $args );
	}
}
