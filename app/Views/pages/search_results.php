<div class="page-hero">
    <div class="container">
        <h1><?php echo $pageHeader ?? 'Search Results'; ?></h1>
    </div>
</div>

<div class="container content-container">
    <?php if (!empty($movies)): ?>
        <div class="movie-grid large-grid">
            <?php foreach ($movies as $movie): ?>
                <?php include VIEWS_PATH . '/partials/movie_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <?php 
            // We need to pass the search query to the pagination partial
            // so it can build the correct links (e.g., /search?q=king&page=2)
            $paginationQuery = ['q' => $query];
            include VIEWS_PATH . '/partials/pagination.php'; 
        ?>

    <?php else: ?>
        <div class="no-results">
            <h2>No Movies Found</h2>
            <p>We couldn't find any movies matching your search for "<?php echo htmlspecialchars($query); ?>". Please try a different search term.</p>
            <a href="/" class="btn btn-primary">Back to Homepage</a>
        </div>
    <?php endif; ?>
</div>