<?php
session_start();
require_once __DIR__ . '/admin_common.php';
if (!isset($_SESSION['user_id']) || strtolower((string)($_SESSION['role'] ?? '')) !== 'admin') {
    admin_json(['status' => 'error', 'message' => 'Unauthorized'], 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    admin_json(['status' => 'error', 'message' => 'Invalid request.'], 405);
}
$userId = (int)$_SESSION['user_id'];
$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone_number'] ?? '');
$address = trim($_POST['address'] ?? '');
$role = 'admin';
if (!table_exists($conn, 'tbl_profile')) {
    admin_json(['status' => 'error', 'message' => 'tbl_profile not found.'], 500);
}
$check = $conn->prepare("SELECT profile_id FROM tbl_profile WHERE profile_id = ? LIMIT 1");
$check->bind_param('i', $userId);
$check->execute();
$exists = $check->get_result()->num_rows > 0;
if ($exists) {
    $stmt = $conn->prepare("UPDATE tbl_profile SET fullname=?, email=?, phone_number=?, address=?, role=?, updated_at=NOW() WHERE profile_id=?");
    $stmt->bind_param('sssssi', $fullname, $email, $phone, $address, $role, $userId);
} else {
    $stmt = $conn->prepare("INSERT INTO tbl_profile (profile_id, fullname, email, phone_number, address, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param('isssss', $userId, $fullname, $email, $phone, $address, $role);
}
if (!$stmt->execute()) {
    admin_json(['status' => 'error', 'message' => 'Failed to save settings: ' . $stmt->error], 500);
}
admin_json(['status' => 'success', 'message' => 'Admin profile updated successfully.']);
