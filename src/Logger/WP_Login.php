<?php

namespace Talog\Logger;
use Talog\Logger;

class WP_Login extends Logger
{
	protected $label = 'User';
	protected $hooks = array( 'wp_login' );
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
		list( $user_login ) = $additional_args;
		$title = sprintf(
			'User "%s" logged in.',
			esc_html( $user_login )
		);

		$content = $this->get_server_variables_table( array(
			'REMOTE_ADDR',
			'HTTP_USER_AGENT'
		) );

		$this->set_title( $title );
		$this->add_content( 'Environment Variables', $content );
	}
}
