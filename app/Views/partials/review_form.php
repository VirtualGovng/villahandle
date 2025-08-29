<div class="review-form-container card">
    <h3>Leave a Review</h3>
    <p>Share your thoughts with the community.</p>
    <form action="/reviews/store" method="POST" class="review-form">
        <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
        <input type="hidden" name="movie_slug" value="<?php echo $movie['slug']; ?>">
        
        <div class="form-group">
            <label>Your Rating</label>
            <div class="star-rating">
                <?php for ($i = 10; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required />
                    <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stars">&#9733;</label>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="comment">Your Review</label>
            <textarea id="comment" name="comment" rows="5" placeholder="What did you think of the movie?" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Post Review</button>
    </form>
</div>