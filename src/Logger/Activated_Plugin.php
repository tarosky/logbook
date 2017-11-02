<?php
/**
 * Save log for activate/deactivate plugin.
 */

namespace Talog\Logger;
use Talog\Logger;

class Activated_Plugin extends Logger
{
	protected $label = 'Plugin';
	protected $hooks = array( 'activated_plugin', 'deactivated_plugin' );
	protected $log_level = '\Talog\Level\Default_Level';
	protected $priority = 10;
	protected $accepted_args = 1;

	/**
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		list( $plugin ) = $additional_args;

		if ( 'activated_plugin' === current_filter() ) {
			$title = 'Plugin "' . $plugin . '" was activated.';
		} else {
			$title = 'Plugin "' . $plugin . '" was deactivated.';
		}

		$this->set_title( $title );

		$path = trailingslashit( WP_PLUGIN_DIR ) . $plugin;
		$table = $this->get_table( get_plugin_data( $path ) );

		$this->add_content( 'Plugin Data', $table );
	}
}
