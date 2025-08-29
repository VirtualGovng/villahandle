<div class="page-hero">
    <div class="container">
        <h1><?php echo htmlspecialchars($pageHeader ?? 'Browse Movies'); ?></h1>
    </div>
</div>

<div class="container content-container">
    <?php if (!empty($movies)): ?>
        <div class="movie-grid large-grid">
            <?php foreach ($movies as $movie): ?>
                <?php include VIEWS_PATH . '/partials/movie_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <?php include VIEWS_PATH . '/partials/pagination.php'; ?>

    <?php else: ?>
        <div class="no-results">
            <h2>No Movies Found</h2>
            <p>We couldn't find any movies matching your criteria. Please try a different category or check back later.</p>
            <a href="/movies" class="btn btn-primary">View All Movies</a>
        </div>
    <?php endif; ?>
</div>