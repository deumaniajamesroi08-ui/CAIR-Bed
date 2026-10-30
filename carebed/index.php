    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cairbed System</title>
        <meta name="description" content="3D Glassmorphism Dashboard Template by TemplateMo">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="templatemo-glass-admin-style.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
    </head>
    <body>
        
        <div class="background"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="login-page">
            
            <button class="theme-toggle-float" id="theme-toggle" title="Toggle Light/Dark Mode">
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/>
                    <path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/>
                    <path d="M2 12h2"/><path d="M20 12h2"/>
                    <path d="M6.34 17.66l-1.41 1.41"/><path d="M19.07 4.93l-1.41 1.41"/>
                </svg>
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>

            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <div class="login-logo">G</div>
                        <h1 class="login-title">Welcome Back</h1>
                        <p class="login-subtitle">Sign in to continue to CairbedDash</p>
                    </div>

                <form id="loginForm">

                        <div class="form-group">
                            <label class="form-label" for="email">Username</label>
                            <input type="text" id="username" class="form-input" placeholder="Enter your Username" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" id="password" class="form-input" placeholder="Enter your password" required>
                        </div>

                        <div class="form-row">
                            <label class="checkbox-label">
                                <input type="checkbox" checked>
                                Remember me
                            </label>
                           <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Sign In
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </button>
                    </form>

                 

                    <p class="login-footer">
                        Don't have an account? <a href="register.php">Create Account</a>
                    </p>
                </div>
            </div>

            
            <footer class="site-footer">
                <p>© 2026 CairBed Systems – Patient Monitoring Dashboard</p>
            </footer>
        </div>

        <script src="templatemo-glass-admin-script.js"></script>
        
    </body>
    <script>
$(document).ready(function() {
    $('#loginForm').submit(function(e) {
        e.preventDefault();

        var username = $('#username').val().trim();
        var password = $('#password').val().trim();

        if (username === '' || password === '') {
            alert('Please enter username and password.');
            return;
        }

        $.ajax({
            url: 'script/login.php',
            type: 'POST',
            data: {
                username: username,
                password: password
            },
            success: function(response) {
                response = response.trim();
                console.log('LOGIN RESPONSE:', response);

                if (response === 'admin') {
                    window.location.href = 'admin/admin.php';
                } else if (response === 'success') {
                    window.location.href = 'dashboard.php';
                } else {
                    alert('Invalid login credentials!');
                }
            },
            error: function(xhr) {
                console.log('LOGIN ERROR:', xhr.responseText);
                alert('Server error. Please try again.');
            }
        });
    });
});
</script>
    </html>