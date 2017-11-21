<?php
/**
 * This class is an example for custom logger class.
 */

namespace Hello;
use \LogBook\Logger;

class Example extends Logger
{
	/**
	 * @var string $label The label that is used on list table.
	 */
	protected $label = 'Post';
	/**
	 * @var array $hooks An array of WordPress's action/filter hooks.
	 */
	protected $hooks = array( 'publish_post' );
	/**
	 * @var string $log_level The log level. See `src/Level/`
	 */
	protected $log_level = \LogBook::DEFAULT_LEVEL;
	/**
	 * @var int $priority Number of priority that will be passed to `add_filter()`.
	 */
	protected $priority = 10;
	/**
	 * @var int $accepted_args Number of args that will be passed to `add_filter()`.
	 */
	protected $accepted_args = 1;

	/**
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		/**
		 * `$additional_args` contains args as array that passed from WordPress's hook.
		 * Following example is `$post_id` that came from `publish_post` hook.
		 */
		list( $post_id ) = $additional_args;

		/**
		 * title will be listed in table on the admin.
		 */
		$this->set_title( "Nice! #{$post_id} was published!" );

		/**
		 * You can add additional contents if you need.
		 */
		$this->add_content( 'Title 1', 'HTML content 1' );
		$this->add_content( 'Title 2', 'HTML content 2' );
	}
}
