<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/db_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

header('Content-Type: text/plain');

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($username === '' || $email === '') {
    echo 'error';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/i', $email)) {
    echo 'invalid_email';
    exit;
}

/* Find the user without assuming tbl_user has an email column. */
$stmt = $conn->prepare("SELECT id, username FROM tbl_user WHERE username = ? LIMIT 1");
if (!$stmt) {
    echo 'prepare_select_failed: ' . $conn->error;
    exit;
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    echo 'not_found';
    exit;
}

$user = $result->fetch_assoc();
$user_id = (int)$user['id'];
$db_username = $user['username'];
$stmt->close();

$db_email = '';

/* First check tbl_user.email if that column exists. */
$check = $conn->query("SHOW COLUMNS FROM tbl_user LIKE 'email'");
if ($check && $check->num_rows > 0) {
    $check->free();
    $emailStmt = $conn->prepare("SELECT email FROM tbl_user WHERE id = ? LIMIT 1");
    if ($emailStmt) {
        $emailStmt->bind_param("i", $user_id);
        $emailStmt->execute();
        $emailResult = $emailStmt->get_result();
        if ($emailResult && $emailResult->num_rows === 1) {
            $db_email = trim((string)$emailResult->fetch_assoc()['email']);
        }
        $emailStmt->close();
    }
} elseif ($check) {
    $check->free();
}

/* Otherwise check tbl_profile.email. */
if ($db_email === '') {
    $checkProfile = $conn->query("SHOW COLUMNS FROM tbl_profile LIKE 'email'");
    if ($checkProfile && $checkProfile->num_rows > 0) {
        $checkProfile->free();
        $profileStmt = $conn->prepare("SELECT email FROM tbl_profile WHERE profile_id = ? LIMIT 1");
        if ($profileStmt) {
            $profileStmt->bind_param("i", $user_id);
            $profileStmt->execute();
            $profileResult = $profileStmt->get_result();
            if ($profileResult && $profileResult->num_rows === 1) {
                $db_email = trim((string)$profileResult->fetch_assoc()['email']);
            }
            $profileStmt->close();
        }
    } elseif ($checkProfile) {
        $checkProfile->free();
    }
}

if ($db_email === '') {
    /* Save the first supplied Gmail where the schema permits it. */
    $check = $conn->query("SHOW COLUMNS FROM tbl_user LIKE 'email'");
    if ($check && $check->num_rows > 0) {
        $check->free();
        $saveStmt = $conn->prepare("UPDATE tbl_user SET email = ? WHERE id = ?");
        if (!$saveStmt) {
            echo 'prepare_save_email_failed: ' . $conn->error;
            $conn->close();
            exit;
        }
        $saveStmt->bind_param("si", $email, $user_id);
        if (!$saveStmt->execute()) {
            echo 'save_email_failed: ' . $saveStmt->error;
            $saveStmt->close();
            $conn->close();
            exit;
        }
        $saveStmt->close();
        $db_email = $email;
    } elseif ($check) {
        $check->free();
        $profileStmt = $conn->prepare(
            "UPDATE tbl_profile SET email = ? WHERE profile_id = ?"
        );
        if ($profileStmt) {
            $profileStmt->bind_param("si", $email, $user_id);
            $profileStmt->execute();
            $profileStmt->close();
            $db_email = $email;
        } else {
            echo 'No email field is available for this account.';
            $conn->close();
            exit;
        }
    }
} elseif (strcasecmp($db_email, $email) !== 0) {
    echo 'email_mismatch';
    $conn->close();
    exit;
}

$deleteStmt = $conn->prepare(
    "DELETE FROM password_resets WHERE user_id = ? OR expires_at < NOW() OR used = 1"
);
if ($deleteStmt) {
    $deleteStmt->bind_param("i", $user_id);
    $deleteStmt->execute();
    $deleteStmt->close();
}

$token = bin2hex(random_bytes(32));
$expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

$insertStmt = $conn->prepare("
    INSERT INTO password_resets (user_id, email, token, expires_at, used)
    VALUES (?, ?, ?, ?, 0)
");

if (!$insertStmt) {
    echo 'prepare_insert_failed: ' . $conn->error;
    $conn->close();
    exit;
}

$insertStmt->bind_param("isss", $user_id, $db_email, $token, $expires_at);

if (!$insertStmt->execute()) {
    echo 'insert_token_failed: ' . $insertStmt->error;
    $insertStmt->close();
    $conn->close();
    exit;
}
$insertStmt->close();

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? ''))), '/');
$reset_link = $scheme . '://' . $host . $basePath . '/reset_password.php?token=' . urlencode($token);

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jamjamdeumania24@gmail.com';
    $mail->Password = 'wbmhyesjwzfejwlm';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('jamjamdeumania24@gmail.com', 'Cairbed System');
    $mail->addAddress($db_email, $db_username);
    $mail->isHTML(true);
    $mail->Subject = 'Reset Your Cairbed Password';
    $mail->Body = "
        <div style='font-family:Arial,sans-serif;line-height:1.6;'>
            <h2>Password Reset Request</h2>
            <p>Hello <b>" . htmlspecialchars($db_username, ENT_QUOTES, 'UTF-8') . "</b>,</p>
            <p>Click the link below to reset your password:</p>
            <p><a href='{$reset_link}'>{$reset_link}</a></p>
            <p>This link will expire in 1 hour.</p>
        </div>
    ";
    $mail->AltBody = "Reset your password using this link: " . $reset_link;
    $mail->send();

    echo 'success';
} catch (Exception $e) {
    echo 'mailer_error: ' . $mail->ErrorInfo;
}

$conn->close();
?>
