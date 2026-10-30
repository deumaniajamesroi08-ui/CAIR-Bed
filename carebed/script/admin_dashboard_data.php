<?php
session_start();
require_once __DIR__ . '/admin_common.php';

if (!isset($_SESSION['user_id']) || strtolower((string)($_SESSION['role'] ?? '')) !== 'admin') {
    admin_json(['status' => 'error', 'message' => 'Unauthorized'], 403);
}

$totalPatients = fetch_scalar($conn, "SELECT COUNT(*) FROM tbl_profile WHERE LOWER(COALESCE(role, '')) != 'admin'");
$totalUsers = fetch_scalar($conn, "SELECT COUNT(*) FROM tbl_user");
$totalAlerts = fetch_scalar($conn, "SELECT COUNT(*) FROM tbl_alert");
$activeAlerts = fetch_scalar($conn, "SELECT COUNT(*) FROM tbl_alert WHERE LOWER(status) IN ('new','pending','unread','active')");

$patients = [];
$criticalPatients = 0;

$sql = "
    SELECT 
        p.profile_id,
        p.fullname,
        p.age,
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
    WHERE LOWER(COALESCE(p.role, '')) != 'admin'
    ORDER BY p.profile_id DESC
    LIMIT 10
";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $status = patient_status_from_row($row);
        if ($status['status'] === 'CRITICAL') $criticalPatients++;

        $patients[] = [
            'patient_name' => !empty($row['fullname']) ? $row['fullname'] : 'Unknown Patient',
            'bed_id' => 'BED-' . str_pad($row['profile_id'], 3, '0', STR_PAD_LEFT),
            'heart_rate' => $row['heart_rate'] ?? '--',
            'temperature' => $row['temperature'] ?? '--',
            'wetness' => $row['wetness'] ?? '--',
            'presence_detection' => $row['presence_detection'] ?? '--',
            'patient_status' => $status['status'],
            'patient_status_class' => $status['class'],
            'latest_time' => $row['latest_time'] ?? 'No data'
        ];
    }
}

$recentAlerts = [];
$alertResult = $conn->query("SELECT patient_name, alert_type, status, created_at FROM tbl_alert ORDER BY created_at DESC LIMIT 5");
if ($alertResult) {
    while ($row = $alertResult->fetch_assoc()) $recentAlerts[] = $row;
}

admin_json([
    'status' => 'success',
    'summary' => [
        'total_patients' => $totalPatients,
        'total_users' => $totalUsers,
        'active_alerts' => $activeAlerts,
        'critical_patients' => $criticalPatients
    ],
    'patients' => $patients,
    'alerts' => $recentAlerts
]);
?>
