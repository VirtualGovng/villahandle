<header class="main-navbar">
    <div class="navbar-left">
        <button class="menu-toggle" id="menuToggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"></path></svg>
        </button>
        <h1 class="page-title"><?php echo htmlspecialchars($title ?? 'Dashboard'); ?></h1>
    </div>
    <div class="navbar-right">
        <div class="user-menu">
            <?php if (isset($user) && is_array($user)): ?>
                <span><?php echo htmlspecialchars($user['username']); ?></span>
            <?php endif; ?>
            <a href="/logout">Logout</a>
        </div>
    </div>
</header>