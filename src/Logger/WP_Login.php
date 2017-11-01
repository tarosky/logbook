<?php

namespace Talog\Logger;
use Talog\Log;
use Talog\Log_Level;
use Talog\Logger;

class WP_Login extends Logger
{
	protected $label = 'User';
	protected $hooks = array( 'wp_login' );
	protected $log_level = Log_Level::DEFAULT_LEVEL;
	protected $priority = 10;
	protected $accepted_args = 2;

	/**
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param Log    $log             An instance of `Talog\Log`.
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( Log $log, $additional_args )
	{
		list( $user_login, $user ) = $additional_args;
		$title = sprintf(
			'User "%s" logged in.',
			esc_html( $user_login )
		);

		$log->set_title( $title );
	}

	/**
	 * Set the properties to `\WP_Post` for the admin.
	 *
	 * @param \WP_Post $post     The post object.
	 * @param array   $post_meta The post meta of the `$post`.
	 * @return \WP_Post The `\WP_Post` object.
	 */
	public function admin( \WP_Post $post, $post_meta ) {}
}
