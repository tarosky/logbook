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
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param Log    $log             An instance of `Talog\Log`.
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( Log $log, $additional_args )
	{
		list( $plugin ) = $additional_args;

		if ( 'activated_plugin' === current_filter() ) {
			$title = 'Plugin "' . $plugin . '" was activated.';
		} else {
			$title = 'Plugin "' . $plugin . '" was deactivated.';
		}

		$log->set_title( $title );
	}

	/**
	 * Set the properties to `\WP_Post` for the admin.
	 *
	 * @param \WP_Post $post     The post object.
	 * @param array   $post_meta The post meta of the `$post`.
	 */
	public function admin( \WP_Post $post, $post_meta ) {}
}
