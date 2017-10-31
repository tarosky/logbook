<?php

namespace Talog\Logger;
use Talog\Log_Level;
use Talog\Logger;

class Delete_Post extends Logger
{
	protected $label = 'Post';
	protected $hooks = array( 'delete_post' );
	protected $log_level = Log_Level::DEFAULT_LEVEL;
	protected $priority = 10;
	protected $accepted_args = 1;

	/**
	 * Returns the log text.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 * @return string A text contents for the log that will be escaped automatically.
	 */
	public function get_log( $additional_args )
	{
		list( $post_id ) = $additional_args;
		$post_title = get_post( $post_id )->post_title;

		if ( empty( $post_title ) ) {
			$post_title = '(empty)';
		}

		return sprintf(
			'Post "%s" #%s was deleted.',
			esc_html( $post_title ),
			esc_html( $post_id )
		);
	}

	/**
	 * Returns the long message for the log.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 * @return string A HTML contents for the log. You should escape as you need.
	 */
	public function get_message( $additional_args )
	{
		return "";
	}
}
