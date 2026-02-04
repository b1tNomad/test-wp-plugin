<?php
namespace Lernichenko\WpPostActivity\Admin\Tables;

use Lernichenko\WpPostActivity\Includes\Post_Activity_Activate;


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Activity_Log_Table extends \WP_List_Table {

	public function get_columns(): array {
		return [
			'post_title'  => __( 'Post Title', 'wp-post-activity' ),
			'user_name'   => __( 'User', 'wp-post-activity' ),
			'action'      => __( 'Action', 'wp-post-activity' ),
			'action_date' => __( 'Date', 'wp-post-activity' ),
		];
	}

	public function prepare_items(): void {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$wpdb = Post_Activity_Activate::wpdb();
		$table = Post_Activity_Activate::get_activity_tbl_name();

		$per_page = 20;
		$current_page = $this->get_pagenum();
		$offset = ( $current_page - 1 ) * $per_page;

		$this->items = $wpdb->get_results( $wpdb->prepare(
			"SELECT a.*, p.post_title, u.display_name as user_name 
             FROM $table a
             LEFT JOIN {$wpdb->posts} p ON a.post_id = p.ID
             LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID
             ORDER BY a.action_date DESC 
             LIMIT %d OFFSET %d",
			$per_page, $offset
		), ARRAY_A );

		$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table" );

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
		] );
	}

	public function column_default( $item, $column_name ) {
		return $item[ $column_name ] ?? '';
	}

	public function column_action( $item ): string {
		$colors = [
			'created'   => '#429b07',
			'published' => '#007fff',
			'trashed'   => '#e11e15',
			'deleted'   => '#e11e15',
			'restored'  => '#f0c000',
		];
		$color = $colors[ $item['action'] ] ?? '#666666';
		return sprintf( '<strong style="color:%s;">%s</strong>', $color, ucfirst( $item['action'] ) );
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

	public function column_user_name( $item ): string {
		$user_name = ! empty( $item['user_name'] ) ? esc_html( $item['user_name'] ) : '—';

		if ( empty( $item['user_id'] ) ) {
			return $user_name;
		}

		$edit_user_link = get_edit_user_link( $item['user_id'] );

		if ( $edit_user_link ) {
			return sprintf(
				'<a href="%s"><strong>%s</strong></a>',
				esc_url( $edit_user_link ),
				$user_name
			);
		}

		return $user_name;
	}
}
