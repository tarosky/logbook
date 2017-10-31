<?php
/**
 * Exmaple class for `Talog\Logger`.
 *
 * To register this class use `Talog\init_log()` like following.
 * ```
 * add_action( 'plugins_loaded', function() {
 *     Talog\init_log( 'My_Name_Space\Example' );
 * } );
 * ```
 */

namespace My_Name_Space;
use Talog\Log_Level;
use Talog\Logger;

class Example extends Logger
{
	protected $label = 'Example';
	protected $hooks = array( 'example_hook' );
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
		return "";
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
