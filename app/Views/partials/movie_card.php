<?php
/**
 * Expects a $movie variable to be available.
 */
$posterUrl = $movie['poster_path'] ?? 'https://placehold.co/500x750/1a1d23/ffffff?text=No+Image';
$releaseYear = isset($movie['release_date']) ? date('Y', strtotime($movie['release_date'])) : 'N/A';
$movieSlug = $movie['slug'] ?? '#'; // Use the reliable slug from the database
?>
<div class="movie-card">
    <a href="/movies/<?php echo htmlspecialchars($movieSlug); ?>" class="card-link" title="<?php echo htmlspecialchars($movie['title']); ?>">
        <div class="card-poster">
            <img src="<?php echo htmlspecialchars($posterUrl); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?> Poster" loading="lazy">
            <div class="poster-overlay">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>
            </div>
        </div>
        <div class="card-body">
            <h4 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h4>
            <span class="card-meta"><?php echo htmlspecialchars($releaseYear); ?> &bull; <?php echo htmlspecialchars($movie['genre'] ?? 'Film'); ?></span>
        </div>
    </a>
</div>