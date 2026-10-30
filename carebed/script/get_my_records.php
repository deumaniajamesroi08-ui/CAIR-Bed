<?php
session_start();
include 'db_connection.php';
include_once 'carebed_logged_profile.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$profile = carebed_logged_profile($conn);
$profileId = (int)$profile['profile_id'];

function tableExists($conn, $table) {
    $table = $conn->real_escape_string($table);
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

function columnExists($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

if (!tableExists($conn, 'tbl_records')) {
    echo json_encode([]);
    exit;
}

$timeColumn = columnExists($conn, 'tbl_records', 'created_at') ? 'created_at' : (columnExists($conn, 'tbl_records', 'timestamp') ? 'timestamp' : null);
$profileColumn = columnExists($conn, 'tbl_records', 'profile_id') ? 'profile_id' : (columnExists($conn, 'tbl_records', 'patient_id') ? 'patient_id' : null);
$nameColumn = columnExists($conn, 'tbl_records', 'patient_name') ? 'patient_name' : null;

$select = [];
$select[] = $timeColumn ? "r.`$timeColumn` AS record_time" : "NULL AS record_time";
$select[] = $nameColumn ? "r.`$nameColumn` AS record_patient_name" : "NULL AS record_patient_name";
$select[] = columnExists($conn, 'tbl_records', 'heart_rate') ? 'r.heart_rate' : 'NULL AS heart_rate';
$select[] = columnExists($conn, 'tbl_records', 'temperature') ? 'r.temperature' : 'NULL AS temperature';
$select[] = columnExists($conn, 'tbl_records', 'wetness') ? 'r.wetness' : 'NULL AS wetness';
$select[] = columnExists($conn, 'tbl_records', 'presence_detection') ? 'r.presence_detection' : 'NULL AS presence_detection';

$where = $profileColumn ? "WHERE r.`$profileColumn` = ?" : '';
$order = $timeColumn ? "ORDER BY r.`$timeColumn` DESC" : 'ORDER BY 1 DESC';
$sql = 'SELECT ' . implode(', ', $select) . " FROM tbl_records r $where $order LIMIT 100";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([]);
    exit;
}

if ($profileColumn) {
    $stmt->bind_param('i', $profileId);
}

$stmt->execute();
$result = $stmt->get_result();
$records = [];

while ($row = $result->fetch_assoc()) {
    $time = $row['record_time'] ?? '';
    $records[] = [
        'timestamp' => $time ? date('M j, Y h:i A', strtotime($time)) : 'No date',
        'patient_name' => $row['record_patient_name'] ?: $profile['fullname'],
        'heart_rate' => is_numeric($row['heart_rate']) ? (int)$row['heart_rate'] : 0,
        'temperature' => is_numeric($row['temperature']) ? number_format((float)$row['temperature'], 2) : '0.00',
        'wetness' => $row['wetness'] ?: 'Dry',
        'presence_detection' => $row['presence_detection'] ?: 'Not Detected'
    ];
}

$stmt->close();
echo json_encode($records);
$conn->close();
?>
