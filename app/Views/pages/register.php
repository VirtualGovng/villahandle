<div class="auth-container">
    <div class="auth-form-wrapper">
        <div class="auth-header">
            <h2>Create Your Account</h2>
            <p>Join VillaStudio today to start streaming.</p>
        </div>
        
        <form action="/register" method="POST" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Minimum 8 characters" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="Re-enter your password" required>
            </div>
            <div class="form-terms">
                By creating an account, you agree to our <a href="/terms-of-service" class="form-link">Terms of Service</a>.
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="/login">Sign In</a></p>
        </div>
    </div>
</div>