<?php

namespace LogBook\Logger;
use LogBook\Logger;

class Updated_Core extends Logger
{
	protected $label = 'WordPress';
	protected $hooks = array( '_core_updated_successfully' );
	protected $log_level = '\LogBook\Level\Default_Level';
	protected $priority = 9;
	protected $accepted_args = 1;

	/**
	 * Set the properties to the `LogBook\Log` object for the log.
	 *
	 * @param mixed $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		list( $wp_version ) = $additional_args;

		$this->set_title( 'WordPress was updated to ' . $wp_version . '.' );
		$this->add_content( 'Version', $wp_version );
	}
}
