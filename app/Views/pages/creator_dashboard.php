<div class="page-hero">
    <div class="container">
        <h1>Creator Dashboard</h1>
        <p>Manage your films, track their status, and share your stories with the world.</p>
    </div>
</div>

<div class="container content-container">
    <div class="creator-header">
        <h2>My Film Submissions</h2>
        <a href="/creator/movies/upload" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"></path></svg>
            <span>Upload New Film</span>
        </a>
    </div>

    <div class="creator-film-list">
        <?php if (!empty($myMovies)): ?>
            <?php foreach($myMovies as $movie): ?>
                <div class="film-card card">
                    <div class="film-card-poster">
                        <img src="<?php echo htmlspecialchars($movie['poster_path'] ?? 'https://placehold.co/500x750/1a1d23/ffffff?text=No+Poster'); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?> Poster">
                    </div>
                    <div class="film-card-body">
                        <div class="film-card-header">
                            <h3 class="film-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <span class="badge status-<?php echo strtolower($movie['status']); ?>"><?php echo ucfirst($movie['status']); ?></span>
                        </div>
                        <p class="film-meta">
                            <span><?php echo htmlspecialchars($movie['genre']); ?></span> |
                            <span>Released: <?php echo date('M j, Y', strtotime($movie['release_date'])); ?></span>
                        </p>
                        <p class="film-description">
                            <?php echo htmlspecialchars(substr($movie['description'], 0, 150)); ?><?php echo strlen($movie['description']) > 150 ? '...' : ''; ?>
                        </p>
                        <div class="film-card-actions">
                            <?php if (in_array($movie['status'], ['pending', 'draft', 'rejected'])): ?>
                                <a href="/creator/movies/<?php echo $movie['id']; ?>/edit" class="btn btn-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path></svg>
                                    <span>Edit Details</span>
                                </a>
                            <?php else: ?>
                                <span class="action-text text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg>
                                    <span>Published</span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results-box">
                <h3>You haven't uploaded any films yet.</h3>
                <p>Ready to share your story? Click the button below to get started.</p>
                <a href="/creator/movies/upload" class="btn btn-primary btn-lg">Upload Your First Film</a>
            </div>
        <?php endif; ?>
    </div>
</div>