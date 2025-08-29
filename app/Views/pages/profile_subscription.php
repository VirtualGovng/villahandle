<div class="page-hero">
    <div class="container">
        <h1>My Subscription</h1>
    </div>
</div>

<div class="container content-container">
    <div class="profile-wrapper">
        <!-- The same profile sidebar for consistent navigation -->
        <div class="profile-sidebar">
             <div class="profile-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <h3><?php echo htmlspecialchars(\App\Services\AuthService::user()['username']); ?></h3>
            <p><?php echo htmlspecialchars(\App\Services\AuthService::user()['email']); ?></p>
            <nav class="profile-nav">
                <ul>
                    <li><a href="/profile">Profile Details</a></li>
                    <li><a href="#">My Watchlist</a></li>
                    <li><a href="/profile/subscription" class="active">Subscription</a></li>
                    <li><a href="/logout">Logout</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="profile-content">
            <?php if ($subscription): ?>
                <h2>Current Plan Details</h2>
                <div class="info-grid subscription-grid">
                    <div class="info-item">
                        <span class="info-label">Current Plan</span>
                        <span class="info-value plan-name"><?php echo htmlspecialchars($subscription['plan_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value status-active"><?php echo htmlspecialchars(ucfirst($subscription['status'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Start Date</span>
                        <span class="info-value"><?php echo date('F j, Y', strtotime($subscription['start_date'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Renews / Expires On</span>
                        <span class="info-value"><?php echo date('F j, Y', strtotime($subscription['end_date'])); ?></span>
                    </div>
                </div>
                <p class="subscription-text"><?php echo htmlspecialchars($subscription['plan_description']); ?></p>
                <a href="/subscribe" class="btn btn-secondary">Change Plan</a>
            <?php else: ?>
                <h2>No Active Subscription</h2>
                <div class="no-results-box">
                    <p>You do not have an active subscription. Subscribe now to unlock exclusive content and features.</p>
                    <a href="/subscribe" class="btn btn-primary btn-lg">View Subscription Plans</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>