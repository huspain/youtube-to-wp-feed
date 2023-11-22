<?php
/**
* Functions to import / update youtube videos and playlists
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// on Youtube Feed Settings options page save, execute functions
add_action('acf/options_page/save', 'youtube_to_wp_feed_save_options_page', 10, 2);
function youtube_to_wp_feed_save_options_page( $post_id, $menu_slug ) {

    if ( 'youtube-to-wp-settings' !== $menu_slug ) {
        return;     
    }
    else {
        sync_youtube_exec();
    }
}

// import videos from channel upload by YouTube Data API and update wordpress database
add_action( 'sync_youtube_hook', 'sync_youtube_exec' ); 
function sync_youtube_exec() {

    // set youtube data API variables
    $api_key = get_field('youtube_data_api_key', 'option'); 
    $channel_id = get_field('youtube_channel_id', 'option'); 
    $additional_playlists = get_field('additional_youtube_playlists_repeater', 'option');

    if(empty($api_key)) {
        error_log('Error: Invalid API key');
        return false;
    }
    else if(empty($channel_id)) {
        error_log('Error: Invalid Channel ID');
        return false;
    } 

    // get uploads playlist ID
    $uploads_playlist_id = get_uploads_playlist_id($api_key, $channel_id);
    if(empty($uploads_playlist_id)) {
        error_log('Error: Invalid Uploads Playlist ID');
        return false;
    }
    
    // Get videos from channel uploads by YouTube Data API 
    $video_list = fetch_videos($api_key, $uploads_playlist_id);

    // insert new videos 
    foreach ($video_list as $video) {
        insert_new_video($video);
    }

    // remove videos from db that are no longer on youtube
    delete_removed_videos($video_list);

    // Get playlists from channel by YouTube Data API 
    $playlist_list = fetch_playlists($api_key, $channel_id);

    // add any additional playlists from options page
    if(!empty($additional_playlists)) {
        $playlist_list = add_additonal_playlists($playlist_list, $additional_playlists);
    }

    // insert new playlists 
    foreach ($playlist_list as $playlist) {
        insert_new_playlist($playlist);
    }

    // update playlist terms for videos
    update_video_playlist_assignements($api_key);
}

// helper function to get uploads playlist ID 
function get_uploads_playlist_id($api_key, $channel_id) {

     // sets api request for channel data
    $api_data = @file_get_contents('https://youtube.googleapis.com/youtube/v3/channels?part=contentDetails&id=' . $channel_id . '&key=' . $api_key . ''); 

    // if request is successful assign channel data
    if($api_data){ 
        $channel_data = json_decode($api_data); 
    }
    else {
        return '';
    }

     // if there is video data, format video list
    if($channel_data && !empty($channel_data->items)){ 
        if(isset($channel_data->items[0]->contentDetails->relatedPlaylists->uploads)){ 
            return $channel_data->items[0]->contentDetails->relatedPlaylists->uploads;
        } 
        else {
            return '';
        }
    }
    else {
        return '';
    }
}

// helper function to fetch all video IDs from a playlist
function fetch_videos($api_key, $uploads_playlist_id, $video_list = array(), $page_token = '') {

    // sets api request depending on whether a page token is provided
    if ($page_token == '') {
        $api_data = @file_get_contents('https://youtube.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId=' . $uploads_playlist_id . '&key=' . $api_key .''); 
    }
    else {
        $api_data = @file_get_contents('https://youtube.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&pageToken=' . $page_token . '&playlistId=' . $uploads_playlist_id . '&key=' . $api_key .''); 
    }

    // if request is successful assign video data
    if($api_data){ 
        $video_data = json_decode($api_data); 
    }
    else {
        return 'Error: invalid request';
    }

     // if there is video data, format video list
    if($video_data && !empty($video_data->items)){ 
        foreach($video_data->items as $item){ 
            array_push($video_list, array(
                'video_id' => $item->snippet->resourceId->videoId, 
                "title" => $item->snippet->title,
                "thumbnail_url" => $item->snippet->thumbnails->default->url,
                "description" => $item->snippet->description,
                "date" => $item->snippet->publishedAt,
            ));
        } 
    }

    // If api data indicates that there are more video items, request more videos until all are fetched. Otherwise, return full list.
    if(isset($video_data->nextPageToken)) {
        return fetch_videos($api_key, $uploads_playlist_id, $video_list, $video_data->nextPageToken);
    }
    else {
        return $video_list;
    }
}

// helper function to fetch all playlist IDs from channel
function fetch_playlists($api_key, $channel_id, $playlist_list = array(), $page_token = '') {

    // sets api request depending on whether a page token is provided
    if ($page_token == '') {
        $api_data = @file_get_contents('https://youtube.googleapis.com/youtube/v3/playlists?part=snippet&channelId=' . $channel_id . '&maxResults=50&key=' . $api_key .''); 
    }
    else {
        $api_data = @file_get_contents('https://youtube.googleapis.com/youtube/v3/playlists?part=snippet&channelId=' . $channel_id . '&pageToken=' . $page_token . '&maxResults=50&key=' . $api_key .''); 
    }

    // if request is successful assign playlist data
    if($api_data){ 
        $playlist_data = json_decode($api_data); 
    }
    else {
        return 'Error: invalid request';
    }

    // if there is playlist data, format playlist lists
    if($playlist_data && !empty($playlist_data->items)){ 
        foreach($playlist_data->items as $item){ 
            if(isset($item->id)){ 
                array_push($playlist_list, array(
                    'playlist_id' => $item->id, 
                    "title" => $item->snippet->title
            ));
            } 
        } 
    }

    // If api data indicates that there are more playlist items, request more playlists until all are fetched. Otherwise, return full list.
    if(isset($playlist_data->nextPageToken)) {
        return fetch_playlists($api_key, $channel_id, $playlist_list, $playlist_data->nextPageToken);
    }
    else {
        return $playlist_list;
    }
}

// add additional playlists from options menu
function add_additonal_playlists($playlist_list, $additional_playlists) {
    if (!empty($additional_playlists)) {
        foreach($additional_playlists as $additional_playlist) {
            array_push($playlist_list, ['playlist_id' => $additional_playlist['additional_youtube_playlist_id'], "title" => $additional_playlist['additional_youtube_playlist_title']]);
        }
    }

    return $playlist_list;
}

// helper function to insert new videos
function insert_new_video($video) {
    global $wpdb;
    $posts_table = $wpdb->prefix . 'posts';
    $postmeta_table = $wpdb->prefix . 'postmeta';

    $video_id = $video['video_id'];
    $video_title = $video['title'];
    $video_thumbnail = $video['thumbnail_url'];
    $video_description = $video['description'];
    $video_date = $video['date'];

    $sql = "SELECT `post_id` FROM $postmeta_table WHERE `meta_key` = 'youtube_id' AND `meta_value` = '%s'";
    $sql = $wpdb->prepare($sql, array($video_id));
    $video_in_db = $wpdb->get_results($sql);
    if(empty($video_in_db)) {
        $wpdb->insert( $posts_table, array( 
            'post_title' => $video_title, 
            'post_type' => 'youtube_videos', 
            'post_date' => $video_date,
            'post_date_gmt' => $video_date,
            'post_name' => sanitize_title($video_title),
            ) 
        );
        $inserted_id = $wpdb->insert_id;
        $wpdb->insert( $postmeta_table, array( 
            'post_id' => $inserted_id, 
            'meta_key' => 'youtube_id', 
            'meta_value' => $video_id 
            ) 
        );
        $wpdb->insert( $postmeta_table, array( 
            'post_id' => $inserted_id, 
            'meta_key' => 'youtube_thumbnail', 
            'meta_value' => $video_thumbnail 
            ) 
        );
        $wpdb->insert( $postmeta_table, array( 
            'post_id' => $inserted_id, 
            'meta_key' => 'youtube_description', 
            'meta_value' => $video_description 
            ) 
        );
    }
}

// helper function to delete videos that are not in provided video list
function delete_removed_videos($video_list) {
    $video_id_list = esc_sql(array_column($video_list, 'video_id'));

    global $wpdb;
    $posts_table = $wpdb->prefix . 'posts';
    $postmeta_table = $wpdb->prefix . 'postmeta';
    $video_list_string = implode("', '", $video_id_list);

    $sql = "SELECT `post_id` FROM $postmeta_table WHERE `meta_key` = 'youtube_id' AND `meta_value` NOT IN ('$video_list_string')";
    $videos_to_delete = array_column($wpdb->get_results($sql, ARRAY_A), 'post_id');
    $videos_to_delete = implode("', '", $videos_to_delete);

    $sql = "DELETE FROM $posts_table WHERE `ID` IN ('$videos_to_delete')";
    $wpdb->query($sql);

    $sql = "DELETE FROM $postmeta_table WHERE `post_id` IN ('$videos_to_delete')";
    $wpdb->query($sql);
}

// helper function to insert new playlists
function insert_new_playlist($playlist) {
    $youtube_playlist_id = $playlist['playlist_id'];
    $youtube_playlist_title = $playlist['title'];

    if(!term_exists(sanitize_title($youtube_playlist_title), 'playlists')) {
        $new_playlist = wp_insert_term($youtube_playlist_title, 'playlists', array(
            'slug' => sanitize_title($youtube_playlist_title)
        ));

        $wp_playlist_id = 'playlists_' . $new_playlist['term_id'];

        update_field('youtube_playlist_id', $youtube_playlist_id, $wp_playlist_id);
    }
}

// update playlist term assignement for videos
function update_video_playlist_assignements($api_key) {
    $terms = get_terms (array(
        'taxonomy' => 'playlists',
        'hide_empty' => false,
        )
    );

    foreach($terms as $term) {
        $youtube_playlist_id = get_field('youtube_playlist_id', 'playlists_' . $term->term_id);

        if (empty($youtube_playlist_id)) {
            continue;
        }

        $video_list = fetch_videos($api_key, $youtube_playlist_id);

        foreach($video_list as $video) {
            assign_playlist($video, $term);
        }
    }
}

// helper function to query database for video and assign playlist terms
function assign_playlist($video, $playlist_term) {
    $args = array(
        'post_type' => 'youtube_videos',
        'meta_query' => array(
           array(
              'key' => 'youtube_id',
              'value' => $video['video_id'],
              'compare' => '=',
           )
        )
     );
     $query = new WP_Query($args);

     if ($query->have_posts()) {
        while($query->have_posts()) {
            $query->the_post();
            if(!has_term($playlist_term->term_id, 'playlists')) {
                wp_set_post_terms(get_the_ID(), $playlist_term->term_id, 'playlists', true);
            }
        }
        wp_reset_postdata();
     }
}
