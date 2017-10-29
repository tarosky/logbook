<?php

namespace Talog;

class Submenu_Page
{
	private $post;

	public function __construct() {
		$this->post = get_post( intval( $_GET['log_id'] ) );
		$this->meta = get_post_meta( $this->post->ID, '_talog', true );
	}

	public function display()
	{
		if ( ! empty( $this->meta['log_level'] )) {
			$log_level = $this->meta['log_level'];
		} else {
			$log_level = 'info';
		}

		echo '<div class="wrap talog-log-details">';
		printf(
			'<p><a href="%s">Back to the list page.</a></p>',
			admin_url( 'edit.php?post_type=talog' )
		);

		printf(
			'<h1 class="log-title">[%s] %s</h1>',
			esc_html( $log_level ),
			esc_html( $this->post->post_title )
		);

		echo $this->meta_contents();
		echo $this->get_the_content( $this->post->post_content, true );
		echo $this->get_the_content( $this->last_error(), true );

		echo '</div><!-- .wrap -->';
	}

	public function meta_contents()
	{
		if ( ! empty( $this->meta['is_cli'] ) ) {
			$author = 'WP-CLI';
		} elseif ( empty( $this->post->post_author ) ) {
			$author = 'anonymous';
		} else {
			$author = get_userdata( $this->post->post_author )->display_name;
		}

		return sprintf(
			'<p style="text-align: right; margin-right: 20px;"><strong>%2$s</strong> by <strong>%1$s</strong></p>',
			esc_html( $author ),
			esc_html( get_date_from_gmt( $this->post->post_date_gmt, 'Y-m-d H:i:s' ) )
		);
	}

	public function last_error()
	{
		if ( ! empty( $this->meta['last_error'] ) ) {
			$cols = array();
			foreach ( $this->meta['last_error'] as $key => $value ) {
				$cols[] = sprintf(
					'<tr><th>%s</th><td>%s</td></tr>',
					esc_html( $key ),
					esc_html( $value )
				);
			}

			return '<h2>Last Error</h2><table class="table-talog">' . implode( "", $cols ) . '</table>';
		}
	}

	public function get_the_content( $content, $allow_html = false )
	{
		if ( $content ) {
			if ( ! $allow_html ) {
				$content = esc_html( $content );
			}
			return sprintf(
				$this->get_container(),
				$content
			);
		}
	}

	public function get_container()
	{
		return '<div class="postbox-container">%s</div><!-- .postbox-container -->' . "\n";
	}
}
