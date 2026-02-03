<?php

namespace Lernichenko\WpPostActivity;

use Lernichenko\WpPostActivity\Includes\Post_Activity_Activate;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}


$activity_tbl_name = Post_Activity_Activate::get_activity_tbl_name();
$views_tbl_name    = Post_Activity_Activate::get_views_tbl_name();

Post_Activity_Activate::wpdb()->query( "DROP TABLE IF EXISTS $activity_tbl_name" );
Post_Activity_Activate::wpdb()->query( "DROP TABLE IF EXISTS $views_tbl_name" );
