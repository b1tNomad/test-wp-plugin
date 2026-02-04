<?php

namespace Lernichenko\WpPostActivity\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post_Activity_Init {
	private static array $processed_posts = [];

	public function init(): void {
		add_action( 'transition_post_status', [ $this, 'handle_status_transition' ], 10, 3 );

		add_action('before_delete_post', [$this, 'handle_delete_post']);

		add_action('template_redirect', [$this, 'track_post_views']);
	}

	public function handle_status_transition( $new_status, $old_status, $post ): void {
		$postId = $post->ID;

		if ( wp_is_post_revision( $postId ) ||
		     $new_status === 'auto-draft' ||
		     $new_status === 'inherit' ) return;

		if ( isset( self::$processed_posts[ $postId ] ) ) return;

		$action = match ( true ) {
			( $old_status === 'new' || $old_status === 'auto-draft' ) && $new_status === 'draft' => 'created',
			$old_status !== 'publish' && $new_status === 'publish' => 'published',
			$new_status === 'trash' => 'trashed',
			$old_status === 'trash' && $new_status !== 'trash' => 'restored',
			$old_status === $new_status && ! in_array( $new_status, [ 'auto-draft', 'new' ] ) => 'updated',
			default => '',
		};

		if ( $action ) {
			self::$processed_posts[ $postId ] = $action;
			$this->insert_activity_log( $postId, $action );
		}
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
