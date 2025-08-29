<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'VillaStudio Admin'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/admin.css">
    <link rel="icon" href="/public/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="admin-wrapper">
        <?php include VIEWS_PATH . '/admin/partials/sidebar.php'; ?>
        <div class="main-content">
            <?php 
                // Pass the user variable to the navbar partial
                include VIEWS_PATH . '/admin/partials/navbar.php'; 
            ?>
            <main class="content-body">
                 <?php
                    if (isset($page)) {
                        $pagePath = VIEWS_PATH . '/' . str_replace('.', '/', $page) . '.php';
                        if (file_exists($pagePath)) {
                            require $pagePath;
                        } else {
                            echo "<div><p>Error: Admin content file not found.</p></div>";
                        }
                    }
                ?>
            </main>
        </div>
    </div>
    <script src="/public/js/admin.js"></script>
</body>
</html>