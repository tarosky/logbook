<?php

namespace LogBook;

/**
 * Class Logger
 * @package LogBook
 *
 * @property \LogBook\Log $log
 * @property string $label
 * @property array $hook
 * @property string $log_level
 * @property int $priority
 * @property int $accepted_args
 */
abstract class Logger
{
	protected $log;
	protected $label = '';
	protected $hooks = array();
	protected $log_level = \LogBook::DEFAULT_LEVEL;
	protected $priority = 10;
	protected $accepted_args = 1;

	/**
	 * Logger constructor.
	 */
	public function __construct() {
		if ( empty( $this->label ) ) {
			wp_die( '`LogBook\Logger\Logger` requires the `$label` property.' );
		}
		if ( empty( $this->hooks ) || ! is_array( $this->hooks ) ) {
			wp_die( '`LogBook\Logger\Logger` requires the `$hooks` property.' );
		}
	}

	/**
	 * Set the properties to the `LogBook\Log` object for the log.
	 *
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	abstract public function log( $additional_args );

	/**
	 * @param string $title A title of the log.
	 */
	public function set_title( $title )
	{
		$this->log->set_title( $title );
	}

	/**
	 * @param string $title    The title for the log detail.
	 * @param string $content  The HTML content.
	 */
	public function add_content( $title, $content )
	{
		$this->log->add_content( $title, $content );
	}

	/**
	 * Set `\LogBook\Log` to the `$this->log`.
	 *
	 * @param Log    $log             An instance of `LogBook\Log`.
	 */
	public function set_log( Log $log )
	{
		$this->log = $log;
		$this->log->set_log_level( $this->log_level );
		$this->log->set_label( $this->label );
	}

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
	 * Returns the value of `LogBook`.
	 *
	 * @return string Log level that come from `LogBook` class.
	 */
	public function get_log_level()
	{
		return $this->log_level;
	}

	/**
	 * @param string $level The log level.
	 */
	public function set_level( $level )
	{
		$this->log->set_log_level( $level );
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
	 * @param array $keys
	 * @return string The HTML table content.
	 */
	public function get_server_variables_table( $keys )
	{
		$vars = array();
		foreach( $keys as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				$vars[ $key ] = '<pre>' . esc_html( $_SERVER[ $key ] ) . '</pre>';
			} else {
				$vars[ $key ] = '';
			}
		}

		return $this->get_table( $vars );
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
					'<tr><th>%s</th><td>%s</td></tr>',
					esc_html( trim( $key ) ),
					self::kses( trim( $value ) )
				);
			}

			return '<table class="table-logbook">'
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
