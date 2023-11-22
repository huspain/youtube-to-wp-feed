<?php
/**
* Sets custom ACF fields/options for plugin use
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// add options page for youtube feed plugin
if( function_exists('acf_add_options_page') ) {
      acf_add_options_page(array(
        'page_title'    => __('Youtube To WP Feed Settings'),
        'menu_title'    => __('Youtube To WP Feed Settings'),
        'menu_slug'     => 'youtube-to-wp-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

// add custom fields
if( function_exists('acf_add_local_field_group') ) {
    // to youtube video posts
    acf_add_local_field_group(array (
        'key' => 'youtube_group',
        'title' => 'Youtube Field Group',
        'fields' => array (
            array (
                'key' => 'youtube_id',
                'label' => 'Youtube ID',
                'name' => 'youtube_id',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'youtube_description',
                'label' => 'Youtube Description',
                'name' => 'youtube_description',
                'type' => 'wysiwyg',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'youtube_thumbnail',
                'label' => 'Youtube Thumbnail',
                'name' => 'youtube_thumbnail',
                'type' => 'url',
                'prefix' => '',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'youtube_videos',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
    ));   
    
    // to playlists taxonomy
    acf_add_local_field_group(array (
        'key' => 'youtube_playlist_group',
        'title' => 'Youtube Playlist Field Group',
        'fields' => array (
            array (
                'key' => 'youtube_playlist_id',
                'label' => 'Youtube Playlist ID',
                'name' => 'youtube_playlist_id',
                'type' => 'text',
                'prefix' => '',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'playlists',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
    ));  

    // to options page
    acf_add_local_field_group(array (
        'key' => 'youtube_feed_options_group',
        'title' => 'Youtube Feed Options Field Group',
        'fields' => array (
            array (
                'key' => 'general_information',
                'label' => 'General Information',
                'name' => 'general_information',
                'type' => 'message',
                'prefix' => '',
                'message' => 'While this plugin is active, youtube videos and playlists will automatically sync daily. In order to manually sync videos and playlists, update this options page. Every time these settings are updated, videos and playlists will sync.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'shortcode_information',
                'label' => 'Shortcode Information',
                'name' => 'shortcode_information',
                'type' => 'message',
                'prefix' => '',
                'message' => 'To display playlists as a feed, use this shortcode: <strong>[youtube-playlist playlist="{playlist-slug}" link="youtube"]</strong>.<br>
                    <ul style="padding-left: 30px;">
                    <li>The <strong>"playlist"</strong> parameter indicates which playlist to display. Use the slug of a "playlists" taxonomy term for the value. If this is not specified, it will default to display all videos.</li>
                    <li>The <strong>"link"</strong> parameter specifies where each video in the feed will link to. If this value is set to "youtube", then each video will link to its respective video on "youtube.com." If any other value is given, or if left unspecified, then each video will link to its respective post on the website.</li> 
                    </ul><br><br>
                    To display videos as an iframe, use this shortcode: <strong>[youtube-iframe post_id="{post_id}"]</strong>.<br>
                    <ul style="padding-left: 30px;">
                    <li>The <strong>"post_id"</strong> parameter accepts the post ID of the youtube video in WordPress (not the video ID in youtube.com). Defaults to the current post ID.</li>
                    </ul>',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'youtube_data_api_key',
                'label' => 'Youtube Data V3 API Key',
                'name' => 'youtube_data_api_key',
                'type' => 'text',
                'prefix' => '',
                'instructions' => 'API Key can be obtained from the Google Developers Console. More information can be found <a href="https://developers.google.com/youtube/v3/getting-started" target="_blank">here</a>.<br><br>',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'youtube_channel_id',
                'label' => 'Youtube Channel ID',
                'name' => 'youtube_channel_id',
                'type' => 'text',
                'prefix' => '',
                'instructions' => 'Channel ID of the youtube channel to pull information from.',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'additional_youtube_playlists_repeater',
                'label' => 'Additional Youtube Playlists',
                'name' => 'additional_youtube_playlists_repeater',
                'type' => 'repeater',
                'instructions' => 'By default, public playlists are pulled. Add any additional playlists that may be unlisted or private.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => '',
                'min' => 0,
                'max' => 0,
                'layout' => 'table',
                'button_label' => '',
                'sub_fields' => array(
                    array(
                        'key' => 'additional_youtube_playlist_id',
                        'label' => 'Additional Youtube Playlist ID',
                        'name' => 'additional_youtube_playlist_id',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'additional_youtube_playlist_title',
                        'label' => 'Additional Youtube Playlist Title',
                        'name' => 'additional_youtube_playlist_title',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                ),
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'youtube-to-wp-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
    ));  
}