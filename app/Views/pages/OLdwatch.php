<?php
/**
 * Expects a $movie variable with movie details.
 */
$movieSlug = $movie['slug'] ?? '#';
?>
<div class="watch-container">
    <div class="watch-header">
        <a href="/movies/<?php echo htmlspecialchars($movieSlug); ?>" class="back-button">&larr; Back to Movie Details</a>
        <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
    </div>
    <div class="video-player-wrapper">
        <!-- This is the new, enhanced Video.js player structure -->
        <video
            id="moviePlayer"
            class="video-js vjs-big-play-centered vjs-theme-sea"
            controls
            preload="auto"
            autoplay="true"
            width="1600"
            height="900"
            data-setup="{}"
        >
            <source src="<?php echo htmlspecialchars($movie['video_path']); ?>" type="video/mp4" />
            <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a
                web browser that
                <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>
        </video>
    </div>
</div>