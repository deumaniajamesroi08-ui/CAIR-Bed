<?php
session_start();
require_once __DIR__ . '/admin_common.php';
if (!isset($_SESSION['user_id']) || strtolower((string)($_SESSION['role'] ?? '')) !== 'admin') {
    admin_json(['status' => 'error', 'message' => 'Unauthorized'], 403);
}
if (!table_exists($conn, 'tbl_user')) {
    admin_json(['status' => 'error', 'message' => 'tbl_user not found.'], 500);
}
$search = trim($_GET['search'] ?? '');
$where = '';
if ($search !== '') {
    $safe = $conn->real_escape_string($search);
    $where = " WHERE u.username LIKE '%{$safe}%' OR p.fullname LIKE '%{$safe}%' OR p.email LIKE '%{$safe}%' OR u.role LIKE '%{$safe}%' ";
}
$sql = "
    SELECT u.id, u.username, u.role, u.password,
           COALESCE(p.fullname, '') AS fullname,
           COALESCE(p.email, '') AS email,
           COALESCE(p.phone_number, '') AS phone_number,
           COALESCE(p.address, '') AS address,
           COALESCE(p.age, '') AS age,
           COALESCE(p.date_of_birth, '') AS date_of_birth,
           COALESCE(p.created_at, '') AS created_at
    FROM tbl_user u
    LEFT JOIN tbl_profile p ON p.profile_id = u.id
    {$where}
    ORDER BY u.id DESC
";
$res = $conn->query($sql);
$rows = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $row['status_label'] = 'Active';
        $rows[] = $row;
    }
}
admin_json(['status' => 'success', 'rows' => $rows]);
