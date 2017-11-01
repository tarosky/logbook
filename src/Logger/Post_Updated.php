<?php

namespace Talog\Logger;
use Talog\Log;
use Talog\Log_Level;
use Talog\Logger;

class Post_Updated extends Logger
{
	protected $label = 'Post';
	protected $hooks = array( 'post_updated' );
	protected $log_level = Log_Level::DEFAULT_LEVEL;
	protected $priority = 10;
	protected $accepted_args = 3;

	/**
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param Log    $log             An instance of `Talog\Log`.
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( Log $log, $additional_args )
	{
		list( $post_id, $post_after, $post_before ) = $additional_args;

		// Followings are always changed.
		unset( $post_after->post_modified_gmt, $post_after->post_modified,
			$post_before->post_modified_gmt, $post_before->post_modified );

		// Don't save log when it has no changes.
		if ( json_encode( $post_after ) !== json_encode( $post_before ) ) {
			if ( 'trash' === $post_after->post_status ) {
				$title = '#' . $post_id . ' "' . $post_after->post_title . '" was moved to trash.';
			} else {
				$title = '#' . $post_id . ' "' . $post_after->post_title . '" was updated.';
			}

			$log->set_title( $title );
			$log->update_meta( 'post_before', $post_before );
			$log->update_meta( 'post_after', $post_after );
		}
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
		$post_after = $post_meta['post_after'];
		$post_before = $post_meta['post_before'];

		$title = wp_text_diff( $post_before->post_title, $post_after->post_title );
		if ( $title ) {
			$title = '<h2>Title</h2>' . $title;
		}

		$content = wp_text_diff( $post_before->post_content, $post_after->post_content );
		if ( $content ) {
			$content = '<h2>Contents</h2>' . $content;
		}

		$status = wp_text_diff( $post_before->post_status, $post_after->post_status );
		if ( $status ) {
			$status = '<h2>Status</h2>' . $status;
		}

		$date = wp_text_diff( $post_before->post_date, $post_after->post_date );
		if ( $date ) {
			$date = '<h2>Date</h2>' . $date;
		}

		$post->post_content = $title . $content . $status . $date;
	}
}
