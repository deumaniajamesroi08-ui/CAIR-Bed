<?php
session_start();
include 'db_connection.php';
include_once 'carebed_logged_profile.php';

$profile = carebed_logged_profile($conn);
$profile_id = (int)$profile['profile_id'];

$stmt = $conn->prepare("
    SELECT alert_type, alert_message, sensor_value, status, sent_email, created_at
    FROM tbl_alert
    WHERE profile_id = ?
    ORDER BY created_at DESC
    LIMIT 10
");

if (!$stmt) {
    echo '<div class="empty-row">Failed to load notifications.</div>';
    exit;
}

$stmt->bind_param("i", $profile_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="empty-row">No notifications yet.</div>';
    exit;
}

while ($row = $result->fetch_assoc()) {
    $type = htmlspecialchars($row['alert_type'] ?? 'Cairbed Alert');
    $msg = htmlspecialchars($row['alert_message'] ?? 'CAIRBed generated an alert.');
    $time = htmlspecialchars($row['created_at'] ?? '');
    $email = ((int)($row['sent_email'] ?? 0) === 1) ? 'Email sent' : 'Email not sent';

    echo '
    <div class="settings-ai-notification">
        <div class="settings-ai-notification-title">'.$type.'</div>
        <div class="settings-ai-notification-message">'.$msg.'</div>
        <div class="settings-ai-notification-footer">'.$time.' • '.$email.'</div>
    </div>';
}

$stmt->close();
?>