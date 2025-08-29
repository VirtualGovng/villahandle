<div class="card">
    <div class="card-header"><h3>Edit Movie: <?php echo htmlspecialchars($movie['title']); ?></h3></div>
    <div class="card-body">
         <form action="/admin/movies/<?php echo $movie['id']; ?>/update" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5"><?php echo htmlspecialchars($movie['description']); ?></textarea>
            </div>
             <div class="form-group">
                <label for="video_file">New Movie Video File (Optional)</label>
                <input type="file" id="video_file" name="video_file" class="form-control">
                <small class="form-text">Upload a new file to replace the current one. Current file: <?php echo htmlspecialchars($movie['video_path'] ?? 'None'); ?></small>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" id="release_date" name="release_date" class="form-control" value="<?php echo htmlspecialchars($movie['release_date']); ?>">
                </div>
                <div class="form-group">
                    <label for="poster_path">Poster URL</label>
                    <input type="url" id="poster_path" name="poster_path" class="form-control" value="<?php echo htmlspecialchars($movie['poster_path']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="genre">Genre</label>
                    <input type="text" id="genre" name="genre" class="form-control" value="<?php echo htmlspecialchars($movie['genre']); ?>">
                </div>
                 <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="published" <?php echo ($movie['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                        <option value="draft" <?php echo ($movie['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_premium">Premium Content</label>
                     <select id="is_premium" name="is_premium" class="form-control">
                        <option value="0" <?php echo ($movie['is_premium'] == 0) ? 'selected' : ''; ?>>No (Free)</option>
                        <option value="1" <?php echo ($movie['is_premium'] == 1) ? 'selected' : ''; ?>>Yes (Premium)</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/admin/movies" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>