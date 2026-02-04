<?php

namespace Lernichenko\WpPostActivity\Includes\Shortcodes;

use Lernichenko\WpPostActivity\Includes\Post_Activity_Activate;

class Post_Views_Shortcode {
	public function render( $atts ): string {
		global $post;
		$atts = shortcode_atts( [
			'id' => $post ? $post->ID : 0,
		], $atts, 'post_views' );

		$post_id = (int) $atts['id'];
		if ( ! $post_id ) return '';

		$wpdb = Post_Activity_Activate::wpdb();
		$table = Post_Activity_Activate::get_views_tbl_name();

		$views = $wpdb->get_var( $wpdb->prepare(
			"SELECT view_count FROM $table WHERE post_id = %d",
			$post_id
		) );

		$count = $views ? number_format_i18n( $views ) : 0;

		return sprintf(
			'<span class="post-views-count">%s %s</span>',
			esc_html( $count ),
			__( 'Views', 'wp-post-activity' )
		);
	}
}
