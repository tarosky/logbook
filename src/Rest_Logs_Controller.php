<?php

namespace LogBook;

use WP_REST_Server;
use WP_Error;

class Rest_Logs_Controller extends \WP_REST_Posts_Controller
{
	public function __construct( $post_type )
	{
		parent::__construct( $post_type );
		$this->post_type = $post_type;
		$this->namespace = 'logbook/v1';
		$this->rest_base = 'logs';
	}

	public function register_routes()
	{
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'permission_callback' ),
				'args'                => $this->get_collection_params(),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			'args' => array(
				'id' => array(
					'description' => __( 'Unique identifier for the log.' ),
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'permission_callback' ),
				'args'                => $this->get_collection_params(),
			),
		) );

		register_rest_route( $this->namespace, '/stats', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_stats' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			),
		) );
	}

	public function get_stats()
	{
		$data = array(
			'wp_version' => $GLOBALS['wp_version'],
			'php_version' => phpversion(),
		);

		$response = rest_ensure_response( $data );
		return $response;
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_item( $request )
	{
		$response = parent::get_item( $request );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response;
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_items( $request )
	{
		$response = parent::get_items( $request );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response;
	}

	public function prepare_item_for_response( $post, $request )
	{
		$log = Log::get_the_log( $post );
		unset( $log['meta']['cli-command'] ); // For security reason.
		$response = rest_ensure_response( $log );

		return $response;
	}

	public function check_read_permission( $post )
	{
		return true;
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return bool|WP_Error
	 */
	public function permission_callback( $request )
	{
		$token = get_option( 'logbook-api-token' );
		$request_token = $request->get_header( 'x_logbook_api_token' );

		if ( ! empty( $request_token ) ) {
			if ( $token === sha1( $request_token ) ) {
				return true;
			}
		}

		$post_type = get_post_type_object( $this->post_type );
		if ( current_user_can( $post_type->cap->edit_posts ) ) {
			return true;
		}

		return new WP_Error(
			'logbook_rest_error',
			'You don\'t have permission to access this API.',
			array( 'status' => 401 )
		);
	}
}
