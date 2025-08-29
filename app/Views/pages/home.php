<?php
// Use the featured movie for the hero section, with fallbacks.
$heroTitle = $featuredMovie['title'] ?? 'Stories That Connect Us';
$heroDesc = $featuredMovie['description'] ?? 'From Nollywood blockbusters to global hits and timeless classics, discover films that celebrate culture and tell amazing stories.';
$heroImage = $featuredMovie['poster_path'] ?? 'https://placehold.co/1920x1080/1a1d23/ffffff?text=VillaStudio';

// THE FIX: Use the reliable 'slug' from the database and determine the correct "Watch Now" URL.
$heroSlug = $featuredMovie['slug'] ?? '#';
$watchUrl = !empty($featuredMovie['video_path']) ? '/watch/' . $heroSlug : '/movies/' . $heroSlug;
?>
<section class="hero-section" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('<?php echo htmlspecialchars($heroImage); ?>');">
    <div class="hero-content">
        <span class="hero-subtitle">Featured Film</span>
        <h2 class="hero-title"><?php echo htmlspecialchars($heroTitle); ?></h2>
        <p class="hero-description"><?php echo htmlspecialchars(substr($heroDesc, 0, 150)); ?><?php echo strlen($heroDesc) > 150 ? '...' : ''; ?></p>
        <div class="hero-actions">
            <a href="<?php echo htmlspecialchars($watchUrl); ?>" class="btn btn-primary btn-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>
                Watch Now
            </a>
            <a href="/movies/<?php echo htmlspecialchars($heroSlug); ?>" class="btn btn-secondary btn-lg">More Info</a>
        </div>
    </div>
</section>

<div class="container content-container">
    
    <?php if (!empty($globalHits)): ?>
    <section class="content-row">
        <div class="row-header">
            <h3>Global Hits</h3>
            <a href="/movies?genre=general" class="view-all">View All</a>
        </div>
        <div class="movie-grid">
            <?php foreach ($globalHits as $movie): ?>
                <?php include VIEWS_PATH . '/partials/movie_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    
    <?php if (!empty($nollywoodMovies)): ?>
    <section class="content-row">
        <div class="row-header">
            <h3>Nollywood Blockbusters</h3>
            <a href="/movies?genre=nollywood" class="view-all">View All</a>
        </div>
        <div class="movie-grid">
            <?php foreach ($nollywoodMovies as $movie): ?>
                <?php include VIEWS_PATH . '/partials/movie_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($africanOriginals)): ?>
    <section class="content-row">
        <div class="row-header">
            <h3>More From Africa</h3>
            <a href="/movies?genre=african" class="view-all">View All</a>
        </div>
        <div class="movie-grid">
            <?php foreach ($africanOriginals as $movie): ?>
                <?php include VIEWS_PATH . '/partials/movie_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</div>