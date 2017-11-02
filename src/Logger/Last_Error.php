<?php

namespace Talog\Logger;
use Talog\Log;
use Talog\Logger;

class Last_Error extends Logger
{
	protected $label = 'Debug';
	protected $hooks = array( 'shutdown' );
	protected $log_level = '\Talog\Level\Debug';
	protected $priority = 10;
	protected $accepted_args = 1;

	/**
	 * Set the properties to the `Talog\Log` object for the log.
	 *
	 * @param Log    $log             An instance of `Talog\Log`.
	 * @param mixed  $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( Log $log, $additional_args ) {
		$error = error_get_last();
		if ( $error ) {
			$lines = explode( "\n", $error['message'] );
			$log->set_title( $lines[0] );
			$log->update_meta( 'error', $error );
			$log->update_meta( 'error-file', self::get_a_part_of_file( $error ) );

			if ( in_array( intval( $error['type'] ), array( 1, 4, 16, 64, 4096 ) ) ) {
				$log->set_log_level( 'error' );
			} elseif ( in_array( intval( $error['type'] ), array( 8, 1024, 8192, 16384 ) ) ) {
				$log->set_log_level( 'trace' );
			}
		}
	}

	/**
	 * Set the properties to `\WP_Post` for the admin.
	 *
	 * @param \WP_Post $post     The post object.
	 * @param array   $post_meta The post meta of the `$post`.
	 * @return \WP_Post The `\WP_Post` object.
	 */
	public function admin( \WP_Post $post, $post_meta ) {
		$content = '';

		if ( $post_meta['error-file'] ) {
			$content .= '<h2>Code</h2>';
			$content .= '<div class="code">' . $post_meta['error-file'] . '</div>';
		}

		if ( $post_meta['error'] ) {
			$post_meta['error']['message'] = '<pre>' . $post_meta['error']['message'] . '</pre>';
			$content .= '<h2>Last Error</h2>' . $this->get_table( $post_meta['error'] );
		}

		$post->post_content = $content;
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
