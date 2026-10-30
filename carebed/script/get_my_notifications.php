<?php
session_start();
include 'db_connection.php';
include_once 'carebed_logged_profile.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$profile = carebed_logged_profile($conn);
$profile_id = (int)$profile['profile_id'];

function notifSeverity($type) {
    $type = strtolower((string)$type);
    if (strpos($type, 'heart') !== false || strpos($type, 'temperature') !== false || strpos($type, 'sos') !== false || strpos($type, 'emergency') !== false) return 'critical';
    if (strpos($type, 'wetness') !== false || strpos($type, 'movement') !== false || strpos($type, 'warning') !== false) return 'warning';
    return 'info';
}

function timeAgo($datetime) {
    if (empty($datetime)) return 'Just now';
    $ts = strtotime($datetime);
    if (!$ts) return 'Just now';
    $diff = time() - $ts;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm';
    if ($diff < 86400) return floor($diff / 3600) . 'h';
    return floor($diff / 86400) . 'd';
}

$stmt = $conn->prepare("
    SELECT alert_id, profile_id, patient_name, alert_type, alert_message, sensor_value, status, sent_email, created_at
    FROM tbl_alert
    WHERE profile_id = ?
    ORDER BY created_at DESC
    LIMIT 30
");

if (!$stmt) {
    echo json_encode(['status'=>'error','unread_count'=>0,'notifications'=>[]]);
    exit;
}

$stmt->bind_param("i", $profile_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
$unread = 0;

while ($row = $result->fetch_assoc()) {
    $isUnread = strtolower((string)($row['status'] ?? 'Unread')) === 'unread';
    if ($isUnread) $unread++;

    $alertType = $row['alert_type'] ?: 'CAIR-Bed Alert';
    $message = trim((string)($row['alert_message'] ?? ''));

    if ($message === '') {
        $sensorValue = trim((string)($row['sensor_value'] ?? ''));
        $message = $sensorValue !== '' ? $alertType . ' detected: ' . $sensorValue : 'CAIR-Bed detected a patient monitoring update.';
    }

    $notifications[] = [
        'id' => (int)$row['alert_id'],
        'title' => $alertType,
        'alert_type' => $alertType,
        'patient_name' => $profile['fullname'],
        'message' => $message,
        'status' => $row['status'] ?: 'Unread',
        'severity' => notifSeverity($alertType),
        'created_at' => $row['created_at'],
        'time_ago' => timeAgo($row['created_at']),
        'sent_email' => (int)($row['sent_email'] ?? 0)
    ];
}

$stmt->close();

echo json_encode([
    'status'=>'success',
    'profile_id'=>$profile_id,
    'fullname'=>$profile['fullname'],
    'email'=>$profile['email'],
    'unread_count'=>$unread,
    'notifications'=>$notifications
]);
?>