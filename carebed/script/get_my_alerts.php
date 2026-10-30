<?php
session_start();
include 'db_connection.php';
include_once 'carebed_logged_profile.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$profile = carebed_logged_profile($conn);
$profile_id = (int)$profile['profile_id'];

function getSeverity($alertType) {
    $critical = ['Abnormal Heart Rate','Low Heart Rate','High Temperature','No Presence Detected','No Movement Detected','Patient Not Detected'];
    $warning = ['Wetness Detected','Low Movement','Poor Sleep Quality','Temperature Warning'];
    if (in_array($alertType, $critical, true)) return 'Critical';
    if (in_array($alertType, $warning, true)) return 'Warning';
    return 'Info';
}

$stmt = $conn->prepare("
    SELECT alert_id, patient_name, alert_type, status, created_at
    FROM tbl_alert
    WHERE profile_id = ?
    ORDER BY created_at DESC
    LIMIT 100
");

if (!$stmt) {
    echo json_encode([]);
    exit;
}

$stmt->bind_param("i", $profile_id);
$stmt->execute();
$result = $stmt->get_result();

$alerts = [];

while ($row = $result->fetch_assoc()) {
    $alerts[] = [
        'timestamp' => !empty($row['created_at']) ? date('M j, Y h:i A', strtotime($row['created_at'])) : 'No date',
        'patient_name' => $profile['fullname'],
        'alert_type' => $row['alert_type'] ?? 'Alert',
        'severity' => getSeverity($row['alert_type'] ?? ''),
        'status' => $row['status'] ?? 'Unread'
    ];
}

$stmt->close();
echo json_encode($alerts);
?>