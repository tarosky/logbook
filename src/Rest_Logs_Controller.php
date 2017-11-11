<?php

namespace LogBook;

use WP_REST_Server;
use WP_Error;

class Rest_Logs_Controller extends \WP_REST_Posts_Controller
{
	public function __construct( $post_type ) {
		parent::__construct( $post_type );
		$this->namespace = 'logbook/v1';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'permission_callback' ),
				'args'                => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
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

		$response->data = array_map( array( $this, 'log_mapper' ), $response->data );

		return $response;
	}

	public function log_mapper( $p )
	{
		/**
		 * @var \WP_Post $post
		 */
		$post = get_post( $p['id'] );

		$log = array();
		$log['id'] = $post->ID;
		$log['date'] = $post->post_date_gmt;
		$log['log'] = $post->post_title;
		$log['content'] = json_decode( urldecode( $post->post_content ), true );

		if ( $post->post_author && $u = get_userdata( $post->post_author ) ) {
			$log['user'] = $u->user_login;
		} else {
			$log['user'] = '';
		}

		$log['label'] = get_post_meta( $p['id'], '_logbook_label', true );
		$log['level'] = get_post_meta( $p['id'], '_logbook_log_level', true );
		$log['meta'] = get_post_meta( $p['id'], '_logbook', true );

		return $log;
	}

	public function permission_callback()
	{
		$token = get_option( 'logbook_api_token' );

		if ( ! empty( $_SERVER['HTTP_X_LOGBOOK_API_TOKEN'] ) ) {
			if ( $token === sha1( $_SERVER['HTTP_X_LOGBOOK_API_TOKEN'] ) ) {
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
