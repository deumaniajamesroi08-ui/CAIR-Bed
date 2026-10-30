<?php
session_start();
require_once __DIR__ . '/admin_common.php';

if (!isset($_SESSION['user_id']) || strtolower((string)($_SESSION['role'] ?? '')) !== 'admin') {
    admin_json(['status' => 'error', 'message' => 'Unauthorized'], 403);
}

$search = trim($_GET['search'] ?? '');
$where = "WHERE LOWER(COALESCE(p.role, '')) != 'admin'";

if ($search !== '') {
    $safe = $conn->real_escape_string($search);
    $where .= "
        AND (
            p.fullname LIKE '%{$safe}%'
            OR p.email LIKE '%{$safe}%'
            OR p.profile_id LIKE '%{$safe}%'
            OR CONCAT('BED-', LPAD(p.profile_id, 3, '0')) LIKE '%{$safe}%'
        )
    ";
}

$sql = "
    SELECT 
        p.profile_id,
        p.fullname,
        p.age,
        p.role,
        pd.heart_rate,
        pd.temperature,
        pd.wetness,
        pd.presence_detection,
        pd.timestamp AS latest_time
    FROM tbl_profile p
    LEFT JOIN (
        SELECT d1.*
        FROM tbl_patient_data d1
        INNER JOIN (
            SELECT patient_id, MAX(id) AS max_id
            FROM tbl_patient_data
            GROUP BY patient_id
        ) d2 ON d1.id = d2.max_id
    ) pd ON pd.patient_id = p.profile_id
    {$where}
    ORDER BY p.profile_id DESC
";

$res = $conn->query($sql);
$rows = [];

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $status = patient_status_from_row($row);

        $rows[] = [
            'patient_name' => !empty($row['fullname']) ? $row['fullname'] : 'Unknown Patient',
            'bed_id' => 'BED-' . str_pad($row['profile_id'], 3, '0', STR_PAD_LEFT),
            'age' => $row['age'] ?? 'N/A',
            'heart_rate' => $row['heart_rate'] ?? '--',
            'temperature' => $row['temperature'] ?? '--',
            'wetness' => $row['wetness'] ?? '--',
            'presence_detection' => $row['presence_detection'] ?? '--',
            'patient_status' => $status['status'],
            'patient_status_class' => $status['class'],
            'latest_time' => $row['latest_time'] ?? 'No data'
        ];
    }
} else {
    admin_json(['status' => 'error', 'message' => $conn->error], 500);
}

admin_json(['status' => 'success', 'rows' => $rows]);
?>
