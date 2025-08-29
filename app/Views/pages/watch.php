<?php
/**
 * Expects a $movie variable with movie details.
 */
$movieSlug = $movie['slug'] ?? '#';
?>
<div class="watch-container cinema-mode">
    <div class="watch-header">
        <a href="/movies/<?php echo htmlspecialchars($movieSlug); ?>" class="back-button" title="Back to Details">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path></svg>
            <span>Back to Details</span>
        </a>
        <h1 class="watch-title"><?php echo htmlspecialchars($movie['title']); ?></h1>
        <button class="fullscreen-toggle" title="Toggle Fullscreen">
             <svg class="icon-fullscreen-enter" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"></path></svg>
             <svg class="icon-fullscreen-exit" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"></path></svg>
        </button>
    </div>
    <div class="video-player-wrapper">
        <video
            id="moviePlayer"
            class="video-js vjs-big-play-centered vjs-theme-sea"
            controls
            preload="auto"
            autoplay="true"
            data-setup='{ "fluid": true }'
        >
            <source src="<?php echo htmlspecialchars($movie['video_path']); ?>" type="video/mp4" />
            <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a
                web browser that
                <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>.
            </p>
        </video>
    </div>
</div>