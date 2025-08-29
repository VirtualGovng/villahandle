<div class="page-hero">
    <div class="container">
        <h1>Upload Your Film</h1>
        <p>Share your story with the VillaStudio community.</p>
    </div>
</div>

<div class="container content-container">
    <div class="auth-form-wrapper">
        <form action="/creator/movies/store" method="POST" enctype="multipart/form-data" class="auth-form">
            <div class="form-group">
                <label for="title">Film Title</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Synopsis</label>
                <textarea id="description" name="description" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="video_file">Video File</label>
                <input type="file" id="video_file" name="video_file" class="form-control" required accept="video/mp4,video/webm,video/ogg">
                <small class="form-text">Max size: 500MB. Allowed formats: MP4, WebM, OGG.</small>
            </div>
            <div class="form-group">
                <label for="poster_path">Poster URL</label>
                <input type="url" id="poster_path" name="poster_path" class="form-control" placeholder="https://image.tmdb.org/..." required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="release_date">Original Release Date</label>
                    <input type="date" id="release_date" name="release_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="genre">Genre</label>
                    <input type="text" id="genre" name="genre" class="form-control" placeholder="e.g., Nollywood Drama" required>
                </div>
            </div>
            <div class="form-group">
                <label for="is_premium">Monetization</label>
                 <select id="is_premium" name="is_premium" class="form-control">
                    <option value="0">Free (Ad-supported)</option>
                    <option value="1">Premium (Subscription)</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Submit for Review</button>
            </div>
        </form>
    </div>
</div>