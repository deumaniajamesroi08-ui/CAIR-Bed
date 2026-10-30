<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/db_connection.php';

header('Content-Type: text/plain');

$token = trim($_POST['token'] ?? '');
$new_password = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

if ($token === '' || $new_password === '' || $confirm_password === '') {
    echo 'error';
    exit;
}

if ($new_password !== $confirm_password) {
    echo 'error';
    exit;
}

$stmt = $conn->prepare("SELECT id, user_id, expires_at, used FROM password_resets WHERE token = ? LIMIT 1");
if (!$stmt) {
    echo 'prepare_failed: ' . $conn->error;
    exit;
}

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    echo 'expired';
    exit;
}

$row = $result->fetch_assoc();
$reset_id = (int)$row['id'];
$user_id = (int)$row['user_id'];
$used = (int)$row['used'];
$expires_at = strtotime($row['expires_at']);
$stmt->close();

if ($used === 1 || $expires_at <= time()) {
    $conn->close();
    echo 'expired';
    exit;
}

$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

$updateUser = $conn->prepare("UPDATE tbl_user SET password = ? WHERE id = ?");
if (!$updateUser) {
    echo 'prepare_update_failed: ' . $conn->error;
    $conn->close();
    exit;
}

$updateUser->bind_param("si", $new_hash, $user_id);

if (!$updateUser->execute()) {
    echo 'update_failed: ' . $updateUser->error;
    $updateUser->close();
    $conn->close();
    exit;
}
$updateUser->close();

$updateReset = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
if ($updateReset) {
    $updateReset->bind_param("i", $reset_id);
    $updateReset->execute();
    $updateReset->close();
}

echo 'success';
$conn->close();
?>