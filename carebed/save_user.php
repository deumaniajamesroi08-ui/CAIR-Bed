<?php
include __DIR__ . '/db_connection.php';

$username = $_POST['username'];
$password = $_POST['password'];


$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


$check = $conn->prepare("SELECT id FROM tbl_user WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Username already exists";
    exit;
}


$stmt = $conn->prepare("INSERT INTO tbl_user (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashedPassword);

if ($stmt->execute()) {
    echo "User added successfully";
} else {
    echo "Error adding user";
}
?>