    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cairbed System</title>
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
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">G</div>
                    <h1 class="login-title">Create Account</h1>
                    <p class="login-subtitle">Start your journey with GlassDash</p>
                </div>

                
                <div id="registerMessage" style="text-align:center;margin-bottom:10px;"></div>

                <form id="registerForm" method="POST">
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-input" placeholder="Enter your username" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="role">Role</label>
                        <select id="role" name="role" class="form-input" required>
                            <option value="" disabled selected hidden>Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="caregiver">Caregiver / Nurse</option>
                            <option value="patient">Patient / Resident</option>
                            <option value="doctor">Doctor / Physician</option>
                        </select>
                        <style>
                        </style>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Gmail / Email</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="Enter your Gmail (used for password reset)">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Create a password" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Confirm your password" required>
                    </div>

                    <div class="form-row" style="margin-bottom: 24px;">
                        <label class="checkbox-label">
                            <input type="checkbox" required>
                            I agree to the <a href="#" style="color: var(--emerald-light);">Terms & Conditions</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Create Account
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </form>

                <div class="divider"><span>or sign up with</span></div>

                <p class="login-footer">
                    Already have an account? <a href="index.php">Sign In</a>
                </p>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        <p>Copyright © 2026 Your Company. Designed by <a href="https://templatemo.com" target="_blank" rel="nofollow">TemplateMo</a></p>
    </footer>

    <script>
    $(document).ready(function() {
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: 'script/register_backend.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#registerMessage').html(response);
                    if(response.toLowerCase().includes("account created")) {
                        $('#registerMessage').css('color','green');
                        setTimeout(function(){
                            window.location.href = "index.php"; 
                        }, 2000);
                    } else {
                        $('#registerMessage').css('color','red');
                    }
                },
                error: function(xhr, status, error) {
                    var msg = (xhr.responseText || '').trim() || 'Server error. Please check that Apache and MySQL are running.'; $('#registerMessage').html(msg).css('color','red');
                }
            });
        });
    });
    </script>
    <script src="templatemo-glass-admin-script.js"></script>
    </body>
    </html>