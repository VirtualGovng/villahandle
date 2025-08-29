<div class="page-hero">
    <div class="container">
        <h1><?php echo htmlspecialchars($pageHeader ?? 'Community Hub'); ?></h1>
        <p>See what the community is saying about the latest content.</p>
    </div>
</div>
<div class="container content-container">
    <div class="community-feed">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): 
                $link = ($review['content_type'] === 'movie') ? "/movies/{$review['content_slug']}" : "/series/{$review['content_slug']}";
            ?>
                <div class="review-item card">
                    <div class="review-author">
                        <div class="author-avatar"><span><?php echo strtoupper(substr($review['username'], 0, 1)); ?></span></div>
                        <div class="author-info">
                            <span class="author-name"><?php echo htmlspecialchars($review['username']); ?></span>
                            <span class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></span>
                        </div>
                    </div>
                    <div class="review-content">
                        <p class="review-context">Reviewed <a href="<?php echo $link; ?>"><strong><?php echo htmlspecialchars($review['content_title']); ?></strong></a></p>
                        <div class="review-rating">
                            <?php for ($i = 0; $i < 10; $i++): ?><span class="star <?php echo ($i < $review['rating']) ? 'filled' : ''; ?>">&#9733;</span><?php endfor; ?>
                        </div>
                        <p class="review-body"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results"><h2>No recent activity.</h2><p>Be the first to leave a review on a movie or series!</p></div>
        <?php endif; ?>
    </div>
</div>