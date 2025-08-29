<?php
/**
 * Expects a $review variable
 */
?>
<div class="review-item card">
    <div class="review-author">
        <div class="author-avatar">
            <span><?php echo strtoupper(substr($review['username'], 0, 1)); ?></span>
        </div>
        <div class="author-info">
            <span class="author-name"><?php echo htmlspecialchars($review['username']); ?></span>
            <span class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></span>
        </div>
    </div>
    <div class="review-content">
        <div class="review-rating">
            <?php for ($i = 0; $i < 10; $i++): ?>
                <span class="star <?php echo ($i < $review['rating']) ? 'filled' : ''; ?>">&#9733;</span>
            <?php endfor; ?>
        </div>
        <p class="review-body"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
    </div>
</div>