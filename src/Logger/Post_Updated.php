<?php

namespace Talog\Logger;
use Talog\Logger;

class Post_Updated extends Logger
{
	protected $label = 'Post';
	protected $hooks = array( 'post_updated' );
	protected $log_level = '\Talog\Level\Default_Level';
	protected $priority = 10;
	protected $accepted_args = 3;

	/**
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		list( $post_id, $post_after, $post_before ) = $additional_args;

		if ( 'publish' !== $post_after->post_status && 'publish' !== $post_before->post_status ) {
			return;
		}

		// Followings are always changed.
		unset( $post_after->post_modified_gmt, $post_after->post_modified,
			$post_before->post_modified_gmt, $post_before->post_modified );

		// Don't save log when it has no changes.
		if ( json_encode( $post_after ) !== json_encode( $post_before ) ) {
			if ( 'trash' === $post_after->post_status ) {
				$title = '#' . $post_id . ' "' . $post_after->post_title . '" was moved to trash.';
			} elseif ( 'publish' === $post_after->post_status && 'publish' !== $post_before->post_status ) {
				$title = '#' . $post_id . ' "' . $post_after->post_title . '" was published.';
			} else {
				$title = '#' . $post_id . ' "' . $post_after->post_title . '" was updated.';
			}

			$this->set_title( $title );

			$content = wp_text_diff( $post_before->post_title, $post_after->post_title );
			if ( $content ) {
				$this->add_content( 'Title', $content );
			}

			$content = wp_text_diff( $post_before->post_content, $post_after->post_content );
			if ( $content ) {
				$this->add_content( 'Contents', $content );
			}

			$content = wp_text_diff( $post_before->post_status, $post_after->post_status );
			if ( $content ) {
				$this->add_content( 'Status', $content );
			}

			$content = wp_text_diff( $post_before->post_date, $post_after->post_date );
			if ( $content ) {
				$this->add_content( 'Date', $content );
			}

			$this->add_content( 'URL', sprintf(
				'<a href="%1$s">%1$s</a>',
				esc_url( get_permalink( $post_id ) )
			) );
		}
	}
}
