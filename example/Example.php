<?php
/**
 * This class is an example for custom logger class.
 */

namespace My_Name_Space;
use Talog\Log;
use Talog\Log_Level;
use Talog\Logger;

class Example extends Logger
{
	protected $label = 'Post';
	protected $hooks = array( 'publish_post' );
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
		// title will be listed in table on the admin.
		$log->set_title( 'This is a log.' );

		// If you need a metadata use `update_meta()`.
		// It will be saved to the database.
		$log->update_meta( 'key', 'value' );
	}

	/**
	 * Set the properties to `\WP_Post` for the admin.
	 *
	 * @param \WP_Post $post     The post object.
	 * @param array   $post_meta The post meta of the `$post`.
	 * @return \WP_Post The `\WP_Post` object.
	 */
	public function admin( \WP_Post $post, $post_meta )
	{
		// post_content will be displayed on admin.
		$post->post_content = 'This is a content of the log.';

		// We can use metadata like following.
		if ( 'value' === $post_meta['key'] ) {
			$post->post_content = 'Hey!!';
		}
	}
}
