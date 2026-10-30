<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Cairbed</title>
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
                    <h1 class="login-title">Forgot Password</h1>
                    <p class="login-subtitle">Enter your username and Gmail to reset your password.</p>
                </div>

                <form id="forgotForm" action="javascript:void(0);">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" id="username" class="form-input" placeholder="Enter your username" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gmail</label>
                        <input type="email" id="email" class="form-input" placeholder="Enter your Gmail" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Send Reset Link</button>
                </form>

                <p class="login-footer" style="margin-top:20px;">
                    <a href="index.php">Back to Login</a>
                </p>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        $('#forgotForm').on('submit', function (e) {
            e.preventDefault();

            const username = $('#username').val().trim();
            const email = $('#email').val().trim();

            if (username === '' || email === '') {
                alert('Please enter username and Gmail.');
                return;
            }

            $.ajax({
                url: 'script/send_reset_link.php',
                type: 'POST',
                data: {
                    username: username,
                    email: email
                },
                success: function (response) {
                    response = response.trim();

                    if (response === 'success') {
                        alert('Email sent successfully.');
                        $('#forgotForm')[0].reset();
                    } else if (response === 'not_found') {
                        alert('Username not found.');
                    } else if (response === 'invalid_email') {
                        alert('Please enter a valid Gmail address.');
                    } else if (response === 'email_mismatch') {
                        alert('This Gmail does not match the saved Gmail of that account.');
                    } else {
                        alert('Error: ' + response);
                    }
                },
                error: function (xhr) {
                    alert('Server error:\n' + xhr.responseText);
                }
            });
        });

        setInterval(function () {
            if (localStorage.getItem('password_reset_done') === '1') {
                localStorage.removeItem('password_reset_done');
                window.location.href = 'index.php';
            }
        }, 1000);
    });
    </script>
</body>
</html>