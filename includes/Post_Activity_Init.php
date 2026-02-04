<?php

namespace Lernichenko\WpPostActivity\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post_Activity_Init {
	public function init(): void {
		add_action('save_post', [$this, 'handle_save_post'], 10, 3);

		add_action('before_delete_post', [$this, 'handle_delete_post']);

		add_action('template_redirect', [$this, 'track_post_views']);
	}


	public function handle_save_post( $postId, $post, $update ): void {

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
		     wp_is_post_revision( $postId ) || $post->post_status === 'inherit' ) return;

		$action = $update ? 'updated' : 'created';
		$this->insert_activity_log( $postId, $action );
	}

	public function handle_delete_post( $postId ): void {
		if ( wp_is_post_revision( $postId ) ) return;

		$this->insert_activity_log( $postId, 'deleted' );
	}

	public function track_post_views(): void {
		if ( ! is_singular( [ 'post', 'page' ] ) ) return;

		global $post;

		$wpdb = Post_Activity_Activate::wpdb();
		$table_name = Post_Activity_Activate::get_views_tbl_name();

		$wpdb->query( $wpdb->prepare(
			"INSERT INTO $table_name (post_id, view_count) 
			 VALUES (%d, 1) 
			 ON DUPLICATE KEY UPDATE view_count = view_count + 1",
			$post->ID
		) );
	}


	private function insert_activity_log( $postId, $action ): void {
		Post_Activity_Activate::wpdb()->insert(
			Post_Activity_Activate::get_activity_tbl_name(),
			[
				'post_id'     => $postId,
				'user_id'     => get_current_user_id(),
				'action'      => $action,
				'action_date' => current_time( 'mysql' ),
			],
			[ '%d', '%d', '%s', '%s' ]
		);
	}
}
