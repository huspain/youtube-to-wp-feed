# Youtube To WP Feed
A WordPress plugin that imports Youtube videos and playlists into the website.<br>

<strong>General Information:</strong><br><br>
This plugin utilizes Youtube Data API V3 and the plugin, <a href="https://www.advancedcustomfields.com/pro/" target="_blank">Advanced Custom Fields Pro</a>. ACF Pro must be active in order to activate this plugin.

The following values are required in order to import Youtube videos and playlists:
<ul>
    <li>Youtube Data API Key</li>
    <li>Channel ID of the Youtube channel to pull information from</li>
</ul>

The API Key can be obtained from the Google Developers Console. More information can be found <a href="https://developers.google.com/youtube/v3/getting-started" target="_blank">here</a>.

The Channel ID can be found on the desired Youtube channel's web page. More information can be found <a href="https://support.google.com/youtube/answer/3250431" target="_blank">here</a>.

While this plugin is active, yYoutube videos and playlists will automatically sync daily. In order to manually sync videos and playlists, update the options page. Every time the settings are updated, videos and playlists will sync.<br>
 
<strong>Shortcode Information:</strong><br><br>
To display playlists as a feed, use this shortcode: <strong>[youtube-playlist playlist="{playlist-slug}" link="youtube"]</strong>.<br>
<ul >
    <li>The <strong>"playlist"</strong> parameter indicates which playlist to display. Use the slug of a "playlists" taxonomy term for the value. If this is not specified, it will default to display all videos.</li>
    <li>The <strong>"link"</strong> parameter specifies where each video in the feed will link to. If this value is set to "youtube", then each video will link to its respective video on "youtube.com." If any other value is given, or if left unspecified, then each video will link to its respective post on the website.</li> 
</ul>
<br>
To display videos as an iframe, use this shortcode: <strong>[youtube-iframe post_id="{post_id}"]</strong>.<br>
<ul>
    <li>The <strong>"post_id"</strong> parameter accepts the post ID of the youtube video in WordPress (not the video ID in youtube.com). Defaults to the current post ID.</li>
</ul>