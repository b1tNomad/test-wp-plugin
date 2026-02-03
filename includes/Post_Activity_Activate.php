<?php

namespace Lernichenko\WpPostActivity\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post_Activity_Activate {
	public static function activate(): void {
		self::install_db_tables();
	}

	public static function deactivate(): void {
		//deactivate
	}

	public static function wpdb() {
		global $wpdb;
		return $wpdb;
	}

	private static function install_db_tables(): void {

		$charset_collate = self::wpdb()->get_charset_collate();
		$table_activity = self::get_activity_tbl_name();
		$table_views = self::get_views_tbl_name();

		$sql_activity = "CREATE TABLE IF NOT EXISTS $table_activity (
	        id bigint(20) NOT NULL AUTO_INCREMENT,
	        post_id bigint(20) NOT NULL,
	        user_id bigint(20) NOT NULL,
	        action varchar(20) NOT NULL,
	        action_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
	        PRIMARY KEY  (id)
	    ) $charset_collate;";

		$sql_views = "CREATE TABLE IF NOT EXISTS $table_views (
	        post_id bigint(20) NOT NULL,
	        view_count bigint(20) DEFAULT 0 NOT NULL,
	        PRIMARY KEY  (post_id)
	    ) $charset_collate;";

		self::wpdb()->query( $sql_activity );
		self::wpdb()->query( $sql_views );
	}

	public static function get_activity_tbl_name(): string {
		return self::wpdb()->prefix . 'wpa_activity_log';
	}

	public static function get_views_tbl_name(): string {
		return self::wpdb()->prefix . 'wpa_views_log';
	}
}
