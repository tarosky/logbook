<?php

namespace Talog\Logger;
use Talog\Log_Level;
use Talog\Logger;

class Activated_Plugin extends Logger
{
	protected $label = 'Plugin';
	protected $hooks = array( 'activated_plugin', 'deactivated_plugin' );
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
		list( $plugin ) = $additional_args;

		if ( 'activated_plugin' === current_filter() ) {
			return 'Plugin "' . $plugin . '" was activated.';
		} else {
			return 'Plugin "' . $plugin . '" was deactivated.';
		}
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
