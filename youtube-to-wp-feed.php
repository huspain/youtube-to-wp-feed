<?php
/**
* Plugin Name: Youtube To WP Feed
* Description: Imports youtube videos and playlists into WordPress website. Utilizes ACF PRO, which must be active in order to activate.
* Version: 1
* Author: Hussain Hamoudi
* Author URI: https://www.hamoudidigital.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

//  ACF must be active
add_action( 'plugins_loaded', 'youtube_to_wp_feed_plugin_init' );
function youtube_to_wp_feed_plugin_init() {
	if( !class_exists( 'ACF' ) || !is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
		deactivate_plugins(plugin_basename(__FILE__));
	}
}

//enqueue assets
add_action('wp_enqueue_scripts', 'enqueue_assets', 0, 1000);
function enqueue_assets() {
    wp_enqueue_style( 'youtube-styles', plugins_url( '/styles.css' , __FILE__ ));
}

// include other functions files
include plugin_dir_path( __FILE__ ) . '/functions/cpt_and_tax.php';
include plugin_dir_path( __FILE__ ) . '/functions/acf.php';
include plugin_dir_path( __FILE__ ) . '/functions/feed.php';
include plugin_dir_path( __FILE__ ) . '/functions/shortcodes.php';


// Register activation hook for cron job to import/update videos/playlists
// Add option to indicate flushing of rewrite rules
register_activation_hook(__FILE__, 'youtube_feed_update_activation');
function youtube_feed_update_activation() {
    if (!wp_next_scheduled('update_youtube_videos_hook')) {
        wp_schedule_event( time(), 'daily', 'sync_youtube_hook' );
    }
    add_option('youtube_feed_flush', 'true');
}
// Register deactivation hook to remove flushing rewrite rules option
register_deactivation_hook(__FILE__, 'youtube_to_wp_feed_update_deactivation');
function youtube_to_wp_feed_update_deactivation() {
    delete_option('youtube_feed_flush');
}