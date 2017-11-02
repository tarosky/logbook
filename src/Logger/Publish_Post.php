<?php

namespace Talog\Logger;
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
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		list( $post_id, $post ) = $additional_args;

		$content = $this->get_table( array(
			'ID' => esc_html( $post_id ),
			'Title' => esc_html( $post->post_title  ),
			'URL' => sprintf(
				'<a href="%1$s">%1$s</a>',
				esc_url( get_the_permalink( $post_id ) )
			)
		) );

		$this->set_title( '#' . $post_id . ' "' . $post->post_title . '" was published.' );
		$this->add_content( 'Summary', $content );
	}
}
