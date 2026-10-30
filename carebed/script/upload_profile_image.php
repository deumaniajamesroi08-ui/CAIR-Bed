<?php
session_start();
include __DIR__ . '/db_connection.php';

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

if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No image uploaded'
    ]);
    exit;
}

$fileTmp  = $_FILES['profile_image']['tmp_name'];
$fileName = $_FILES['profile_image']['name'];
$fileSize = $_FILES['profile_image']['size'];

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
$fileType = mime_content_type($fileTmp);

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only JPG, JPEG, PNG, and WEBP files are allowed'
    ]);
    exit;
}

if ($fileSize > 5 * 1024 * 1024) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Image must be 5MB or below'
    ]);
    exit;
}

$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$newFileName = 'profile_' . $user_id . '_' . time() . '.' . $extension;

$uploadDir = dirname(__DIR__) . '/uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$uploadPath = $uploadDir . $newFileName;
$dbPath = 'uploads/' . $newFileName;


$oldImagePath = '';

$checkOld = $conn->prepare("
    SELECT profile_image 
    FROM tbl_profile 
    WHERE user_id = ? 
    LIMIT 1
");

$checkOld->bind_param("i", $user_id);
$checkOld->execute();

$oldResult = $checkOld->get_result();

if ($oldRow = $oldResult->fetch_assoc()) {
    $oldImagePath = !empty($oldRow['profile_image'])
        ? trim($oldRow['profile_image'])
        : '';
}

$checkOld->close();

if (!move_uploaded_file($fileTmp, $uploadPath)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to upload image'
    ]);
    exit;
}

$check = $conn->prepare("
    SELECT user_id 
    FROM tbl_profile 
    WHERE user_id = ? 
    LIMIT 1
");

$check->bind_param("i", $user_id);
$check->execute();

$res = $check->get_result();

if ($res->num_rows > 0) {

    $stmt = $conn->prepare("
        UPDATE tbl_profile
        SET profile_image = ?, updated_at = NOW()
        WHERE user_id = ?
    ");

    $stmt->bind_param("si", $dbPath, $user_id);

} else {

    $fullname = '';
    $address = '';
    $email = '';
    $phone_number = '';
    $date_of_birth = '';
    $age = null;
    $role = '';

    $stmt = $conn->prepare("
        INSERT INTO tbl_profile 
        (user_id, fullname, address, email, phone_number, date_of_birth, age, role, profile_image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssssiss",
        $user_id,
        $fullname,
        $address,
        $email,
        $phone_number,
        $date_of_birth,
        $age,
        $role,
        $dbPath
    );
}

if ($stmt->execute()) {

    if (!empty($oldImagePath) && $oldImagePath !== $dbPath) {

        $oldFullPath = dirname(__DIR__) . '/' . $oldImagePath;

        if (file_exists($oldFullPath)) {
            @unlink($oldFullPath);
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Profile image uploaded successfully',
        'image_path' => $dbPath
    ]);

} else {

    echo json_encode([
        'status' => 'error',
        'message' => 'Database update failed: ' . $stmt->error
    ]);
}

$stmt->close();
$check->close();
$conn->close();
?>