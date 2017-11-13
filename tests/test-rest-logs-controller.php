<?php

class Rest_Logs_Controller_Test extends WP_UnitTestCase
{
	public function test_auth()
	{
		self::getStaticMethod( '\LogBook\Admin', 'generate_token' );

		$this->assertTrue( !! get_option( 'logbook-api-token' ) );
		$this->assertTrue( !! get_option( 'logbook-tmp-token' ) );
		$this->assertSame(
			get_option( 'logbook-api-token' ),
			sha1( get_option( 'logbook-tmp-token' ) )
		);

		$_SERVER['HTTP_X_LOGBOOK_API_TOKEN'] = get_option( 'logbook-tmp-token' );
		$rest = new LogBook\Rest_Logs_Controller( 'logbook' );
		$this->assertTrue( $rest->permission_callback() );

		$_SERVER['HTTP_X_LOGBOOK_API_TOKEN'] = get_option( 'logbook-tmp-token' );
		$rest = new LogBook\Rest_Logs_Controller( 'logbook' );
		$this->assertFalse( is_wp_error( $rest->permission_callback() ) );
		$this->assertTrue( !! $rest->permission_callback() );

		$_SERVER['HTTP_X_LOGBOOK_API_TOKEN'] = 'xxxx';
		$this->assertTrue( is_wp_error( $rest->permission_callback() ) );
	}

	protected static function getStaticMethod( $class, $method_name, $args = array() )
	{
		$class = new \ReflectionClass( $class );

		$method = $class->getMethod( $method_name );
		$method->setAccessible( true );

		return $method->invokeArgs( null, $args );
	}
}
