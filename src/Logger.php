<?php

namespace Talog;

abstract class Logger
{
	protected $label = '';
	protected $hooks = array();
	protected $log_level = Log_Level::DEFAULT_LEVEL;
	protected $priority = 10;
	protected $accepted_args = 1;

	/**
	 * Logger constructor.
	 */
	public function __construct() {
		if ( empty( $this->label ) ) {
			wp_die( '`Talog\Logger\Logger` requires the `$label` property.' );
		}
		if ( empty( $this->hooks ) || ! is_array( $this->hooks ) ) {
			wp_die( '`Talog\Logger\Logger` requires the `$hooks` property.' );
		}
	}

	/**
	 * Returns the log text.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 * @return string A text contents for the log that will be escaped automatically.
	 */
	abstract public function get_log( $additional_args );

	/**
	 * Returns the long message for the log.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 * @return string A HTML contents for the log. You should escape as you need.
	 */
	abstract public function get_message( $additional_args );

	/**
	 * Returns the label text for the log.
	 *
	 * @return string The label text for the log.
	 */
	public function get_label()
	{
		return $this->label;
	}

	/**
	 * Returns the WordPress's action hook or filter hook.
	 *
	 * @return array The hook that will fire callback.
	 */
	public function get_hooks()
	{
		return $this->hooks;
	}

	/**
	 * Returns the value of `Talog\Log_Level`.
	 *
	 * @return string Log level that come from `Talog\Log_Level` class.
	 */
	public function get_log_level()
	{
		return Log_Level::get_level( $this->log_level );
	}

	/**
	 * Returns integer that will be used for `$priority` of the `add_filter()`.
	 *
	 * @return int Integer that will passed to the `add_filter()`.
	 */
	public function get_priority()
	{
		return $this->priority;
	}

	/**
	 * Returns integer that will be used for `$accepted_args` of the `add_filter()`.
	 *
	 * @return int Integer that will passed to the `add_filter()`.
	 */
	public function get_accepted_args()
	{
		return $this->accepted_args;
	}
}
