<div class="page-hero">
    <div class="container">
        <h1>Payment Cancelled</h1>
    </div>
</div>

<div class="container content-container">
    <div class="status-page-wrapper">
         <svg class="status-icon cancel" xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
        <h2>Your Payment Was Not Completed</h2>
        <?php if (isset($_SESSION['error_message'])): ?>
             <p><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
        <?php else: ?>
            <p>Your transaction was either cancelled or failed. Your card has not been charged.</p>
        <?php endif; ?>
        <a href="/subscribe" class="btn btn-secondary">Try Again</a>
    </div>
</div>