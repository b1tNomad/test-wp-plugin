<?php

namespace Lernichenko\WpPostActivity\Includes\Shortcodes;

class Shortcode_Manager {
	public function init(): void {
		$post_views = new Post_Views_Shortcode();
		add_shortcode( 'post_views_count', [ $post_views, 'render' ] );
	}
}
