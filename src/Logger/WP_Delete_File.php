<?php

namespace Talog\Logger;
use Talog\Logger;

class WP_Delete_File extends Logger
{
	protected $label = 'Media';
	protected $hooks = array( 'wp_delete_file' );
	protected $log_level = '\Talog\Level\Default_Level';
	protected $priority = 10;
	protected $accepted_args = 1;

	/**
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param mixed $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		list( $file ) = $additional_args;
		$title = sprintf(
			'File "%s" was deleted.',
			esc_html( str_replace( untrailingslashit( ABSPATH ), '', $file ) )
		);

		$this->set_title( $title );
		// TODO: We need something content.
	}
}
