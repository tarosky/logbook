<?php

namespace Talog\Logger;
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
	 * Returns the log text.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 * @return string A text contents for the log that will be escaped automatically.
	 */
	public function get_log( $additional_args )
	{
		list( $post_id, $post_after, $post_before ) = $additional_args;

		// Followings are always changed.
		unset( $post_after->post_modified_gmt, $post_after->post_modified,
			$post_before->post_modified_gmt, $post_before->post_modified );

		// Don't save log when it has no changes.
		if ( json_encode( $post_after ) === json_encode( $post_before ) ) {
			return '';
		}

		return 'Updated "' . $post_after->post_title . '" #' . $post_id . '.';
	}

	/**
	 * Returns the long message for the log.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 * @return string A HTML contents for the log. You should escape as you need.
	 */
	public function get_message( $additional_args )
	{
		$post_after = $additional_args[1];
		$post_before = $additional_args[2];

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

		return $title . $content . $status . $date;
	}
}
