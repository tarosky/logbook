<?php

namespace Talog\Logger;
use Talog\Log_Level;
use \Talog\Logger;

class Last_Error extends Logger
{
	protected $label = 'Debug';
	protected $hooks = array( 'shutdown' );
	protected $log_level = Log_Level::DEBUG;
	protected $priority = 10;
	protected $accepted_args = 1;

	private $error = array();

	public function __construct() {
		parent::__construct();
	}

	public function get_log( $additional_args ) {
		$this->error = $this->get_last_error();
		if ( $this->error ) {
			return esc_html( $this->error['message'] );
		} else {
			return null;
		}
	}

	public function get_message( $additional_args ) {
		if ( $this->error ) {
			$err = $this->error;
			$html = $this->last_error();
			if ( is_readable( $err['file'] ) ) {
				$content = self::get_a_part_of_file( $err['file'], $err['line'] );
				if ( $content ) {
					$html .= sprintf(
						'<h2>Source Code</h2><div class="code">%s</div>',
						$content
					);
				}
			}
			return  $html;
		} else {
			return null;
		}
	}

	/**
	 * Returns the last error of PHP.
	 *
	 * @return array An array of the result of `error_get_last()`.
	 */
	private function get_last_error()
	{
		return error_get_last();
	}

	private function last_error()
	{
		if ( ! empty( $this->error ) ) {
			$cols = array();
			foreach ( $this->error as $key => $value ) {
				$cols[] = sprintf(
					'<tr><th>%s</th><td>%s</td></tr>',
					esc_html( $key ),
					esc_html( $value )
				);
			}

			return '<h2>Last Error</h2><table class="table-talog">' . implode( "", $cols ) . '</table>';
		}
	}

	private static function get_a_part_of_file( $file_name, $line_number )
	{
		if ( is_readable( $file_name ) ) {
			$file = file( $file_name );
			$line = $line_number - 1;
			$end = $line + 10;
			$start = $line - 10;
			if ( $start < 0 ) {
				$start = 0;
			}
			$end = $end - $start;
			$line = $line - $start;

			$lines = array_slice( $file, $start, $end );

			$html = '';
			for ( $i = 0; $i < count( $lines ); $i++ ) {
				$class = '';
				if ( $i === $line ) {
					$class = 'line';
				}
				$html .= sprintf(
					'<pre class="%s">%s</pre>',
					esc_attr( $class ),
					esc_html( $lines[ $i ] )
				);
			}
			return $html;
		}
	}
}
