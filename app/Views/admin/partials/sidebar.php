<?php
// Determine the current page's main segment to set the active class
$currentUri = $_SERVER['REQUEST_URI'];
$activeLink = 'dashboard'; // Default active link

if (strpos($currentUri, '/admin/movies') === 0) {
    $activeLink = 'movies';
} elseif (strpos($currentUri, '/admin/series') === 0) { // Added this check
    $activeLink = 'series';
} elseif (strpos($currentUri, '/admin/users') === 0) {
    $activeLink = 'users';
} elseif (strpos($currentUri, '/admin/subscriptions') === 0) {
    $activeLink = 'subscriptions';
} elseif (strpos($currentUri, '/admin/settings') === 0) {
    $activeLink = 'settings';
}
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/admin" class="logo">
            <img src="/public/images/logo.png" alt="Logo">
            <h2>VillaStudio</h2>
        </a>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="/admin/dashboard" class="<?php echo ($activeLink === 'dashboard') ? 'active' : ''; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path></svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/admin/movies" class="<?php echo ($activeLink === 'movies') ? 'active' : ''; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"></path></svg>
                    <span>Movies</span>
                </a>
            </li>
            <li>
                <a href="/admin/series" class="<?php echo ($activeLink === 'series') ? 'active' : ''; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"></path></svg>
                    <span>Series</span>
                </a>
            </li>
            <li>
                <a href="/admin/users" class="<?php echo ($activeLink === 'users') ? 'active' : ''; ?>">
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path></svg>
                    <span>Users</span>
                </a>
            </li>
             <li>
                <a href="/admin/subscriptions" class="<?php echo ($activeLink === 'subscriptions') ? 'active' : ''; ?>">
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"></path></svg>
                    <span>Subscriptions</span>
                </a>
            </li>
            <li>
                <a href="/admin/settings" class="<?php echo ($activeLink === 'settings') ? 'active' : ''; ?>">
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69-.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"></path></svg>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="/" target="_blank">View Live Site</a>
    </div>
</aside>