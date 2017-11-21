<?php

namespace LogBook\Logger;
use LogBook\Logger;

class Delete_Post extends Logger
{
	protected $label = 'Post';
	protected $hooks = array( 'delete_post' );
	protected $log_level = \LogBook::DEFAULT_LEVEL;
	protected $priority = 10;
	protected $accepted_args = 1;

	/**
	 * Set the properties to the `LogBook\Log` object for the log.
	 *
	 * @param mixed $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		list( $post_id ) = $additional_args;
		$post = get_post( $post_id );

		if ( 'revision' === $post->post_type ) {
			return;
		}

		if ( empty( $post->post_title ) ) {
			$post->post_title = '(empty)';
		}

		$title = sprintf(
			__( '%s #%s "%s" was deleted.', 'logbook' ),
			esc_html( ucfirst( $post->post_type ) ),
			esc_html( $post_id ),
			esc_html( $post->post_title )
		);

		$post_url = get_permalink( $post_id );
		$content = $this->get_table( array(
			'Post Type' => $post->post_type,
			'Title' => $post->post_title,
			'URL' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( $post_url ),
				esc_url( $post_url )
			),
		) );

		$this->set_title( $title );
		$this->add_content( 'Summary', $content );
	}
}
