<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

if (isset($_POST['fetch'])) {
    $stmt = $conn->prepare("
        SELECT fullname, address, email, phone_number, date_of_birth, age, role, profile_image
        FROM tbl_profile
        WHERE profile_id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Prepare failed: ' . $conn->error
        ]);
        exit;
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'data' => $data ? $data : [
            'fullname' => '',
            'address' => '',
            'email' => '',
            'phone_number' => '',
            'date_of_birth' => '',
            'age' => '',
            'role' => '',
            'profile_image' => ''
        ]
    ]);
    exit;
}

$fullname      = trim($_POST['fullname'] ?? '');
$email         = trim($_POST['email'] ?? '');
$role          = trim($_POST['role'] ?? '');
$address       = trim($_POST['address'] ?? '');
$phone_number  = trim($_POST['phone_number'] ?? '');
$date_of_birth = trim($_POST['date_of_birth'] ?? '');
$age           = isset($_POST['age']) && $_POST['age'] !== '' ? (int) $_POST['age'] : null;

if ($fullname === '' || $email === '' || $role === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Full Name, Email, and Role are required'
    ]);
    exit;
}

$check = $conn->prepare("SELECT profile_id FROM tbl_profile WHERE profile_id = ? LIMIT 1");
$check->bind_param("i", $user_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    $stmt = $conn->prepare("
        UPDATE tbl_profile
        SET fullname = ?, address = ?, email = ?, phone_number = ?, date_of_birth = ?, age = ?, role = ?, updated_at = NOW()
        WHERE profile_id = ?
    ");
    $stmt->bind_param("sssssisi", $fullname, $address, $email, $phone_number, $date_of_birth, $age, $role, $user_id);
} else {
    $stmt = $conn->prepare("
        INSERT INTO tbl_profile (profile_id, fullname, address, email, phone_number, date_of_birth, age, role)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssssis", $user_id, $fullname, $address, $email, $phone_number, $date_of_birth, $age, $role);
}

if ($stmt->execute()) {
    $stmt2 = $conn->prepare("
        SELECT fullname, address, email, phone_number, date_of_birth, age, role, profile_image
        FROM tbl_profile
        WHERE profile_id = ?
        LIMIT 1
    ");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $data = $stmt2->get_result()->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'message' => 'Profile saved successfully',
        'data' => $data
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $stmt->error
    ]);
}
?>