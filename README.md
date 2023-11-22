# Youtube To WP Feed
A WordPress plugin that imports Youtube videos and playlists to the website.
 

 To display playlists as a feed, use this shortcode: <strong>[youtube-playlist playlist="{playlist-slug}" link="youtube"]</strong>.<br>
<ul style="padding-left: 30px;">
<li>The <strong>"playlist"</strong> parameter indicates which playlist to display. Use the slug of a "playlists" taxonomy term for the value. If this is not specified, it will default to display all videos.</li>
<li>The <strong>"link"</strong> parameter specifies where each video in the feed will link to. If this value is set to "youtube", then each video will link to its respective video on "youtube.com." If any other value is given, or if left unspecified, then each video will link to its respective post on the website.</li> 
</ul><br><br>
To display videos as an iframe, use this shortcode: <strong>[youtube-iframe post_id="{post_id}"]</strong>.<br>
<ul style="padding-left: 30px;">
<li>The <strong>"post_id"</strong> parameter accepts the post ID of the youtube video in WordPress (not the video ID in youtube.com). Defaults to the current post ID.</li>
</ul>