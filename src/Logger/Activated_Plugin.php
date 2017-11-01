<?php

namespace Talog\Logger;
use Talog\Log_Level;
use Talog\Logger;
use Talog\Log;

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
	 * @param Log    $log             An instance of `Talog\Log`.
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function get_log( Log $log, $additional_args )
	{
		list( $plugin ) = $additional_args;

		if ( 'activated_plugin' === current_filter() ) {
			$title = 'Plugin "' . $plugin . '" was activated.';
		} else {
			$title = 'Plugin "' . $plugin . '" was deactivated.';
		}

		$log->set_title( $title );
	}
}
