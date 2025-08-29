<div class="card">
    <div class="card-header"><h3>Add New Movie</h3></div>
    <div class="card-body">
        <form action="/admin/movies/store" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5"></textarea>
            </div>
            <div class="form-group">
                <label for="video_file">Movie Video File (Optional)</label>
                <input type="file" id="video_file" name="video_file" class="form-control">
                <small class="form-text">Upload the main video file here.</small>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" id="release_date" name="release_date" class="form-control">
                </div>
                <div class="form-group">
                    <label for="poster_path">Poster URL</label>
                    <input type="url" id="poster_path" name="poster_path" class="form-control" placeholder="https://image.tmdb.org/...">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="genre">Genre</label>
                    <input type="text" id="genre" name="genre" class="form-control" placeholder="e.g., Nollywood">
                </div>
                 <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="published">Published</option>
                        <option value="draft" selected>Draft</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_premium">Premium Content</label>
                     <select id="is_premium" name="is_premium" class="form-control">
                        <option value="0">No (Free)</option>
                        <option value="1">Yes (Premium)</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Movie</button>
                <a href="/admin/movies" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>