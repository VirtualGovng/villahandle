<div class="page-hero">
    <div class="container">
        <h1><?php echo htmlspecialchars($pageHeader ?? 'Browse Series'); ?></h1>
    </div>
</div>
<div class="container content-container">
    <?php if (!empty($series)): ?>
        <div class="movie-grid large-grid">
            <?php foreach ($series as $item): 
                // We can reuse the movie_card partial by mapping the data
                $movie = [
                    'slug' => $item['slug'],
                    'title' => $item['title'],
                    'poster_path' => $item['poster_path'],
                    'release_date' => $item['release_date'],
                    'genre' => $item['genre'],
                ];
                // Manually change the link path for series
                $movieCardLink = "/series/" . htmlspecialchars($movie['slug']);
            ?>
                <div class="movie-card">
                    <a href="<?php echo $movieCardLink; ?>" class="card-link" title="<?php echo htmlspecialchars($movie['title']); ?>">
                        <div class="card-poster">
                            <img src="<?php echo htmlspecialchars($movie['poster_path'] ?? 'https://placehold.co/500x750/1a1d23/ffffff?text=No+Poster'); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?> Poster" loading="lazy">
                        </div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h4>
                            <span class="card-meta"><?php echo isset($movie['release_date']) ? date('Y', strtotime($movie['release_date'])) : 'N/A'; ?> &bull; Series</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php // Pagination would go here ?>
    <?php else: ?>
        <div class="no-results">
            <h2>No Series Found</h2>
            <p>Check back later for new and exciting series.</p>
        </div>
    <?php endif; ?>
</div>