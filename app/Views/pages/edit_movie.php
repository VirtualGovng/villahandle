<div class="page-hero">
    <div class="container">
        <h1>Edit Your Film</h1>
        <p>"<?php echo htmlspecialchars($movie['title']); ?>"</p>
    </div>
</div>

<div class="container content-container">
    <div class="auth-form-wrapper">
        <form action="/creator/movies/<?php echo $movie['id']; ?>/update" method="POST" class="auth-form">
            <div class="form-group">
                <label for="title">Film Title</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Synopsis</label>
                <textarea id="description" name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($movie['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="poster_path">Poster URL</label>
                <input type="url" id="poster_path" name="poster_path" class="form-control" value="<?php echo htmlspecialchars($movie['poster_path']); ?>" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="release_date">Original Release Date</label>
                    <input type="date" id="release_date" name="release_date" class="form-control" value="<?php echo htmlspecialchars($movie['release_date']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="genre">Genre</label>
                    <input type="text" id="genre" name="genre" class="form-control" value="<?php echo htmlspecialchars($movie['genre']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="is_premium">Monetization</label>
                 <select id="is_premium" name="is_premium" class="form-control">
                    <option value="0" <?php echo ($movie['is_premium'] == 0) ? 'selected' : ''; ?>>Free (Ad-supported)</option>
                    <option value="1" <?php echo ($movie['is_premium'] == 1) ? 'selected' : ''; ?>>Premium (Subscription)</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Update & Re-submit</button>
                <a href="/creator/dashboard" class="btn btn-secondary btn-block">Cancel</a>
            </div>
        </form>
    </div>
</div>