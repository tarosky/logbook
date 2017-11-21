<?php
/**
 * Save log for activate/deactivate plugin.
 */

namespace LogBook\Logger;
use LogBook\Logger;

class Activated_Extensions extends Logger
{
	protected $label = 'Plugin';
	protected $hooks = array( 'activated_plugin', 'deactivated_plugin', 'switch_theme' );
	protected $log_level = \LogBook::DEFAULT_LEVEL;
	protected $priority = 10;
	protected $accepted_args = 2;

	/**
	 * Set the properties to the `LogBook\Log` object for the log.
	 *
	 * @param mixed $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		if ( 'switch_theme' === current_filter() ) {
			/**
			 * @var string $theme_name
			 * @var \WP_Theme $theme
			 */
			list( $theme_name, $theme ) = $additional_args;
			$this->set_title( sprintf(
				__( 'Theme was switched to %s.', 'logbook' ),
				esc_html( $theme_name )
			) );
			$this->add_content( 'Theme Data', $this->get_table( array(
				'Name' => $theme->get( 'Name' ),
				'Version' => $theme->get( 'Version' ),
				'Author' => $theme->get( 'Author' ),
				'Description' => $theme->get( 'Description' ),
			) ) );
		} else {
			list( $plugin ) = $additional_args;

			if ( 'activated_plugin' === current_filter() ) {
				$title = sprintf(
					__( 'Plugin "%s" was activated.', 'logbook' ),
					$plugin
				);
			} else {
				$title = sprintf(
					__( 'Plugin "%s" was deactivated.', 'logbook' ),
					$plugin
				);
			}

			$path = trailingslashit( WP_PLUGIN_DIR ) . $plugin;

			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$table = $this->get_table( get_plugin_data( $path ) );

			$this->set_title( $title );
			$this->add_content( 'Plugin Data', $table );
		}
	}
}
