<?php

namespace Talog\Logger;
use Talog\Log_Level;
use Talog\Logger;

class WP_Delete_File extends Logger
{
	protected $label = 'Media';
	protected $hooks = array( 'wp_delete_file' );
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
		list( $file ) = $additional_args;
		return sprintf(
			'File "%s" was deleted.',
			esc_html( str_replace( untrailingslashit( ABSPATH ), '', $file ) )
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
