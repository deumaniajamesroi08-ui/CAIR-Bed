<?php
session_start();
require_once __DIR__ . '/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method.";
    exit;
}

$username = trim($_POST['username'] ?? '');
$role = strtolower(trim($_POST['role'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$confirm_password = (string)($_POST['confirm_password'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($username === '' || $role === '' || $password === '' || $confirm_password === '') {
    echo "All required fields are required.";
    exit;
}

if ($password !== $confirm_password) {
    echo "Passwords do not match.";
    exit;
}

if (strlen($password) < 6) {
    echo "Password must be at least 6 characters.";
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Please enter a valid email address.";
    exit;
}

$stmt = $conn->prepare("SELECT id FROM tbl_user WHERE LOWER(username) = LOWER(?) LIMIT 1");
if (!$stmt) {
    echo "Database error: " . $conn->error;
    exit;
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $stmt->close();
    echo "Username already taken.";
    exit;
}
$stmt->close();

$password_hash = password_hash($password, PASSWORD_DEFAULT);

/*
 * Support both versions of the Cairbed database:
 * - databases where tbl_user has an email column
 * - databases where email is stored in tbl_profile
 */
$hasUserEmail = false;
$check = $conn->query("SHOW COLUMNS FROM tbl_user LIKE 'email'");
if ($check && $check->num_rows > 0) {
    $hasUserEmail = true;
}
if ($check) $check->free();

if ($hasUserEmail) {
    $stmt = $conn->prepare("INSERT INTO tbl_user (username, role, password, email) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "Database error: " . $conn->error;
        exit;
    }
    $stmt->bind_param("ssss", $username, $role, $password_hash, $email);
} else {
    $stmt = $conn->prepare("INSERT INTO tbl_user (username, role, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo "Database error: " . $conn->error;
        exit;
    }
    $stmt->bind_param("sss", $username, $role, $password_hash);
}

if (!$stmt->execute()) {
    echo "Error creating account: " . $stmt->error;
    $stmt->close();
    exit;
}

$userId = (int)$conn->insert_id;
$stmt->close();

/* Save the email in tbl_profile when that table has the expected columns. */
if (!$hasUserEmail && $email !== '') {
    $profileCheck = $conn->query("SHOW COLUMNS FROM tbl_profile LIKE 'email'");
    if ($profileCheck && $profileCheck->num_rows > 0) {
        $profileCheck->free();

        $profileIdColumn = false;
        $pc = $conn->query("SHOW COLUMNS FROM tbl_profile LIKE 'profile_id'");
        if ($pc && $pc->num_rows > 0) $profileIdColumn = true;
        if ($pc) $pc->free();

        if ($profileIdColumn) {
            $fullname = $username;
            $address = '';
            $profileStmt = $conn->prepare(
                "INSERT INTO tbl_profile (profile_id, fullname, address, email, role)
                 VALUES (?, ?, ?, ?, ?)"
            );
            if ($profileStmt) {
                $profileStmt->bind_param("issss", $userId, $fullname, $address, $email, $role);
                $profileStmt->execute();
                $profileStmt->close();
            }
        }
    } elseif ($profileCheck) {
        $profileCheck->free();
    }
}

$conn->close();
echo "Account created successfully. <a href='index.php'>Go to Login</a>";
?>
