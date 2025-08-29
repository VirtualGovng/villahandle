<div class="card">
    <div class="card-header">
        <h3>General Site Settings</h3>
    </div>
    <div class="card-body">
        <form action="#" method="POST">
            <div class="form-group">
                <label for="site_name">Site Name</label>
                <input type="text" id="site_name" name="site_name" class="form-control" value="<?php echo env('APP_NAME'); ?>" disabled>
                <small class="form-text">This value is set in your .env file.</small>
            </div>
            <div class="form-group">
                <label for="tmdb_api_key">TMDB API Key</label>
                <input type="password" id="tmdb_api_key" name="tmdb_api_key" class="form-control" value="<?php echo env('TMDB_API_KEY'); ?>" disabled>
                <small class="form-text">This value is set in your .env file.</small>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" disabled>Save Settings (Disabled)</button>
            </div>
        </form>
    </div>
</div>