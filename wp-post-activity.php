<?php
/**
 * Plugin Name: WP Post Activity
 * Description: A lightweight plugin designed to track administrative actions and post engagement. It provides real-time monitoring of content changes and detailed view statistics directly in dashboard.
 * Version: 0.0.4
 * Author: Roman Lernichenko
 * Text Domain: wp-post-activity
 */

namespace Lernichenko\WpPostActivity;

use Lernichenko\WpPostActivity\Includes\Post_Activity_Activate;
use Lernichenko\WpPostActivity\Includes\Post_Activity_Init;

use Lernichenko\WpPostActivity\Admin\Post_Activity_Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

register_activation_hook( __FILE__, [ Post_Activity_Activate::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ Post_Activity_Activate::class, 'deactivate' ] );

add_action( 'init', function() {
	(new Post_Activity_Init())->init();

	if ( is_admin() ) {
		(new Post_Activity_Admin())->init();
	}
});
