<?php 
use App\Services\AuthService; 
// Increment version number to ensure this new HTML is not cached.
$assetVersion = '1.7'; 
// Get the current URI for active link highlighting
$currentUri = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'VillaStudio'); ?></title>
    <meta name="description" content="VillaStudio is your legal and engaging hub for discovering independent films, series, and classic movies.">
    
    <meta name="referrer" content="no-referrer-when-downgrade">
    <meta name="theme-color" content="#00aaff"/>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/public/css/style.css?v=<?php echo $assetVersion; ?>">
    
    <link rel="manifest" href="/public/manifest.json">
    <link rel="icon" href="/public/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="/public/images/icons/icon-192x192.png">
</head>
<body>

    <header class="main-header">
        <div class="container">
            <a href="/" class="logo">
                <img src="/public/images/logo.png" alt="VillaStudio Logo">
                <h1>VillaStudio</h1>
            </a>
            <nav class="main-nav">
                <ul>
                    <li><a href="/" class="<?php echo ($currentUri === '/') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="/movies" class="<?php echo (strpos($currentUri, '/movies') === 0) ? 'active' : ''; ?>">Movies</a></li>
                    <li><a href="/series" class="<?php echo (strpos($currentUri, '/series') === 0) ? 'active' : ''; ?>">Series</a></li>
                    <li><a href="/community" class="<?php echo (strpos($currentUri, '/community') === 0) ? 'active' : ''; ?>">Community</a></li>
                </ul>
                <div class="mobile-nav-actions">
                    <?php if (AuthService::check()): ?>
                        <a href="/profile" class="btn btn-secondary">My Profile</a>
                        <a href="/logout" class="btn btn-primary">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary">Sign In</a>
                    <?php endif; ?>
                </div>
            </nav>
            <div class="header-actions">
                <button class="search-btn" aria-label="Search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
                <div class="desktop-only">
                    <?php if (AuthService::check()): ?>
                        <a href="/profile" class="btn btn-secondary">Profile</a>
                        <a href="/logout" class="btn btn-primary">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary">Sign In</a>
                    <?php endif; ?>
                </div>
                <button class="mobile-nav-toggle" aria-label="Toggle Navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <?php include VIEWS_PATH . '/partials/messages.php'; ?>

    <main>
        <?php
            if (isset($page)) {
                $pagePath = VIEWS_PATH . '/' . str_replace('.', '/', $page) . '.php';
                if (file_exists($pagePath)) {
                    require $pagePath;
                } else {
                    echo "<div class='container'><p>Error: Content file not found.</p></div>";
                }
            }
        ?>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <a href="/" class="logo">
                        <img src="/public/images/logo.png" alt="VillaStudio Logo">
                        <h2>VillaStudio</h2>
                    </a>
                    <p>Your legal and engaging hub for discovering independent films, series, and classic movies.</p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/about">About Us</a></li>
                        <li><a href="/contact">Contact</a></li>
                        <li><a href="/faq">FAQ</a></li>
                        <li><a href="/creator/dashboard">Creator Portal</a></li>
                    </ul>
                </div>
                <div class="footer-legal">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="/terms">Terms of Service</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                        <li><a href="/copyright">Copyright</a></li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h4>Follow Us</h4>
                    <a href="#" aria-label="Facebook">FB</a>
                    <a href="#" aria-label="Twitter">TW</a>
                    <a href="#" aria-label="Instagram">IG</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> VillaStudio. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Search Modal -->
    <div class="search-modal">
        <div class="search-modal-content">
            <button class="search-modal-close" aria-label="Close Search">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <h3>Search for Movies or Series</h3>
            <form action="/search" method="get" class="search-modal-form">
                <input type="text" id="searchInput" name="q" placeholder="e.g., King of Boys" required>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>
    
    <script src="/public/js/app.js?v=<?php echo $assetVersion; ?>"></script>
    
    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/public/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(error => {
                        console.log('ServiceWorker registration failed: ', error);
                    });
            });
        }
    </script>
</body>
</html>