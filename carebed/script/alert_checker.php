<?php
include 'db_connection.php';
require 'send_email.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$action = $_GET['action'] ?? 'check';


if ($action === 'history') {

    $query = "SELECT alert_id, patient_name, alert_type, status, created_at 
              FROM tbl_alert 
              ORDER BY created_at DESC";

    $result = mysqli_query($conn, $query);

    $alerts = [];

    while ($row = mysqli_fetch_assoc($result)) {

        $severity = "Info";

        if (
            $row['alert_type'] === "Abnormal Heart Rate" ||
            $row['alert_type'] === "Abnormal Respiration" ||
            $row['alert_type'] === "Wetness Detected" ||
            $row['alert_type'] === "No Movement Detected"
        ) {
            $severity = "Critical";
        } elseif (
            $row['alert_type'] === "Low Movement" ||
            $row['alert_type'] === "Poor Sleep Quality"
        ) {
            $severity = "Warning";
        }

        $alerts[] = [
            'timestamp'    => date("M j, Y h:i A", strtotime($row['created_at'])),
            'patient_name' => $row['patient_name'],
            'alert_type'   => $row['alert_type'],
            'severity'     => $severity,
            'status'       => $row['status']
        ];
    }

    echo json_encode($alerts);
    exit;
}


$profile_id = isset($_GET['profile_id']) ? (int)$_GET['profile_id'] : 1;

$stmt = $conn->prepare("SELECT profile_id, fullname, email FROM tbl_profile WHERE profile_id = ? LIMIT 1");
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Profile not found.'
    ]);
    exit;
}

if (empty($user['email'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Caregiver email is empty.'
    ]);
    exit;
}

$caregiver_email = $user['email'];

$stmt = $conn->prepare("
    SELECT id, patient_id, heart_rate, respiration, movement, sleep_quality, timestamp
    FROM tbl_patient_data
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No patient data found in tbl_patient_data.'
    ]);
    exit;
}

$patient_data_id = $data['id'];
$patient_id      = $data['patient_id'];
$heart_rate      = $data['heart_rate'];
$respiration     = $data['respiration'];
$movement        = $data['movement'];
$sleep_quality   = $data['sleep_quality'];
$time_detected   = $data['timestamp'];
$patient_name    = !empty($user['fullname']) ? $user['fullname'] : 'Unknown Patient';

$alerts_sent = [];
$email_lines = [];
$highest_severity = "Info";

function saveAlert($conn, $profile_id, $patient_data_id, $patient_name, $alert_type, $alert_message, $sensor_value, $sent_email = 0)
{
    $stmt = $conn->prepare("
        INSERT INTO tbl_alert (profile_id, patient_data_id, patient_name, alert_type, alert_message, sensor_value, sent_email)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissssi", $profile_id, $patient_data_id, $patient_name, $alert_type, $alert_message, $sensor_value, $sent_email);
    return $stmt->execute();
}

function canSendCombinedAlert($conn, $profile_id, $patient_id)
{
    $stmt = $conn->prepare("
        SELECT last_sent
        FROM tbl_alert_cooldown
        WHERE profile_id = ? AND patient_id = ? AND alert_type = 'Combined Alert'
        ORDER BY last_sent DESC
        LIMIT 1
    ");
    $stmt->bind_param("ii", $profile_id, $patient_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        return true;
    }

    $last_sent = strtotime($row['last_sent']);
    return (time() - $last_sent) > 300; 
}

function updateCombinedCooldown($conn, $profile_id, $patient_id)
{
    $alert_type = 'Combined Alert';
    $stmt = $conn->prepare("
        INSERT INTO tbl_alert_cooldown (profile_id, patient_id, alert_type, last_sent)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param("iis", $profile_id, $patient_id, $alert_type);
    $stmt->execute();
}


if ($heart_rate < 50 || $heart_rate > 100) {
    $alerts_sent[] = "Abnormal Heart Rate";
    $email_lines[] = "- Abnormal Heart Rate: $heart_rate BPM";
    $highest_severity = "Critical";
}


if ($respiration < 12 || $respiration > 20) {
    $alerts_sent[] = "Abnormal Respiration";
    $email_lines[] = "- Abnormal Respiration: $respiration breaths/min";
    $highest_severity = "Critical";
}


if (strtolower($movement) === 'low') {
    $alerts_sent[] = "Low Movement";
    $email_lines[] = "- Low Movement: $movement";
    if ($highest_severity !== "Critical") {
        $highest_severity = "Warning";
    }
}


if (strtolower($sleep_quality) === 'poor') {
    $alerts_sent[] = "Poor Sleep Quality";
    $email_lines[] = "- Poor Sleep Quality: $sleep_quality";
    if ($highest_severity !== "Critical") {
        $highest_severity = "Warning";
    }
}


if (!empty($alerts_sent) && canSendCombinedAlert($conn, $profile_id, $patient_id)) {

    $subject = ($highest_severity === "Critical")
        ? "🚨 CAIR-Bed Alert: Multiple Conditions Detected"
        : "⚠️ CAIR-Bed Warning: Patient Condition Update";

    $message = "Dear Caregiver,

The CAIR-Bed system detected the following condition(s):

Patient ID: $patient_id
Patient: $patient_name
Time Detected: $time_detected

" . implode("\n", $email_lines) . "

Please check and monitor the patient immediately.

- CAIR-Bed Monitoring System";

    $sent = sendAlertEmail($caregiver_email, $subject, $message);
    $sent_flag = $sent ? 1 : 0;

    
    foreach ($alerts_sent as $alert_type) {
        $sensor_value = "";

        if ($alert_type === "Abnormal Heart Rate") {
            $sensor_value = $heart_rate . " BPM";
        } elseif ($alert_type === "Abnormal Respiration") {
            $sensor_value = $respiration . " breaths/min";
        } elseif ($alert_type === "Low Movement") {
            $sensor_value = $movement;
        } elseif ($alert_type === "Poor Sleep Quality") {
            $sensor_value = $sleep_quality;
        }

        saveAlert(
            $conn,
            $profile_id,
            $patient_data_id,
            $patient_name,
            $alert_type,
            $message,
            $sensor_value,
            $sent_flag
        );
    }

    updateCombinedCooldown($conn, $profile_id, $patient_id);
}

echo json_encode([
    'status' => 'success',
    'latest_data_id' => $patient_data_id,
    'message' => empty($alerts_sent)
        ? 'No alerts triggered.'
        : 'Alerts triggered: ' . implode(', ', $alerts_sent)
]);
?>