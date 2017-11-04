<?php

namespace Talog\Logger;
use Talog\Logger;

class XML_RPC extends Logger
{
	protected $label = 'XML-RPC';
	protected $hooks = array( 'xmlrpc_call' );
	protected $log_level = '\Talog\Level\Warn';
	protected $priority = 10;
	protected $accepted_args = 2;

	/**
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param mixed $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		list( $method ) = $additional_args;

		$this->set_title( 'XML-RPC user was authenticated.' );
		$this->add_content( 'Method', $method );
	}
}
