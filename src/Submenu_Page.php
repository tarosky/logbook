<?php

namespace Talog;

final class Submenu_Page
{
	private $post;
	private $meta;

	public function __construct() {
		$this->post = get_post( intval( $_GET['log_id'] ) );
		$this->meta = get_post_meta( $this->post->ID, '_talog', true );
	}

	public function display()
	{
		$post = $this->post;
		if ( ! empty( $this->meta['filter'] ) ) {
			do_action( $this->meta['filter'], $post, $this->meta );
		}

		if ( ! empty( $this->meta['log_level'] )) {
			$log_level = Log_Level::get_level( $this->meta['log_level'] );
		} else {
			$log_level = Log_Level::get_level();
		}

		echo '<div class="wrap talog-log-details">';
		printf(
			'<p><a href="%s">Back to the list page.</a></p>',
			admin_url( 'edit.php?post_type=talog' )
		);

		printf(
			'<h1 class="log-title">[%s] %s</h1>',
			esc_html( Log_Level::get_level( $log_level ) ),
			esc_html( $post->post_title )
		);

		echo $this->meta_contents();
		echo $this->get_the_content( $post->post_content, true );
		echo $this->get_the_content( $this->server_vars(), true );

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
			'<p style="text-align: right; margin-right: 20px;">
						<strong>%2$s</strong> by <strong>%1$s</strong></p>',
			esc_html( $author ),
			esc_html( get_date_from_gmt( $this->post->post_date_gmt, 'Y-m-d H:i:s' ) )
		);
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

	public function server_vars()
	{
		if ( ! empty( $this->meta['server_vars'] ) ) {
			$table = '';
			foreach ( $this->meta['server_vars'] as $key => $value ) {
				$table .= sprintf(
					'<tr><td>%s</td><td>%s</td></tr>',
					esc_html( str_replace( '%', '%%', $key ) ),
					esc_html( str_replace( '%', '%%', $this->json_encode( $value ) ) )
				);
			}
			return sprintf(
				'<h2 class="title">$_SERVER</h2><table class="table-talog">%s%s</table>',
				'<tr><th>Name</th><th>Value</th></tr>',
				$table
			);
		}
	}

	public function get_container()
	{
		return '<div class="postbox-container">%s</div><!-- .postbox-container -->' . "\n";
	}

	private function json_encode( $var )
	{
		return json_encode( $var, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	}
}
