<?php

namespace LogBook\Logger;
use LogBook\Logger;

class Updated_Extensions extends Logger
{
	protected $label = 'WordPress';
	protected $hooks = array( 'upgrader_process_complete' );
	protected $log_level = \LogBook::DEFAULT_LEVEL;
	protected $priority = 9;
	protected $accepted_args = 2;

	/**
	 * Set the properties to the `LogBook\Log` object for the log.
	 *
	 * @param mixed $additional_args An array of the args that was passed from WordPress hook.
	 */
	public function log( $additional_args )
	{
		list( $upgrader, $options ) = $additional_args;

		if ( 'update' !== $options['action'] ) {
			return;
		}

		if ( 'core' === $options['type'] ) {
			return;
		}

		switch ( $options['type'] ) {
			case 'translation':
				$this->set_title( __( 'Languages were updated.', 'logbook' ) );
				$this->add_content( 'Summary', self::_table( $options['translations'], array(
					'language',
					'type',
					'slug',
					'version',
				) ) );
				break;
			case 'plugin':
				$this->set_title( __( 'Plugins were updated.', 'logbook' ) );
				$table = $this->get_plugins_table( $options['plugins'] );
				$this->add_content( 'Summary', $table );
				break;
			case 'theme':
				$this->set_title( __( 'Themes were updated.', 'logbook' ) );
				$table = $this->get_themes_table( $options['themes'] );
				$this->add_content( 'Summary', $table );
				break;
			default:
				break;
		}
	}

	private function get_themes_table( $themes )
	{
		$data = array();
		foreach ( $themes as $theme ) {
			$theme_data = wp_get_theme( $theme );
			$data[] = array(
				'name' => $theme_data['Name'],
				'slug' => $theme,
				'version' => $theme_data['Version'],
			);
		}

		return self::_table( $data, array( 'name', 'version' ) );
	}

	private function get_plugins_table( $plugins )
	{
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$data = array();
		foreach ( $plugins as $plugin ) {
			$path = trailingslashit( WP_PLUGIN_DIR ) . $plugin;
			$plugin_data = get_plugin_data( $path );
			$data[] = array(
				'name' => $plugin_data['Name'],
				'slug' => $plugin,
				'version' => $plugin_data['Version'],
			);
		}

		return self::_table( $data, array( 'name', 'version' ) );
	}

	/**
	 * @param array $data
	 * @param array $cols
	 *
	 * @return string
	 */
	private function _table( $data, $cols )
	{
		$table = '<table>';

		$rows = array();
		foreach ( $data as $d ) {
			$table_cols = array();
			foreach ( $d as $key => $value ) {
				if ( in_array( $key, $cols ) ) {
					$table_cols[ $key ] = $value;
				}
			}
			$rows[] = $table_cols;
		}

		$table .= '<tr>';
		foreach ( $cols as $col ) {
			$table .= sprintf(
				'<th>%s</th>',
				esc_html( ucfirst( $col ) )
			);
		}
		$table .= '</tr>';

		foreach ( $rows as $row ) {
			$table .= '<tr>';
			foreach ( $cols as $col ) {
				if ( empty( $row[ $col ] ) ) {
					$row[ $col ] = '';
				}
				$table .= sprintf(
					'<td>%s</td>',
					esc_html( $row[ $col ] )
				);
			}
			$table .= '</tr>';
		}

		$table .= '</table>';

		return $table;
	}
}
