<?php

namespace LogBook\Logger;
use LogBook\Logger;

class XML_RPC extends Logger
{
	protected $label = 'XML-RPC';
	protected $hooks = array( 'xmlrpc_call' );
	protected $log_level = \LogBook::WARN;
	protected $priority = 10;
	protected $accepted_args = 2;

	/**
	 * Set the properties to the `LogBook\Log` object for the log.
	 *
	 * @param mixed $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		list( $method ) = $additional_args;

		$this->set_title( __( 'XML-RPC user was authenticated.', 'logbook' ) );
		$this->add_content( 'Method', $method );
	}
}
