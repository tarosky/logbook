<?php

namespace Talog\Logger;
use Talog\Log;
use Talog\Logger;

class Publish_Post extends Logger
{
	protected $label = 'Post';
	protected $hooks = array( 'publish_post', 'publish_page' );
	protected $log_level = '\Talog\Level\Default_Level';
	protected $priority = 10;
	protected $accepted_args = 2;

	/**
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param Log    $log             An instance of `Talog\Log`.
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		list( $post_id, $post ) = $additional_args;
		$title = '#' . $post_id . ' "' . $post->post_title . '" was published.';
		$log->set_title( $title );
		$log->update_meta( 'post_title', $post->post_title );
		$log->update_meta( 'post_id', $post_id );
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
		$post->post_content = $this->get_table( array(
			'ID' => esc_html( $post_meta['post_id'] ),
			'Title' => esc_html( $post_meta['post_title'] ),
			'URL' => esc_url( get_the_permalink( $post_meta['post_id'] ) )
		) );
	}
}
