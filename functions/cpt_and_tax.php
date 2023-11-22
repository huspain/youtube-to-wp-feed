<?php
/**
* Registers necessary custom post types and taxonomies for plugin use
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// registers youtube video CPT
add_action('init', 'register_youtube_videos');
function register_youtube_videos() {
    register_post_type( 'youtube_videos',
        array(
            'labels' => array(
                'name' => __( 'Youtube Videos' ),
                'singular_name' => __( 'Youtube Video' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'youtube-videos'),
            'show_in_rest' => true,
            'supports' => array('title')
        )
    );

    // flush rewrite rules if option is set to true, then delete option. This prevents this function from running more than once
    if (get_option('youtube_feed_flush') == 'true') {
        flush_rewrite_rules();
        delete_option('youtube_feed_flush');
    }
}

// registers playlist taxonomy
add_action('init', 'register_youtube_playlists');
function register_youtube_playlists() {
    $labels = array(
        'name' => _x( 'Playlists', 'general name' ),
        'singular_name' => _x( 'Playlist', 'single name' ),
        'search_items' =>  __( 'Search Playlists' ),
        'all_items' => __( 'All Playlists' ),
        'parent_item' => __( 'Parent Playlist' ),
        'parent_item_colon' => __( 'Parent Playlist:' ),
        'edit_item' => __( 'Edit Playlist' ), 
        'update_item' => __( 'Update Playlist' ),
        'add_new_item' => __( 'Add New Playlist' ),
        'new_item_name' => __( 'New Playlist Name' ),
        'menu_name' => __( 'Playlists' ),
    ); 

    register_taxonomy('playlists',array('youtube_videos'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'playlist' ),
     ));
}
