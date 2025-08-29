<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon users">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path></svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Total Users</span>
            <span class="stat-value"><?php echo number_format($userCount ?? 0); ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon movies">
             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"></path></svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Published Movies</span>
            <span class="stat-value"><?php echo number_format($movieCount ?? 0); ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon subscriptions">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"></path></svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Active Subscriptions</span>
            <span class="stat-value"><?php echo number_format($subscriptionCount ?? 0); ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon revenue">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c2.16-.43 3.5-1.66 3.5-3.6 0-2.31-1.91-3.46-4.7-4.14z"></path></svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Total Revenue</span>
            <span class="stat-value">$<?php echo number_format($totalRevenue ?? 0.00, 2); ?></span>
        </div>
    </div>
</div>

<div class="recent-activity-panel">
    <h3>Recent Activity</h3>
    <?php if (!empty($recentActivity)): ?>
        <ul class="activity-list">
            <?php foreach($recentActivity as $activity): ?>
                <li>
                    <span class="activity-desc">
                        <?php if ($activity['username']): ?>
                            <strong><?php echo htmlspecialchars($activity['username']); ?>:</strong>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($activity['description']); ?>
                    </span>
                    <span class="activity-time"><?php echo date('M j, g:i a', strtotime($activity['created_at'])); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No recent activity to display.</p>
    <?php endif; ?>
</div>