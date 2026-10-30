<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
    exit;
}

if (!isset($conn) || !$conn) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed.'
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$current_password = trim($_POST['current_password'] ?? '');
$new_password = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

if ($current_password === '' || $new_password === '' || $confirm_password === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'All password fields are required.'
    ]);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode([
        'status' => 'error',
        'message' => 'New password and confirm password do not match.'
    ]);
    exit;
}

if (strlen($new_password) < 4) {
    echo json_encode([
        'status' => 'error',
        'message' => 'New password must be at least 4 characters.'
    ]);
    exit;
}

$stmt = $conn->prepare("SELECT id, password FROM tbl_user WHERE id = ? LIMIT 1");
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Prepare failed: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Execute failed: ' . $stmt->error
    ]);
    exit;
}

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $stmt->close();
    echo json_encode([
        'status' => 'error',
        'message' => 'User account not found in tbl_user.'
    ]);
    exit;
}

$storedPassword = $user['password'];
$isCurrentPasswordValid = false;


if ($current_password === $storedPassword) {
    $isCurrentPasswordValid = true;
}


if (!$isCurrentPasswordValid && password_verify($current_password, $storedPassword)) {
    $isCurrentPasswordValid = true;
}

if (!$isCurrentPasswordValid) {
    $stmt->close();
    echo json_encode([
        'status' => 'error',
        'message' => 'Current password is incorrect.'
    ]);
    exit;
}


$update = $conn->prepare("UPDATE tbl_user SET password = ? WHERE id = ?");
if (!$update) {
    $stmt->close();
    echo json_encode([
        'status' => 'error',
        'message' => 'Prepare failed: ' . $conn->error
    ]);
    exit;
}

$update->bind_param("si", $new_password, $user_id);

if ($update->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Password updated successfully.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update password: ' . $update->error
    ]);
}

$update->close();
$stmt->close();
$conn->close();
?>