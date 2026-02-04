<?php
namespace Lernichenko\WpPostActivity\Admin\Tables;

use Lernichenko\WpPostActivity\Includes\Post_Activity_Activate;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Post_Views_Table extends \WP_List_Table {

	public function get_columns(): array {
		return [
			'post_title' => __( 'Post Title', 'wp-post-activity' ),
			'view_count' => __( 'Total Views', 'wp-post-activity' ),
		];
	}

	public function prepare_items(): void {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$wpdb = Post_Activity_Activate::wpdb();
		$table = Post_Activity_Activate::get_views_tbl_name();

		$this->items = $wpdb->get_results(
			"SELECT v.*, p.post_title 
             FROM $table v
             JOIN {$wpdb->posts} p ON v.post_id = p.ID
             ORDER BY v.view_count DESC",
			ARRAY_A
		);
	}

	public function column_default( $item, $column_name ) {
		return $item[ $column_name ] ?? '';
	}

	public function column_post_title( $item ): string {

		$title = ! empty( $item['post_title'] ) ? esc_html( $item['post_title'] ) : '';

		if ( ! $title ) {
			return sprintf( '<em>(ID: %d - %s)</em>', $item['post_id'], __( 'Deleted', 'wp-post-activity' ) );
		}

		$edit_link = get_edit_post_link( $item['post_id'] );

		if ( $edit_link ) {
			return sprintf(
				'<a href="%s" aria-label="%s"><strong>%s</strong></a>',
				esc_url( $edit_link ),
				esc_attr( sprintf( __( 'Edit “%s”', 'wp-post-activity' ), $title ) ),
				$title
			);
		}

		return "<strong>$title</strong>";
	}
}
