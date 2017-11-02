<?php

namespace Talog;

abstract class Logger
{
	protected $label = '';
	protected $hooks = array();
	protected $log_level = '\Talog\Level\Default_Level';
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
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param Log    $log             An instance of `Talog\Log`.
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	abstract public function log( Log $log, $additional_args );

	/**
	 * Set the properties to `\WP_Post` for the admin.
	 *
	 * @param \WP_Post $post     The post object.
	 * @param array   $post_meta The post meta of the `$post`.
	 */
	abstract public function admin( \WP_Post $post, $post_meta );

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
	 * Returns the value of `Talog\Level`.
	 *
	 * @return string Log level that come from `Talog\Level` class.
	 */
	public function get_log_level()
	{
		$level = $this->log_level;

		$level_name  = '';
		if ( $level ) {
			if ( class_exists( $level ) ) {
				$level_object = new $level();
				if ( is_a( $level_object, 'Talog\Level' ) ) {
					$level_name = $level_object->get_level();
				}
			}
		}

		if ( ! $level_name ) {
			$obj = new Level\Default_Level();
			$level_name = $obj->get_level();
		}

		return $level_name;
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

	/**
	 *  Registers the callback function for the admin page.
	 */
	public function add_filter()
	{
		$hook = $this->get_hook_name();
		add_action( $hook, array( $this, 'admin' ), 10, 2 );
	}

	/**
	 * Returns the hook name.
	 *
	 * @return string The name of the hook.
	 */
	public function get_hook_name()
	{
		return 'talog_content_' . str_replace( '\\', '_', strtolower( get_class( $this ) ) );
	}

	/**
	 * @param array $array
	 * @return string The HTML table.
	 */
	public function get_table( $array )
	{
		if ( ! empty( $array ) ) {
			$cols = array();
			foreach ( $array as $key => $value ) {
				$cols[] = sprintf(
					'<tr><th style="white-space: nowrap;">%s</th><td>%s</td></tr>',
					esc_html( trim( $key ) ),
					self::kses( trim( $value ) )
				);
			}

			return '<table class="table-talog">'
			       . implode( "", $cols ) . '</table>';
		}
	}

	private static function kses( $html )
	{
		$allowed_html = array(
			"a" => array(
				"href" => array(),
				"title" => array(),
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
			'pre' => array(),
		);

		$allowed_protocols = array( 'http', 'https' );

		return wp_kses( $html, $allowed_html, $allowed_protocols );
	}
}
