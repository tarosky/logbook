<?php

namespace Talog;

final class Submenu_Page
{
	private $post;
	private $meta;

	public function __construct() {
		$this->post = get_post( $_GET['log_id'] );
		if ( empty( $this->post ) || 'talog' !== $this->post->post_type ) {
			wp_die( 'Not found.' );
		}
		$this->meta = get_post_meta( $this->post->ID, '_talog', true );
	}

	public function display()
	{
		$post = $this->post;

		if ( ! empty( $this->meta['log_level'] )) {
			$log_level = self::get_level_name( $this->meta['log_level'] );
		} else {
			$log_level = self::get_level_name();
		}

		echo '<div class="wrap talog-log-details">';
		printf(
			'<p><a href="%s">Back to the list page.</a></p>',
			admin_url( 'edit.php?post_type=talog' )
		);

		printf(
			'<h1 class="log-title"><span class="%s log-level">[%s]</span> %s</h1>',
			esc_attr( $log_level ),
			esc_html( ucfirst( $log_level ) ),
			esc_html( $post->post_title )
		);

		echo $this->meta_contents();

		if ( ! empty( $post->post_content ) ) {
			$contents = json_decode( urldecode( $post->post_content ), true );
			if ( is_array( $contents ) ) {
				for ( $i = 0; $i < count( $contents ); $i++ ) {
					$title = $contents[ $i ]['title'];
					$content = $contents[ $i ]['content'];
					add_meta_box(
						'talog-content-' . $i,
						$title,
						function () use ( $content ) {
							echo $content;
						},
						'talog',
						'normal'
					);
				}
			}
		}

		echo '<div class="metabox-holder">';
		do_meta_boxes( 'talog', 'normal', $post );
		echo '</div>';

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

	public function get_container()
	{
		return '<div class="apostbox-container">%s</div><!-- .postbox-container -->';
	}

	private function json_encode( $var )
	{
		return json_encode( $var, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	}

	protected function get_level_name( $level = null ) {
		$level_name  = '';
		if ( $level ) {
			$level_class = '\\Talog\\Level\\' . ucfirst( $level );
			if ( class_exists( $level_class ) ) {
				$level_object = new $level_class();
				if ( is_a( $level_object, 'Talog\Level' ) ) {
					$level_name = $level_object->get_level();
				}
			}
		}

		if ( ! $level_name ) {
			$obj = new Level\Default_Level();
			$level_name = $obj->get_level();
		}

		return $level_name;
	}
}
