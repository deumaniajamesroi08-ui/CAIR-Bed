<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
if (!isset($_SESSION['role']) || strtolower((string)$_SESSION['role']) !== 'admin') {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}
require_once __DIR__ . '/../../script/db_connection.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
