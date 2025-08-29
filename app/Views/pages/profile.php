<div class="page-hero">
    <div class="container">
        <h1>My Profile</h1>
    </div>
</div>

<div class="container content-container">
    <div class="profile-wrapper">
        <div class="profile-sidebar">
            <div class="profile-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <h3><?php echo htmlspecialchars($user['username']); ?></h3>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <nav class="profile-nav">
                <ul>
                    <li><a href="/profile" class="active">Profile Details</a></li>
                    
                    <?php // --- THIS IS THE FIX: Role-Specific Navigation --- ?>
                    <?php if (\App\Services\AuthService::hasRole('creator')): ?>
                        <li><a href="/creator/dashboard">Creator Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="#">My Watchlist (Coming Soon)</a></li>
                    <?php endif; ?>
                    <?php // --- END OF FIX --- ?>

                    <li><a href="/profile/subscription">Subscription</a></li>
                    <li><a href="/logout">Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="profile-content">
            <h2>Account Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Username</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email Address</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Member Since</span>
                    <span class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Account Status</span>
                    <span class="info-value status-active"><?php echo htmlspecialchars(ucfirst($user['status'])); ?></span>
                </div>
            </div>
            <a href="#" class="btn btn-secondary">Edit Profile (Coming Soon)</a>

            <div class="badges-section">
                <h3>My Badges</h3>
                <?php if (!empty($badges)): ?>
                    <div class="badges-grid">
                        <?php foreach ($badges as $badge): ?>
                            <div class="badge-item" title="<?php echo htmlspecialchars($badge['description']); ?>">
                                <span class="badge-icon <?php echo htmlspecialchars($badge['color_class']); ?>"><?php echo htmlspecialchars($badge['icon_class']); ?></span>
                                <span class="badge-name"><?php echo htmlspecialchars($badge['name']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-badges-text">You haven't earned any badges yet. Start by reviewing a movie!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>