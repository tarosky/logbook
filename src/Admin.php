<?php

namespace Talog;

/**
 * Customize the list table on the admin screen.
 * https://make.wordpress.org/docs/plugin-developer-handbook/10-plugin-components/custom-list-table-columns/
 *
 * @package Talog
 */
class Admin
{
	public function register()
	{
		add_action( 'manage_talog_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts') );
		add_filter( 'manage_edit-talog_columns', array( $this, 'manage_sortable_columns') );
		add_filter( 'manage_edit-talog_sortable_columns', array( $this, 'manage_sortable_columns') );
		add_filter( 'manage_edit-talog_columns', array( $this, 'manage_columns' ) );
		add_filter( 'request', array( $this, 'request' ) );
		add_filter( 'bulk_actions-edit-talog', '__return_empty_array' );
		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts') );
		add_action( 'admin_menu', array( $this, 'admin_menu') );
	}

	public function admin_menu()
	{
		add_submenu_page(
			null,
			'Hello',
			null,
			'edit_pages',
			'talog',
			function() {
				$page = new Submenu_Page();
				$page->display();
			}
		);
	}

	public function restrict_manage_posts()
	{
		echo '<select name="_log_level">';
		echo '<option value="">Log level&nbsp;</option>';
		$levels = $this->get_meta_values( '_talog_log_level', 'talog' );
		foreach ( $levels as $level ) {
			if ( ! empty( $_GET['_log_level'] ) && $level === $_GET['_log_level'] ) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( Log_Level::get_level( $level ) ),
				$selected,
				esc_html( ucfirst( $level ) )
			);
		}
		echo '</select>';

		echo '<select name="_hook">';
		echo '<option value="">Action</option>';
		$hooks = $this->get_meta_values( '_talog_hook', 'talog' );
		foreach ( $hooks as $hook ) {
			if ( ! empty( $_GET['_hook'] ) && $hook === $_GET['_hook'] ) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $hook ),
				$selected,
				esc_html( $hook )
			);
		}
		echo '</select>';
	}

	public function manage_columns()
	{
		$columns = array();

		$columns['_date'] = 'Date';
		$columns['_title'] = 'Log';
		$columns['_log_level'] = 'Level';
		$columns['_user'] = 'User';

		return $columns;
	}

	public function manage_sortable_columns()
	{
		$columns = array();

		$columns['_date'] = 'Date';
		$columns['_title'] = 'Log';
		$columns['_log_level'] = 'Level';
		$columns['_user'] = 'User';

		return $columns;
	}

	public function request( $vars )
	{
		if ( ! empty( $_GET['post_type'] ) ) {
			if ( 'talog' === $_GET['post_type'] && array_key_exists( 'orderby', $vars ) ) {
				if ( 'Log' == $vars['orderby'] ) {
					$vars['orderby'] = 'post_title';
				} elseif ( 'Date' == $vars['orderby'] ) {
					$vars['orderby'] = 'post_date_gmt';
				} elseif ( 'User' == $vars['orderby'] ) {
					$vars['orderby'] = 'post_author';
				} elseif ( 'Level' == $vars['orderby'] ) {
					$vars['orderby']  = 'meta_value';
					$vars['meta_key'] = '_talog_log_level';
				}
			}

			if ( 'talog' === $_GET['post_type'] && ! empty( $_GET['_log_level'] ) ) {
				$vars['meta_query'] = array(
					array(
						'key'   => '_talog_log_level',
						'value' => $_GET['_log_level'],
					),
				);
			}

			if ( 'talog' === $_GET['post_type'] && ! empty( $_GET['_hook'] ) ) {
				$vars['meta_query'] = array(
					array(
						'key'   => '_talog_hook',
						'value' => $_GET['_hook'],
					),
				);
			}
		}

		return $vars;
	}

	public function manage_custom_column( $column_name, $post_id )
	{
		if ( '_title' === $column_name ) {
			$meta = get_post_meta( $post_id, '_talog', true );
			$post = get_post( $post_id );
			$post_title = $post->post_title;
			if ( ! empty( $meta['is_cli'] )) {
				$post_title = '[WP-CLI] ' . $post_title;
			}
			printf(
				'<a class="row-title" href="%2$s"><strong>%1$s</strong></a> ',
				$post_title,
				get_admin_url() . '/options.php?page=talog&log_id=' . intval( $post_id )
			);
		} elseif ( '_user' === $column_name ) {
			$post = get_post( $post_id );
			if( $post->post_author ) {
				echo esc_html( get_userdata( $post->post_author )->display_name );
			} else {
				echo '';
			}
		} elseif ( '_log_level' === $column_name ) {
			$meta = get_post_meta( $post_id, '_talog', true );
			if ( ! empty( $meta['log_level'] ) ) {
				echo esc_html( ucfirst( Log_Level::get_level( $meta['log_level'] ) ) );
			} else {
				echo esc_html( ucfirst( Log_Level::get_level() ) );
			}
		} elseif ( '_date' === $column_name ) {
			$post = get_post( $post_id );
			echo esc_html( get_date_from_gmt( $post->post_date_gmt, 'Y-m-d H:i:s' ) );
		}
	}

	public function admin_enqueue_scripts()
	{
		wp_enqueue_style(
			'talog-admin-style',
			plugins_url( '/css/style.css', dirname( __FILE__ ) )
		);
	}

	public function get_meta_values( $meta_key, $post_type = 'post' )
	{
		$posts = get_posts(
			array(
				'post_type' => $post_type,
				'meta_key' => $meta_key,
				'posts_per_page' => -1,
			)
		);

		$meta_values = array();
		foreach ( $posts as $post ) {
			$meta_values[] = get_post_meta( $post->ID, $meta_key, true );
		}

		$meta_values = array_unique( $meta_values );
		sort( $meta_values );

		return $meta_values;
	}
}
