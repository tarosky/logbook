<?php

namespace LogBook;

/**
 * Customize the list table on the admin screen.
 *
 * @package LogBook
 */
final class Admin {
	public function register() {
		add_action( 'manage_logbook_posts_custom_column',
					array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'manage_edit-logbook_columns', array( $this, 'manage_sortable_columns' ) );
		add_filter( 'manage_edit-logbook_sortable_columns',
					array( $this, 'manage_sortable_columns' ) );
		add_filter( 'manage_edit-logbook_columns', array( $this, 'manage_columns' ) );
		add_filter( 'request', array( $this, 'request' ) );
		add_filter( 'bulk_actions-edit-logbook', '__return_empty_array' );
		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_menu() {
		add_submenu_page(
			null,
			'Hello',
			null,
			'edit_pages',
			'logbook',
			function () {
				$page = new Admin\Log_Page();
				$page->display();
			}
		);
	}

	public function restrict_manage_posts() {
		echo '<select name="_label">';
		echo '<option value="">All labels &nbsp;</option>';
		$labels = self::get_meta_values( '_logbook_label' );
		foreach ( $labels as $label ) {
			if ( ! empty( $_GET['_label'] ) && $label === $_GET['_label'] ) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $label ),
				$selected,
				esc_html( $label )
			);
		}
		echo '</select>';

		echo '<select name="_log_level">';
		echo '<option value="">All levels &nbsp;</option>';
		$levels = array(
			'fatal',
			'error',
			'warn',
			'info',
			'debug',
			'trace',
		);
		foreach ( $levels as $level ) {
			$level = self::get_level_name( $level );
			if ( ! empty( $_GET['_log_level'] ) && $level === $_GET['_log_level'] ) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $level ),
				$selected,
				esc_html( ucfirst( $level ) )
			);
		}
		echo '</select>';
	}

	public function manage_columns() {
		$columns = array();

		$columns['_date']      = 'Date';
		$columns['_title']     = 'Log';
		$columns['_log_level'] = 'Level';
		$columns['_ip'] = 'IP';
		$columns['_user']      = 'User';

		return $columns;
	}

	public function manage_sortable_columns() {
		$columns = array();

		$columns['_date']      = 'Date';
		$columns['_title']     = 'Log';
		$columns['_log_level'] = 'Level';
		$columns['_user']      = 'User';

		return $columns;
	}

	public function request( $vars ) {
		if ( ! empty( $_GET['post_type'] ) ) {
			if ( 'logbook' === $_GET['post_type'] && array_key_exists( 'orderby', $vars ) ) {
				if ( 'Log' == $vars['orderby'] ) {
					$vars['orderby'] = 'post_title';
				} elseif ( 'Date' == $vars['orderby'] ) {
					$vars['orderby'] = 'post_date_gmt';
				} elseif ( 'User' == $vars['orderby'] ) {
					$vars['orderby'] = 'post_author';
				} elseif ( 'Level' == $vars['orderby'] ) {
					$vars['orderby']  = 'meta_value';
					$vars['meta_key'] = '_logbook_log_level';
				}
			}

			$meta_query = array();
			if ( 'logbook' === $_GET['post_type'] && ! empty( $_GET['_label'] ) ) {
				$meta_query[] = array(
					'key'   => '_logbook_label',
					'value' => $_GET['_label'],
				);
			}

			if ( 'logbook' === $_GET['post_type'] && ! empty( $_GET['_log_level'] ) ) {
				$meta_query[] = array(
					'key'   => '_logbook_log_level',
					'value' => $_GET['_log_level'],
				);
			}

			if ( $meta_query ) {
				$vars['meta_query'] = $meta_query;
			}
		}

		return $vars;
	}

	public function manage_custom_column( $column_name, $post_id ) {
		if ( '_title' === $column_name ) {
			$meta       = get_post_meta( $post_id, '_logbook', true );
			$post       = get_post( $post_id );
			$post_title = $post->post_title;
			printf(
				'<a class="row-title" href="%2$s"><strong>%1$s</strong></a> ',
				esc_html( $post_title ),
				get_admin_url() . 'options.php?page=logbook&log_id=' . intval( $post_id )
			);
			if ( ! empty( $meta['is_cli'] ) ) {
				echo '<sup class="wp-cli">WP-CLI</sup>';
			}
		} elseif ( '_user' === $column_name ) {
			$post = get_post( $post_id );
			if ( $post->post_author ) {
				echo esc_html( get_userdata( $post->post_author )->user_login );
			} else {
				echo '';
			}
		} elseif ( '_log_level' === $column_name ) {
			$meta = get_post_meta( $post_id, '_logbook', true );
			if ( ! empty( $meta['log_level'] ) ) {
				printf(
					'<span class="%s log-level">%s</span>',
					esc_attr( self::get_level_name( $meta['log_level'] ) ),
					esc_html( ucfirst( self::get_level_name( $meta['log_level'] ) ) )
				);
			}
		} elseif ( '_date' === $column_name ) {
			$post = get_post( $post_id );
			echo esc_html( get_date_from_gmt( $post->post_date_gmt, 'Y-m-d H:i:s' ) );
		} elseif ( '_ip' === $column_name ) {
			$meta = get_post_meta( $post_id, '_logbook', true );
			if ( ! empty( $meta['ip'] ) ) {
				echo esc_html( $meta['ip'] );
			} else {
				echo '';
			}
		}
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_style(
			'logbook-admin-style',
			plugins_url( '/css/style.css', dirname( __FILE__ ) ),
			array(),
			filemtime( dirname( dirname( __FILE__ ) ) . '/css/style.css' )
		);
	}

	protected static function get_meta_values( $meta_key, $post_type = 'logbook' ) {
		global $wpdb;

		$sql = "SELECT pm.meta_value FROM {$wpdb->postmeta} pm
			LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = '%s' AND p.post_type = '%s'";

		$meta_values = $wpdb->get_col( $wpdb->prepare( $sql, $meta_key, $post_type ) );

		$meta_values = array_unique( $meta_values );
		sort( $meta_values );

		return $meta_values;
	}

	protected function get_level_name( $level ) {
		if ( ! $level ) {
			$level = '';
		}

		return $level;
	}
}
