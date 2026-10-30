<?php
session_start();
require_once __DIR__ . '/admin_common.php';

if (!isset($_SESSION['user_id']) || strtolower((string)($_SESSION['role'] ?? '')) !== 'admin') {
    admin_json(['status' => 'error', 'message' => 'Unauthorized'], 403);
}

$fromDate = trim($_GET['from_date'] ?? $_GET['start'] ?? '');
$toDate = trim($_GET['to_date'] ?? $_GET['end'] ?? '');

$where = "WHERE 1=1";
$params = [];
$types = '';

if ($fromDate !== '' && $toDate !== '') {
    $where .= " AND DATE(pd.timestamp) BETWEEN ? AND ?";
    $params = [$fromDate, $toDate];
    $types = 'ss';
} elseif ($fromDate !== '') {
    $where .= " AND DATE(pd.timestamp) >= ?";
    $params = [$fromDate];
    $types = 's';
} elseif ($toDate !== '') {
    $where .= " AND DATE(pd.timestamp) <= ?";
    $params = [$toDate];
    $types = 's';
}

$sql = "
    SELECT 
        COALESCE(p.fullname, 'Unknown Patient') AS patient_name,
        pd.patient_id,
        pd.heart_rate,
        pd.temperature,
        pd.wetness,
        pd.presence_detection,
        pd.timestamp
    FROM tbl_patient_data pd
    LEFT JOIN tbl_profile p ON p.profile_id = pd.patient_id
    {$where}
    ORDER BY pd.timestamp DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) admin_json(['status' => 'error', 'message' => 'Query prepare failed: ' . $conn->error], 500);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $status = patient_status_from_row($row);
    $data[] = [
        'patient_name' => $row['patient_name'],
        'bed_id' => 'BED-' . str_pad($row['patient_id'], 3, '0', STR_PAD_LEFT),
        'heart_rate' => $row['heart_rate'] ?? '--',
        'temperature' => $row['temperature'] ?? '--',
        'wetness' => $row['wetness'] ?? '--',
        'presence_detection' => $row['presence_detection'] ?? '--',
        'status' => $status['status'],
        'timestamp' => date('M d, Y h:i A', strtotime($row['timestamp']))
    ];
}

$totalAlerts = 0;
$alertTable = first_existing_table($conn, ['tbl_alert', 'tbl_alerts']);
if ($alertTable) {
    if ($fromDate !== '' && $toDate !== '' && column_exists($conn, $alertTable, 'created_at')) {
        $alertStmt = $conn->prepare("SELECT COUNT(*) AS total FROM {$alertTable} WHERE DATE(created_at) BETWEEN ? AND ?");
        if ($alertStmt) {
            $alertStmt->bind_param('ss', $fromDate, $toDate);
            $alertStmt->execute();
            $totalAlerts = (int)($alertStmt->get_result()->fetch_assoc()['total'] ?? 0);
        }
    } else {
        $totalAlerts = fetch_scalar($conn, "SELECT COUNT(*) FROM {$alertTable}");
    }
}

admin_json([
    'status' => 'success',
    'total_records' => count($data),
    'total_alerts' => $totalAlerts,
    'data' => $data
]);
?>
