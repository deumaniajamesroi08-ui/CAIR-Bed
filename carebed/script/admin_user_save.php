<?php
session_start();
require_once __DIR__ . '/admin_common.php';
if (!isset($_SESSION['user_id']) || strtolower((string)($_SESSION['role'] ?? '')) !== 'admin') {
    admin_json(['status' => 'error', 'message' => 'Unauthorized'], 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    admin_json(['status' => 'error', 'message' => 'Invalid request.'], 405);
}
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = trim($_POST['role'] ?? 'caregiver');
$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone_number'] ?? '');
$address = trim($_POST['address'] ?? '');
$age = trim($_POST['age'] ?? '');
$dob = trim($_POST['date_of_birth'] ?? '');

if ($username === '' || $password === '' || $role === '') {
    admin_json(['status' => 'error', 'message' => 'Username, password, and role are required.'], 422);
}
$check = $conn->prepare("SELECT id FROM tbl_user WHERE username = ? LIMIT 1");
$check->bind_param('s', $username);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    admin_json(['status' => 'error', 'message' => 'Username already exists.'], 409);
}
$insert = $conn->prepare("INSERT INTO tbl_user (username, password, role) VALUES (?, ?, ?)");
$insert->bind_param('sss', $username, $password, $role);
if (!$insert->execute()) {
    admin_json(['status' => 'error', 'message' => 'Failed to save user: ' . $insert->error], 500);
}
$userId = $insert->insert_id;
if (table_exists($conn, 'tbl_profile')) {
    $profile = $conn->prepare("INSERT INTO tbl_profile (profile_id, fullname, address, email, phone_number, date_of_birth, age, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $ageValue = $age === '' ? null : (int)$age;
    $profile->bind_param('isssssis', $userId, $fullname, $address, $email, $phone, $dob, $ageValue, $role);
    $profile->execute();
}
admin_json(['status' => 'success', 'message' => 'User created successfully.']);
