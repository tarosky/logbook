<?php

/**
 * Class Rest_Logs_Controller_Test
 *
 * @property \WP_REST_Server $server
 */
class Rest_Logs_Controller_Test extends WP_UnitTestCase
{
	protected $token;
	protected $server;

	public function setUp()
	{
		parent::setUp();

		global $wp_rest_server;
		$this->server = $wp_rest_server = new WP_REST_Server;
		do_action( 'rest_api_init' );

		self::getStaticMethod( '\LogBook\Admin', 'generate_token' );
		$this->token = get_option( 'logbook-tmp-token' );
	}

	public function test_auth()
	{
		$this->assertSame(
			get_option( 'logbook-api-token' ),
			sha1( $this->token )
		);

		$request = new WP_REST_Request( 'GET', '/logbook/v1/logs' );
		$request->set_header( 'X-LogBook-API-Token', $this->token );
		$rest = new LogBook\Rest_Logs_Controller( 'logbook' );
		$this->assertFalse( is_wp_error( $rest->permission_callback( $request ) ) );
		$this->assertTrue( !! $rest->permission_callback( $request ) );

		$request = new WP_REST_Request( 'GET', '/logbook/v1/logs' );
		$request->set_header( 'X-LogBook-API-Token', "xxxx" );
		$this->assertTrue( is_wp_error( $rest->permission_callback( $request ) ) );
	}

	public function test_get_unauthorized()
	{
		$request = new WP_REST_Request( 'GET', '/logbook/v1/logs' );
		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( 401, $response );

		$request = new WP_REST_Request( 'GET', '/logbook/v1/logs' );
		$request->set_header( 'X-LogBook-API-Token', 'xxxx' );
		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( 401, $response );
	}

	public function test_get_authorized()
	{
		$request = new WP_REST_Request( 'GET', '/logbook/v1/logs' );
		$request->set_header( 'X-LogBook-API-Token', $this->token );
		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( 200, $response );
	}

	public function test_get_authorized_as_admin()
	{
		$this->set_current_user( 'administrator' );
		$request = new WP_REST_Request( 'GET', '/logbook/v1/logs' );
		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( 200, $response );
	}

	public function test_get_authorized_as_editor()
	{
		$this->set_current_user( 'editor' );
		$request = new WP_REST_Request( 'GET', '/logbook/v1/logs' );
		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( 200, $response );
	}

	public function test_get_authorized_as_atuthor()
	{
		$roles = array( 'author', 'contributor', 'subscriber' );
		foreach ( $roles as $role ) {
			$this->set_current_user( $role );
			$request = new WP_REST_Request( 'GET', '/logbook/v1/logs' );
			$response = $this->server->dispatch( $request );
			$this->assertResponseStatus( 401, $response );
		}
	}

	protected static function getStaticMethod( $class, $method_name, $args = array() )
	{
		$class = new \ReflectionClass( $class );

		$method = $class->getMethod( $method_name );
		$method->setAccessible( true );

		return $method->invokeArgs( null, $args );
	}

	/**
	 * Add user and set the user as current user.
	 *
	 * @param  string $role administrator, editor, author, contributor ...
	 * @return int The user ID
	 */
	private function set_current_user( $role )
	{
		$user = $this->factory()->user->create_and_get( array(
			'role' => $role,
		) );

		wp_set_current_user( $user->ID, $user->user_login );

		return $user->ID;
	}

	/**
	 * @param int $status
	 * @param \WP_REST_Response $response
	 */
	protected function assertResponseStatus( $status, $response )
	{
		$this->assertEquals( $status, $response->get_status() );
	}

	/**
	 * @param array $data
	 * @param \WP_REST_Response $response
	 */
	protected function assertResponseData( $data, $response )
	{
		$response_data = $response->get_data();
		$tested_data = array();
		foreach( $data as $key => $value ) {
			if ( isset( $response_data[ $key ] ) ) {
				$tested_data[ $key ] = $response_data[ $key ];
			} else {
				$tested_data[ $key ] = null;
			}
		}
		$this->assertEquals( $data, $tested_data );
	}

	public function tearDown()
	{
		parent::tearDown();

		global $wp_rest_server;
		$wp_rest_server = null;
	}
}
