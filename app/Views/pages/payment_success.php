<div class="page-hero">
    <div class="container">
        <h1>Payment Successful!</h1>
    </div>
</div>

<div class="container content-container">
    <div class="status-page-wrapper">
        <svg class="status-icon success" xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <h2>Thank You For Your Purchase</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <p><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
        <?php else: ?>
            <p>Your subscription is now active. You can now enjoy all the benefits of your new plan.</p>
        <?php endif; ?>
        <a href="/profile" class="btn btn-primary">Go to My Profile</a>
    </div>
</div>