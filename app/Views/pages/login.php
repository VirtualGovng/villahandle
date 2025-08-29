<div class="auth-container">
    <div class="auth-form-wrapper">
        <div class="auth-header">
            <h2>Welcome Back!</h2>
            <p>Sign in to continue to VillaStudio.</p>
        </div>
        
        <form action="/login" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-options">
                <div class="form-check">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>
                <a href="/forgot-password" class="form-link">Forgot Password?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>
        
        <div class="auth-footer">
            <p>Don't have an account? <a href="/register">Sign Up</a></p>
        </div>
    </div>
</div>