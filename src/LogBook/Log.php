<?php

namespace LogBook;

class Log
{
	/**
	 * @var \stdClass $log
	 */
	private $log;

	public function __construct()
	{
		$this->log = new \stdClass;
		$this->log->title = '';
		$this->log->content = '';
		$this->log->user = self::get_user_id();
		$this->log->meta = array(
			'label' => 'General',
			'log_level' => null,
			'hook' => self::get_current_hook(),
			'is_cli' => self::is_cli(),
			'cli-command' => self::cli(),
			'ip' => self::get_ip(),
		);
	}

	/**
	 * @param string $title The log.
	 */
	public function set_title( $title )
	{
		$this->log->title = $title;
	}

	/**
	 * @param string $title   The title of the content.
	 * @param string $content The content.
	 */
	public function add_content( $title, $content )
	{
		if ( empty( $title ) || empty( $content ) ) {
			return;
		}
		if ( $this->log->content ) {
			$contents = json_decode( urldecode( $this->log->content ), true );
		} else {
			$contents = array();
		}
		$contents[] = array(
			'title' => $title,
			'content' => $content,
		);

		$content =  json_encode( $contents, JSON_HEX_APOS | JSON_HEX_QUOT );
		// To prevent escaping by WordPress
		$this->log->content = urlencode( $content );
	}

	/**
	 * @param \WP_Post $post
	 * @return array $log
	 */
	public static function get_the_log( $post )
	{
		$log = array();
		$log['id'] = $post->ID;
		$log['date'] = $post->post_date_gmt;
		$log['log'] = $post->post_title;
		$log['content'] = json_decode( urldecode( $post->post_content ), true );

		if ( $post->post_author && $u = get_userdata( $post->post_author ) ) {
			$log['user'] = $u->user_login;
		} else {
			$log['user'] = '';
		}

		$log['label'] = get_post_meta( $post->ID, '_logbook_label', true );
		$log['level'] = get_post_meta( $post->ID, '_logbook_log_level', true );
		$log['meta'] = get_post_meta( $post->ID, '_logbook', true );

		return $log;
	}

	public function set_label( $label )
	{
		if ( ! empty( $label ) ) {
			$this->log->meta['label'] = $label;
		}
	}

	public function set_log_level( $level )
	{
		$this->log->meta['log_level'] = $level;
	}

	public function get_log_level()
	{
		return $this->log->meta['log_level'];
	}

	public function update_meta( $key, $value )
	{
		$this->log->meta[ $key ] = $value;
	}

	public function delete_meta( $key )
	{
		unset( $this->log->meta[ $key ] );
	}

	public function get_log()
	{
		if ( empty( $this->log->title ) || empty( $this->log->meta['hook'] ) ) {
			return new \WP_Error( 'Incorrect log object' );
		} else {
			return $this->log;
		}
	}

	public function is_log()
	{
		if ( empty( $this->log->title ) || empty( $this->log->meta['hook'] ) ) {
			return false;
		} else {
			return true;
		}
	}

	public function get_command_log()
	{
		if ( $this->log->meta['cli-command'] ) {
			$u   = exec( 'whoami' );
			if ( empty( $_SERVER['REMOTE_ADDR'] ) ) {
				$addr = 'localhost';
			} else {
				$addr = $_SERVER['REMOTE_ADDR'];
			}

			return '[' . $u . '@' . $addr . '] wp ' . $this->log->meta['cli-command'];
		}
	}

	public function has_command_log()
	{
		return !! $this->log->meta['cli-command'];
	}

	protected static function is_cli()
	{
		if ( defined('WP_CLI') && WP_CLI ) {
			return true;
		} else {
			return false;
		}
	}

	protected static function get_ip()
	{
		if ( ! empty( $_SERVER['HTTP_REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['HTTP_REMOTE_ADDR'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = '';
		}

		/**
		 * Filters the remote address.
		 *
		 * @param string $ip The remote address of the user.
		 */
		return apply_filters( 'logbook_log_remote_ip', $ip );
	}

	protected static function get_current_hook()
	{
		return current_filter();
	}

	protected static function get_user()
	{
		return wp_get_current_user();
	}

	protected function get_user_id()
	{
		$user = self::get_user();
		if ( empty( $user->ID ) ) {
			return 0;
		} else {
			return $user->ID;
		}
	}

	protected function cli()
	{
		if ( self::is_cli() ) {
			$commands = $GLOBALS['argv'];
			foreach ( $commands as $cmd ) {
				array_shift( $commands );
				if ( $cmd === $_SERVER['PHP_SELF'] ) {
					break;
				}
			}
			if ( $commands ) {
				return implode( ' ', $commands );
			} else {
				return '';
			}
		} else {
			return '';
		}
	}
}
