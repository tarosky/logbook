<?php

namespace Talog\Logger;
use Talog\Log_Level;
use Talog\Logger;

class Publish_Post extends Logger
{
	protected $label = 'Post';
	protected $hooks = array( 'publish_post', 'publish_page' );
	protected $log_level = Log_Level::DEFAULT_LEVEL;
	protected $priority = 10;
	protected $accepted_args = 2;

	/**
	 * Returns the log text.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 * @return string A text contents for the log that will be escaped automatically.
	 */
	public function get_log( $additional_args )
	{
		list( $post_id, $post ) = $additional_args;
		return esc_html( '"' . $post->post_title . '" #' . $post_id . ' was published.' );
	}

	/**
	 * Returns the long message for the log.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 * @return string A HTML contents for the log. You should escape as you need.
	 */
	public function get_message( $additional_args )
	{
		list( $post_id ) = $additional_args;
		return sprintf(
			'<p><strong>URL:</strong> <a href="%1$s">%1$s</a></p>',
			esc_url( get_the_permalink( $post_id ) )
		);
	}
}
