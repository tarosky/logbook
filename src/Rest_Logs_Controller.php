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

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'permission_callback' ),
				'args'                => $this->get_collection_params(),
			),
		) );
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

	public function check_read_permission( $post ) {
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

		return new WP_Error(
			'logbook_rest_error',
			'You don\'t have permission to access this API.',
			array( 'status' => 401 )
		);
	}
}
