<?php
session_start();
include 'script/db_connection.php';

header('Content-Type: application/json');


if (!isset($_SESSION['user_id'], $_SESSION['user'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to change your password.'
    ]);
    exit;
}

$username = $_SESSION['user'];
$userId = (int) $_SESSION['user_id'];


$current_password = trim($_POST['current_password'] ?? '');
$new_password = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
    exit;
}

if ($current_password === '' || $new_password === '' || $confirm_password === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required.'
    ]);
    exit;
}


if ($new_password !== $confirm_password) {
    echo json_encode([
        'status' => 'error',
        'message' => 'New passwords do not match.'
    ]);
    exit;
}


$stmt = $conn->prepare("SELECT password FROM tbl_user WHERE id = ? AND username = ? LIMIT 1");
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Prepare failed: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("is", $userId, $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;

if (!$user) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found.'
    ]);
    $stmt->close();
    exit;
}
$stmt->close();


$storedPassword = $user['password'];
$isCurrentPasswordValid = false;

if ($current_password === $storedPassword) {
    $isCurrentPasswordValid = true;
}

if (!$isCurrentPasswordValid && password_verify($current_password, $storedPassword)) {
    $isCurrentPasswordValid = true;
}

if (!$isCurrentPasswordValid) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Current password is incorrect.'
    ]);
    exit;
}


$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);


$stmt = $conn->prepare("UPDATE tbl_user SET password = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Prepare failed: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("si", $new_password_hash, $userId);
if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Password updated successfully.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update password. Please try again.'
    ]);
}

$stmt->close();
$conn->close();
?>
