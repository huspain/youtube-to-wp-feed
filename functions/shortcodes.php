<?php
/**
* functions to display shortcodes
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// table to display youtube videos
add_shortcode('youtube-playlist', 'get_youtube_playlist');
function get_youtube_playlist($atts) {
	
$atts = shortcode_atts( array(
    'playlist' => '',
    'link' => ''
), $atts );

if ($atts['playlist'] != '') {
    $args = array(
        'post_type' => 'youtube_videos',
        'order_by' => 'date',
        'order' => 'DESC',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'playlists',
                'terms' => array($atts['playlist']),
                'field' => 'slug',
                'operator' => 'IN'
            )
        )
    );
}
else {
    $args = array(
        'post_type' => 'youtube_videos',
        'order_by' => 'date',
        'order' => 'DESC',
        'posts_per_page' => -1,
    );
}
$query = new WP_Query($args);

ob_start();

if($query->have_posts()) { ?>
    <div class="youtube-feed-playlist-container">
    <?php
    while($query->have_posts()) {
        $query->the_post();
        if ($atts['link'] == 'youtube') {
            $video_link = 'https://www.youtube.com/watch?v=' . get_field('youtube_id');
        }
        else {
            $video_link = get_the_permalink();
        }
        ?>
        <div class="youtube-feed-playlist-item">
            <a href="<?php echo $video_link; ?>" target="_blank">
                <h3><?php the_title(); ?></h3>
            </a>
        </div>
   <?php }
    wp_reset_postdata();
    ?>
    </div>
    <?php
}
else {
    ?>
    <p>Invalid or empty playlist.</p>
    <?php
}

$output = ob_get_contents();
ob_end_clean();
return $output;
}

// displays youtube video based on post ID
add_shortcode('youtube-iframe', 'get_youtube_iframe');
function get_youtube_iframe($atts) {

    $atts = shortcode_atts( array(
        'post_id' => '',
    ), $atts );

    if(empty($atts['post_id'])) {
        $post_id = get_the_id();
    }
    else {
        $post_id = $atts['post_id'];
    }
    
    if (get_post_type($post_id) !== 'youtube_videos') {
        return 'Invalid post ID';
    }

    $video_id = get_field('youtube_id', $post_id);

    if (empty($video_id)) {
        return 'Invalid video ID';
    }

    ob_start();

    ?>
    <div class="youtube-feed-video-container">
        <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $video_id ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    </div>
   <?php

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}