<?php 
use App\Services\AuthService; 
// Increment version for new AdSense logic and CSS changes
$assetVersion = '1.5'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'VillaStudio'); ?></title>
    <meta name="description" content="Watching a movie on VillaStudio, your legal entertainment hub.">
    
    <meta name="referrer" content="no-referrer-when-downgrade">
    <meta name="theme-color" content="#0f1014"/>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Video.js CSS -->
    <link href="https://vjs.zencdn.net/8.5.2/video-js.css" rel="stylesheet" />
    
    <link rel="stylesheet" href="/public/css/style.css?v=<?php echo $assetVersion; ?>">
    
    <link rel="manifest" href="/public/manifest.json">
    <link rel="icon" href="/public/images/favicon.ico" type="image/x-icon">

    <?php // Conditionally include AdSense script for free movies ?>
    <?php if (isset($showAds) && $showAds === true): ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3299095953522582"
     crossorigin="anonymous"></script>
    <?php endif; ?>
</head>
<body class="cinema-layout">

    <header class="main-header">
        <div class="container">
            <a href="/" class="logo">
                <img src="/public/images/logo.png" alt="VillaStudio Logo">
                <h1>VillaStudio</h1>
            </a>
            <!-- Intentionally simplified header for cinema mode -->
            <div class="header-actions">
                 <div class="desktop-only">
                    <?php if (AuthService::check()): ?>
                        <a href="/profile" class="btn btn-secondary">Profile</a>
                        <a href="/logout" class="btn btn-primary">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main>
        <?php
            if (isset($page)) {
                $pagePath = VIEWS_PATH . '/' . str_replace('.', '/', $page) . '.php';
                if (file_exists($pagePath)) {
                    require $pagePath;
                } else {
                    echo "<div class='container'><p>Error: Watch content file not found.</p></div>";
                }
            }
        ?>
    </main>

    <!-- Video.js JavaScript -->
    <script src="https://vjs.zencdn.net/8.5.2/video.min.js"></script>

    <!-- Custom Player Interaction Script -->
    <script src="/public/js/player.js?v=<?php echo $assetVersion; ?>"></script>

    <!-- Script to initialize the Video.js player -->
    <script>
        if (document.getElementById('moviePlayer')) {
            const player = videojs('moviePlayer', {
                controls: true,
                autoplay: true,
                preload: 'auto',
                fluid: true // This makes the player responsive
            });
        }
    </script>
</body>
</html>