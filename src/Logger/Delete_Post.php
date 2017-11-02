<?php

namespace Talog\Logger;
use Talog\Log;
use Talog\Logger;

class Delete_Post extends Logger
{
	protected $label = 'Post';
	protected $hooks = array( 'delete_post' );
	protected $log_level = '\Talog\Level\Default_Level';
	protected $priority = 10;
	protected $accepted_args = 1;

	/**
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param Log    $log             An instance of `Talog\Log`.
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( Log $log, $additional_args )
	{
		list( $post_id ) = $additional_args;
		$post_title = get_post( $post_id )->post_title;

		if ( empty( $post_title ) ) {
			$post_title = '(empty)';
		}

		$title = sprintf(
			'Post "%s" #%s was deleted.',
			esc_html( $post_title ),
			esc_html( $post_id )
		);

		$log->set_title( $title );
	}

	/**
	 * Set the properties to `\WP_Post` for the admin.
	 *
	 * @param \WP_Post $post     The post object.
	 * @param array   $post_meta The post meta of the `$post`.
	 * @return \WP_Post The `\WP_Post` object.
	 */
	public function admin( \WP_Post $post, $post_meta )
	{
		return $post;
	}
}
