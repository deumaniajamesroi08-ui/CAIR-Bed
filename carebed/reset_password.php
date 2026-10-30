<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/script/db_connection.php';

$token = trim($_GET['token'] ?? '');
$isValid = false;

if ($token !== '') {
    $stmt = $conn->prepare("SELECT id, user_id, expires_at, used FROM password_resets WHERE token = ? LIMIT 1");

    if ($stmt) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if ((int)$row['used'] === 0 && strtotime($row['expires_at']) > time()) {
                $isValid = true;
            }
        }

        $stmt->close();
    }
}

if (isset($conn) && $conn) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Cairbed</title>
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
                    <h1 class="login-title">Reset Password</h1>
                    <p class="login-subtitle">Type your new password below.</p>
                </div>

                <?php if ($isValid): ?>
                    <form id="resetForm" action="javascript:void(0);">
                        <input type="hidden" id="token" value="<?php echo htmlspecialchars($token); ?>">

                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" id="new_password" class="form-input" placeholder="Enter new password" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" id="confirm_password" class="form-input" placeholder="Confirm new password" required>
                        </div>

                          <button type="submit" class="btn btn-primary">Reset Password</button>
                    </form>
                <?php else: ?>
                    <p style="text-align:center; color:white;">Invalid or expired reset link.</p>
                    <p class="login-footer" style="margin-top:20px;">
                        <a href="forgot_password.php">Request another reset link</a>
                    </p>
                <?php endif; ?>

                <p class="login-footer" style="margin-top:20px;">
                    <a href="index.php">Back to Login</a>
                </p>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        $('#resetForm').on('submit', function (e) {
            e.preventDefault();

            const token = $('#token').val().trim();
            const newPassword = $('#new_password').val().trim();
            const confirmPassword = $('#confirm_password').val().trim();

            if (newPassword === '' || confirmPassword === '') {
                alert('Please fill in all fields.');
                return;
            }

            if (newPassword !== confirmPassword) {
                alert('Passwords do not match.');
                return;
            }

            $.ajax({
                url: 'script/update_reset_password.php',
                type: 'POST',
                data: {
                    token: token,
                    new_password: newPassword,
                    confirm_password: confirmPassword
                },
                success: function (response) {
                    response = response.trim();

                   if (response === 'success') {
    alert('Password changed successfully.');
    localStorage.setItem('password_reset_done', '1');
    window.location.href = 'index.php';

                    } else if (response === 'expired') {
                        alert('Reset link is invalid or expired.');
                    } else {
                        alert('Error: ' + response);
                    }
                },
                error: function (xhr) {
                    alert('Server error:\n' + xhr.responseText);
                }
            });
        });
    });
    </script>
</body>
</html>