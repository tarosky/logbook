<?php

namespace LogBook\Logger;
use LogBook\Logger;

class Last_Error extends Logger
{
	protected $label = 'Debug';
	protected $hooks = array( 'shutdown' );
	protected $log_level = \LogBook::DEBUG;
	protected $priority = 10;
	protected $accepted_args = 1;

	/**
	 * Set the properties to the `LogBook\Log` object for the log.
	 *
	 * @param mixed $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args ) {
		$error = error_get_last();
		if ( $error ) {
			$lines = explode( "\n", $error['message'] );
			$this->set_title( $lines[0] );

			$title = 'Code';
			$content = self::get_a_part_of_file( $error );
			if ( $content ) {
				$this->add_content( $title, '<div class="code">' . $content . '</div>' );
			}

			$title = 'Summary';
			foreach ( $error as $key => $value ) {
				if ( 'message' === $key ) {
					$error['message'] = '<pre>' . $value . '</pre>';
				}
			}
			$content = $this->get_table( $error );
			if ( $content ) {
				$this->add_content( $title, $content );
			}

			if ( in_array( intval( $error['type'] ), array( 1, 4, 16, 64 ) ) ) {
				$this->set_level( \LogBook::ERROR );
			} elseif ( in_array( intval( $error['type'] ), array( 8, 1024, 4096, 8192, 16384 ) ) ) {
				$this->set_level( \LogBook::TRACE );
			}
		}
	}

	public static function get_a_part_of_file( $error )
	{
		if ( empty( $error ) ) {
			return '';
		}

		$file_name = $error['file'];
		$line_number = $error['line'];
		$length = 15;

		if ( is_readable( $file_name ) ) {
			$file = file( $file_name );
			$line = $line_number - 1;
			$end = $line + $length + 1;
			$start = $line - $length;
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
					'<pre class="%s">%s</pre>' . "\n",
					esc_attr( $class ),
					esc_html( rtrim( $lines[ $i ] ) )
				);
			}
			return $html;
		}
	}
}
