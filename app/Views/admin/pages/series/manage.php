<h3>Manage Seasons & Episodes for "<?php echo htmlspecialchars($series['title']); ?>"</h3>

<div class="manage-grid">
    <div class="seasons-panel">
        <div class="card">
            <div class="card-header"><h4>Seasons</h4></div>
            <div class="card-body">
                <?php if (!empty($series['seasons'])): ?>
                    <ul class="item-list">
                        <?php foreach($series['seasons'] as $season): ?>
                            <li>Season <?php echo $season['season_number']; ?> (<?php echo count($season['episodes']); ?> episodes)</li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No seasons added yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5>Add New Season</h5></div>
            <div class="card-body">
                <form action="/admin/series/<?php echo $series['id']; ?>/seasons/store" method="POST">
                    <div class="form-group"><label for="season_number">Season Number</label><input type="number" name="season_number" class="form-control" required></div>
                    <div class="form-group"><label for="title">Season Title (Optional)</label><input type="text" name="title" class="form-control"></div>
                    <button type="submit" class="btn btn-primary">Add Season</button>
                </form>
            </div>
        </div>
    </div>

    <div class="episodes-panel">
        <?php foreach($series['seasons'] as $season): ?>
            <div class="card">
                <div class="card-header"><h4>Episodes for Season <?php echo $season['season_number']; ?></h4></div>
                <div class="card-body">
                    <?php if (!empty($season['episodes'])): ?>
                        <ul class="item-list">
                        <?php foreach($season['episodes'] as $episode): ?>
                            <li>Ep <?php echo $episode['episode_number']; ?>: <?php echo htmlspecialchars($episode['title']); ?></li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No episodes added for this season yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5>Add New Episode to Season <?php echo $season['season_number']; ?></h5></div>
                <div class="card-body">
                    <form action="/admin/seasons/<?php echo $season['id']; ?>/episodes/store" method="POST" enctype="multipart/form-data">
                        <div class="form-group"><label>Episode Number</label><input type="number" name="episode_number" class="form-control" required></div>
                        <div class="form-group"><label>Episode Title</label><input type="text" name="title" class="form-control" required></div>
                        <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                        <div class="form-group"><label>Video File</label><input type="file" name="video_file" class="form-control" required></div>
                        <button type="submit" class="btn btn-primary">Add Episode</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>