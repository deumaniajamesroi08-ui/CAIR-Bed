<?php
session_start();
include 'db_connection.php';

header('Content-Type: text/plain');

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    echo "error";
    exit;
}

$stmt = $conn->prepare("SELECT id, username, password, role FROM tbl_user WHERE username = ? LIMIT 1");

if (!$stmt) {
    echo "error";
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();

    $storedPassword = $row['password'];
    $role = $row['role'] ?? 'user';
    $userId = (int) $row['id'];

    $isValid = false;
    $needsRehash = false;

    
    if ($password === $storedPassword) {
        $isValid = true;
        $needsRehash = true;
    }

    
    if (!$isValid && password_verify($password, $storedPassword)) {
        $isValid = true;
        $needsRehash = password_needs_rehash($storedPassword, PASSWORD_DEFAULT);
    }

    if ($isValid) {
        if ($needsRehash) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE tbl_user SET password = ? WHERE id = ?");

            if ($updateStmt) {
                $updateStmt->bind_param("si", $newHash, $userId);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user'] = $row['username'];
        $_SESSION['role'] = $role;
        

        if ($role === 'admin') {
            echo "admin";
        } else {
            echo "success";
        }
    } else {
        echo "error";
    }
} else {
    echo "error";
}

$stmt->close();
$conn->close();
?>
