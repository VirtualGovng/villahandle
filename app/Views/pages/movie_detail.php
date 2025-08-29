<?php
/**
 * Expects:
 * @var array $movie (from our local DB)
 * @var array|null $tmdb (from TMDB API)
 * @var \App\Services\ApiService $apiService
 * @var array $relatedMovies
 */

// Use TMDB data if available, otherwise fall back to our local DB or defaults.
$title = $movie['title'];
$description = $tmdb['overview'] ?? $movie['description'] ?? 'No description available.';
$releaseYear = isset($tmdb['release_date']) ? date('Y', strtotime($tmdb['release_date'])) : (isset($movie['release_date']) ? date('Y', strtotime($movie['release_date'])) : 'N/A');
$duration = $movie['duration_minutes'] ? $movie['duration_minutes'] . ' min' : 'N/A';
$genre = $movie['genre'] ?? 'Film';
$rating = isset($tmdb['vote_average']) && $tmdb['vote_average'] > 0 ? round($tmdb['vote_average'], 1) . ' / 10' : 'N/A';

$backdropUrl = $apiService->getImageUrl($tmdb['backdrop_path'] ?? null, 'original');
$posterUrl = $apiService->getImageUrl($tmdb['poster_path'] ?? $movie['poster_path'], 'w500');
$movieSlug = $movie['slug'] ?? '#';

// --- BATCH 11 LOGIC ---
$reviewModel = new \App\Models\Review();
$reviews = $reviewModel->getByMovieId($movie['id']);

// --- THIS IS THE FIX ---
// We now pass the third argument, 'movie', to the hasUserReviewed function.
$canReview = \App\Services\AuthService::check() && !$reviewModel->hasUserReviewed(\App\Services\AuthService::id(), $movie['id'], 'movie');
// --- END OF FIX ---
?>

<div class="movie-detail-hero" style="background-image: url('<?php echo htmlspecialchars($backdropUrl); ?>');">
    <div class="backdrop-overlay"></div>
    <div class="container detail-content">
        <div class="detail-poster">
            <img src="<?php echo htmlspecialchars($posterUrl); ?>" alt="<?php echo htmlspecialchars($title); ?> Poster">
        </div>
        <div class="detail-info">
            <h1 class="detail-title"><?php echo htmlspecialchars($title); ?></h1>
            <div class="detail-meta">
                <span><?php echo htmlspecialchars($releaseYear); ?></span>
                <span>&bull;</span>
                <span><?php echo htmlspecialchars($genre); ?></span>
                <span>&bull;</span>
                <span><?php echo htmlspecialchars($duration); ?></span>
                <?php if ($rating !== 'N/A'): ?>
                <span class="tmdb-rating">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                    <?php echo htmlspecialchars($rating); ?>
                </span>
                <?php endif; ?>
            </div>
            <p class="detail-description">
                <?php echo htmlspecialchars($description); ?>
            </p>
            <div class="detail-actions">
                <?php if (!empty($movie['video_path'])): ?>
                    <a href="/watch/<?php echo htmlspecialchars($movieSlug); ?>" class="btn btn-primary btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>
                        Play Movie
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($movie['trailer_url'])): ?>
                    <a href="<?php echo htmlspecialchars($movie['trailer_url']); ?>" class="btn btn-secondary btn-lg" target="_blank" rel="noopener noreferrer">
                        Watch Trailer
                    </a>
                <?php endif; ?>

                <button class="btn btn-secondary btn-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                    Add to Watchlist
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container content-container">
    <div class="detail-main-content">
        <section class="content-row">
            <h3>More Like This</h3>
            <?php if (!empty($relatedMovies) && count($relatedMovies) > 1): ?>
                <div class="movie-grid">
                    <?php 
                    $originalMovie = $movie;
                    foreach ($relatedMovies as $relatedMovie): 
                        if ($relatedMovie['id'] === $originalMovie['id']) continue;
                        $movie = $relatedMovie; 
                        include VIEWS_PATH . '/partials/movie_card.php'; 
                    endforeach; 
                    $movie = $originalMovie;
                    ?>
                </div>
            <?php else: ?>
                <p>No recommendations available at this time.</p>
            <?php endif; ?>
        </section>

        <section class="reviews-section">
            <h3>Reviews & Community Discussion</h3>
            
            <?php if (\App\Services\AuthService::check()): ?>
                <?php if ($canReview): ?>
                    <?php include VIEWS_PATH . '/partials/review_form.php'; ?>
                <?php else: ?>
                    <div class="reviewed-notice card">
                        <p>You've already reviewed this movie. Thank you for your feedback!</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="login-notice card">
                    <p><a href="/login">Sign in</a> or <a href="/register">create an account</a> to leave a review.</p>
                </div>
            <?php endif; ?>

            <div class="review-list">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <?php include VIEWS_PATH . '/partials/review_item.php'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-reviews card">
                        <p>There are no reviews for this movie yet. Be the first to share your thoughts!</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
    <div class="detail-sidebar">
        <!-- Sidebar content can be added here in a future batch -->
    </div>
</div>

<script src="/public/js/community.js"></script>