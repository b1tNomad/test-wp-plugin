<?php

namespace Lernichenko\WpPostActivity\Admin;

use Lernichenko\WpPostActivity\Admin\Tables\Activity_Log_Table;
use Lernichenko\WpPostActivity\Admin\Tables\Post_Views_Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post_Activity_Admin {

	public function init(): void {
		add_action( 'admin_menu', [ $this, 'add_plugin_admin_menu' ] );
	}

	public function add_plugin_admin_menu(): void {
		add_menu_page(
			__( 'Post Activity', 'wp-post-activity' ),
			__( 'Post Activity', 'wp-post-activity' ),
			'manage_options',
			'post-activity',
			[ $this, 'render_activity_logs_page' ],
			'dashicons-visibility',
			25
		);

		add_submenu_page(
			'post-activity',
			__( 'Activity Logs', 'wp-post-activity' ),
			__( 'Activity Logs', 'wp-post-activity' ),
			'manage_options',
			'post-activity',
			[ $this, 'render_activity_logs_page' ]
		);

		add_submenu_page(
			'post-activity',
			__( 'Post Views', 'wp-post-activity' ),
			__( 'Post Views', 'wp-post-activity' ),
			'manage_options',
			'post-activity-views',
			[ $this, 'render_post_views_page' ]
		);
	}

	public function render_activity_logs_page(): void {
		$activity_table = new Activity_Log_Table();
		$activity_table->prepare_items();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e( 'Activity Logs', 'wp-post-activity' ); ?></h1>
			<hr class="wp-header-end">

			<?php $activity_table->display(); ?>
		</div>
		<?php
	}

	public function render_post_views_page(): void {
		$views_table = new Post_Views_Table();
		$views_table->prepare_items();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e( 'Post Views Statistics', 'wp-post-activity' ); ?></h1>
			<hr class="wp-header-end">

			<?php $views_table->display(); ?>
		</div>
		<?php
	}
}
