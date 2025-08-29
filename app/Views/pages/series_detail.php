<div class="movie-detail-hero" style="background-image: url('<?php echo htmlspecialchars($series['poster_path']); ?>');">
    <div class="backdrop-overlay"></div>
    <div class="container detail-content">
        <div class="detail-poster"><img src="<?php echo htmlspecialchars($series['poster_path']); ?>" alt="<?php echo htmlspecialchars($series['title']); ?> Poster"></div>
        <div class="detail-info">
            <h1 class="detail-title"><?php echo htmlspecialchars($series['title']); ?></h1>
            <p class="detail-description"><?php echo htmlspecialchars($series['description']); ?></p>
        </div>
    </div>
</div>

<div class="container content-container">
    <div class="series-episodes-section">
        <div class="season-tabs">
            <?php foreach($series['seasons'] as $index => $season): ?>
                <button class="season-tab-btn <?php echo ($index === 0) ? 'active' : ''; ?>" data-season="season-<?php echo $season['id']; ?>">
                    Season <?php echo $season['season_number']; ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <?php foreach($series['seasons'] as $index => $season): ?>
            <div id="season-<?php echo $season['id']; ?>" class="episode-list <?php echo ($index === 0) ? 'active' : ''; ?>">
                <?php foreach($season['episodes'] as $episode): ?>
                    <a href="#" class="episode-item">
                        <div class="episode-number"><?php echo $episode['episode_number']; ?></div>
                        <div class="episode-details">
                            <h4 class="episode-title"><?php echo htmlspecialchars($episode['title']); ?></h4>
                            <p class="episode-description"><?php echo htmlspecialchars($episode['description']); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = document.querySelectorAll('.season-tab-btn');
    const episodeLists = document.querySelectorAll('.episode-list');
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            episodeLists.forEach(list => list.classList.remove('active'));
            button.classList.add('active');
            document.getElementById(button.dataset.season).classList.add('active');
        });
    });
});
</script>