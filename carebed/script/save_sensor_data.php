<?php
session_start();
include 'db_connection.php';
include_once 'carebed_logged_profile.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$requested_patient_id = $_POST['patient_id'] ?? $_GET['patient_id'] ?? null;
$heart_rate = $_POST['heart_rate'] ?? $_GET['heart_rate'] ?? 0;
$temperature = $_POST['temperature'] ?? $_GET['temperature'] ?? 0;
$wetness = $_POST['wetness'] ?? $_GET['wetness'] ?? 'Dry';
$presence_detection = $_POST['presence_detection'] ?? $_GET['presence_detection'] ?? 'Not Detected';

$heart_rate = (int)$heart_rate;
$temperature = (float)$temperature;
$wetness = trim((string)$wetness);
$presence_detection = trim((string)$presence_detection);

if ($requested_patient_id !== null && $requested_patient_id !== '') {
    $profile = carebed_profile_by_id($conn, (int)$requested_patient_id);
} else {
    $profile = carebed_logged_profile($conn);
}

$profile_id = (int)$profile['profile_id'];
$patient_id = $profile_id;
$patient_name = $profile['fullname'];
$patient_email = $profile['email'];

function carebed_column_exists($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $res && $res->num_rows > 0;
}

function carebed_table_exists($conn, $table) {
    $table = $conn->real_escape_string($table);
    $res = $conn->query("SHOW TABLES LIKE '$table'");
    return $res && $res->num_rows > 0;
}

function carebed_save_record($conn, $data) {
    if (!carebed_table_exists($conn, 'tbl_records')) {
        return false;
    }

    $columns = [];
    $values = [];
    $types = '';
    $now = false;

    foreach ($data as $column => $item) {
        if (!carebed_column_exists($conn, 'tbl_records', $column)) {
            continue;
        }
        $columns[] = "`$column`";
        $values[] = $item['value'];
        $types .= $item['type'];
    }

    foreach (['created_at', 'timestamp'] as $timeColumn) {
        if (carebed_column_exists($conn, 'tbl_records', $timeColumn)) {
            $columns[] = "`$timeColumn`";
            $now = true;
            break;
        }
    }

    if (!$columns) {
        return false;
    }

    $placeholders = array_fill(0, count($values), '?');
    if ($now) {
        $placeholders[] = 'NOW()';
    }

    $sql = 'INSERT INTO tbl_records (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    if ($values) {
        $stmt->bind_param($types, ...$values);
    }

    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function carebed_send_profile_email($to, $subject, $message) {
    if (empty($to)) return false;

    $sendEmailPath = __DIR__ . '/send_email.php';
    if (file_exists($sendEmailPath)) {
        require_once $sendEmailPath;
        if (function_exists('sendAlertEmail')) {
            ob_start();
            $ok = @sendAlertEmail($to, $subject, $message);
            ob_end_clean();
            return (bool)$ok;
        }
    }

    $headers = "From: CAIRBed Monitoring System <noreply@localhost>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    return @mail($to, $subject, $message, $headers);
}

function alertRecentlyExists($conn, $profile_id, $alert_type) {
    $stmt = $conn->prepare("
        SELECT alert_id
        FROM tbl_alert
        WHERE profile_id = ?
          AND alert_type = ?
          AND created_at >= (NOW() - INTERVAL 60 SECOND)
        LIMIT 1
    ");

    if (!$stmt) return false;

    $stmt->bind_param("is", $profile_id, $alert_type);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    return $exists;
}

function saveRealtimeAlert($conn, $profile_id, $patient_data_id, $patient_name, $patient_email, $alert_type, $alert_message, $sensor_value) {
    if (alertRecentlyExists($conn, $profile_id, $alert_type)) {
        return ['saved' => false, 'email_sent' => false, 'reason' => 'cooldown'];
    }

    $sent_email = 0;

    if (!empty($patient_email)) {
        $subject = "CAIRBed Alert: " . $alert_type;
        $emailBody =
            "Dear " . $patient_name . ",\n\n" .
            $alert_message . "\n\n" .
            "Please open your CAIRBed dashboard and check the patient immediately.\n\n" .
            "This is an automated alert from CAIRBed Monitoring System.";

        $sent_email = carebed_send_profile_email($patient_email, $subject, $emailBody) ? 1 : 0;
    }

    $stmt = $conn->prepare("
        INSERT INTO tbl_alert
        (profile_id, patient_data_id, patient_name, alert_type, alert_message, sensor_value, status, sent_email, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'Unread', ?, NOW())
    ");

    if (!$stmt) {
        return ['saved' => false, 'email_sent' => false, 'reason' => $conn->error];
    }

    $stmt->bind_param("iissssi", $profile_id, $patient_data_id, $patient_name, $alert_type, $alert_message, $sensor_value, $sent_email);
    $ok = $stmt->execute();
    $stmt->close();

    return ['saved' => $ok, 'email_sent' => (bool)$sent_email, 'reason' => $ok ? 'saved' : 'insert_failed'];
}

$stmt = $conn->prepare("
    UPDATE tbl_patient_data
    SET heart_rate = ?, temperature = ?, wetness = ?, presence_detection = ?, timestamp = NOW()
    WHERE patient_id = ?
");

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("idssi", $heart_rate, $temperature, $wetness, $presence_detection, $patient_id);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]);
    exit;
}

$stmt->close();

$patient_data_id = 0;
$idStmt = $conn->prepare("SELECT id FROM tbl_patient_data WHERE patient_id = ? ORDER BY timestamp DESC, id DESC LIMIT 1");

if ($idStmt) {
    $idStmt->bind_param("i", $patient_id);
    $idStmt->execute();
    $row = $idStmt->get_result()->fetch_assoc();
    $patient_data_id = (int)($row['id'] ?? 0);
    $idStmt->close();
}

if ($patient_data_id === 0) {
    $insert = $conn->prepare("
        INSERT INTO tbl_patient_data
        (patient_id, heart_rate, temperature, wetness, presence_detection, timestamp)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    if (!$insert) {
        echo json_encode(["status" => "error", "message" => "Insert prepare failed: " . $conn->error]);
        exit;
    }

    $insert->bind_param("iidss", $patient_id, $heart_rate, $temperature, $wetness, $presence_detection);

    if (!$insert->execute()) {
        echo json_encode(["status" => "error", "message" => "Insert failed: " . $insert->error]);
        exit;
    }

    $patient_data_id = $insert->insert_id;
    $insert->close();
}

carebed_save_record($conn, [
    'profile_id' => ['value' => $profile_id, 'type' => 'i'],
    'patient_id' => ['value' => $patient_id, 'type' => 'i'],
    'patient_name' => ['value' => $patient_name, 'type' => 's'],
    'heart_rate' => ['value' => $heart_rate, 'type' => 'i'],
    'temperature' => ['value' => $temperature, 'type' => 'd'],
    'wetness' => ['value' => $wetness, 'type' => 's'],
    'presence_detection' => ['value' => $presence_detection, 'type' => 's'],
    'status' => ['value' => 'Recorded', 'type' => 's']
]);

$created_alerts = [];
$email_results = [];

if ($heart_rate >= 100 && $heart_rate <= 150) {
    $type = 'Abnormal Heart Rate';
    $msg = "Cairbed Monitor detected elevated heart rate for {$patient_name}: {$heart_rate} BPM. Normal resting range is usually 60–100 BPM. Please check and monitor the patient.";
    $result = saveRealtimeAlert($conn, $profile_id, $patient_data_id, $patient_name, $patient_email, $type, $msg, $heart_rate . ' BPM');
    if ($result['saved']) $created_alerts[] = $type;
    $email_results[$type] = $result['email_sent'];
}

if ($heart_rate > 0 && $heart_rate < 50) {
    $type = 'Low Heart Rate';
    $msg = "Cairbed Monitor detected low heart rate for {$patient_name}: {$heart_rate} BPM. Please check and monitor the patient.";
    $result = saveRealtimeAlert($conn, $profile_id, $patient_data_id, $patient_name, $patient_email, $type, $msg, $heart_rate . ' BPM');
    if ($result['saved']) $created_alerts[] = $type;
    $email_results[$type] = $result['email_sent'];
}

if ($temperature >= 38) {
    $type = 'High Temperature';
    $msg = "Cairbed Monitor detected high temperature for {$patient_name}: " . number_format($temperature, 2) . " °C. Please monitor the patient.";
    $result = saveRealtimeAlert($conn, $profile_id, $patient_data_id, $patient_name, $patient_email, $type, $msg, number_format($temperature, 2) . ' °C');
    if ($result['saved']) $created_alerts[] = $type;
    $email_results[$type] = $result['email_sent'];
}

$wetLower = strtolower($wetness);
if ($wetLower === 'wet' || $wetLower === 'moist' || $wetLower === 'damp') {
    $type = 'Wetness Detected';
    $msg = "Cairbed Monitor detected bed wetness/moisture for {$patient_name}: {$wetness}. Please check the bed condition.";
    $result = saveRealtimeAlert($conn, $profile_id, $patient_data_id, $patient_name, $patient_email, $type, $msg, $wetness);
    if ($result['saved']) $created_alerts[] = $type;
    $email_results[$type] = $result['email_sent'];
}

echo json_encode([
    "status" => "success",
    "message" => "Realtime sensor data saved",
    "profile_id" => $profile_id,
    "patient_id" => $patient_id,
    "patient_name" => $patient_name,
    "patient_email" => $patient_email,
    "heart_rate" => $heart_rate,
    "temperature" => $temperature,
    "wetness" => $wetness,
    "presence_detection" => $presence_detection,
    "alerts_created" => $created_alerts,
    "email_results" => $email_results
]);

$conn->close();
?>