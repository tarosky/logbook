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
	public function register() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	public function plugins_loaded()
	{
		add_action( 'manage_talog_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts') );
		add_filter( 'manage_edit-talog_columns', array( $this, 'manage_sortable_columns') );
		add_filter( 'manage_edit-talog_sortable_columns', array( $this, 'manage_sortable_columns') );
	}

	public function manage_sortable_columns()
	{
		$columns = array();

		$columns['title'] = 'Log';
		$columns['_log_level'] = 'Level';
		$columns['author'] = 'User';
		$columns['date'] = 'Date';

		return $columns;
	}

	public function manage_custom_column( $column_name, $post_id )
	{
		echo esc_html( get_post_meta( $post_id, $column_name, true ) );
	}

	public function admin_enqueue_scripts()
	{
		wp_enqueue_style(
			'talog-admin-style',
			plugins_url( '/css/style.css', dirname( __FILE__ ) )
		);
	}
}
