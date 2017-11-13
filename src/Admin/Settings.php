<?php

namespace LogBook\Admin;

class Settings
{
	public function __construct()
	{
	}

	public static function get_instance()
	{
		static $instance;
		if ( ! $instance ) {
			$instance = new Settings();
		}
		return $instance;
	}

	public function display()
	{
		echo '<div class="wrap logbook-settings">';
		echo '<h1 class="wp-heading-inline">LogBook Settings</h1>';
		echo '<form method="post">';
		wp_nonce_field( 'logbook-access-token', 'logbook-token' );
		echo '<h2>Rest API Access Token</h2>';
		$message = __( 'Regenerate token', 'logbook' );
		if ( $token = get_option( 'logbook-tmp-token' ) ) {
			delete_option( 'logbook-tmp-token' );
		} else {
			if ( get_option( 'logbook-api-token' ) ) {
				$token = str_repeat( '*', 40 );
			} else {
				$token = '';
				$message = __( 'Generate token', 'logbook' );
			}
		}
		echo '<div class="token"><span>' . esc_html( $token ) . '</span>';
		echo '<input type="submit" class="button" value="' . $message . '"></div>';
		echo '</form>';
		echo '</div>';
	}
}
