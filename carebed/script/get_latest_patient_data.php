<?php
session_start();
include 'db_connection.php';
header('Content-Type: application/json');

function has_column(mysqli $conn, string $table, string $column): bool {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return $res && $res->num_rows > 0;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

if ($patient_id <= 0 && $user_id > 0) {
    $stmt = $conn->prepare("SELECT profile_id FROM tbl_profile WHERE user_id = ? OR profile_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($profile) {
            $patient_id = (int)$profile['profile_id'];
        }
    }
}

$hasTemperature = has_column($conn, 'tbl_patient_data', 'temperature');
$hasWetness = has_column($conn, 'tbl_patient_data', 'wetness');
$hasPresence = has_column($conn, 'tbl_patient_data', 'presence_detection');

$temperatureSelect = $hasTemperature ? 'temperature' : 'respiration';
$wetnessSelect = $hasWetness ? 'wetness' : 'movement';
$presenceSelect = $hasPresence ? 'presence_detection' : 'sleep_quality';

$sql = "
    SELECT 
        heart_rate,
        {$temperatureSelect} AS temperature,
        {$wetnessSelect} AS wetness,
        {$presenceSelect} AS presence_detection,
        timestamp
    FROM tbl_patient_data
";

$params = [];
$types = '';

if ($patient_id > 0) {
    $sql .= " WHERE patient_id = ? ";
    $params[] = $patient_id;
    $types .= 'i';
}

$sql .= " ORDER BY timestamp DESC, id DESC LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $conn->error]);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data && $patient_id > 0) {
    $fallback = $conn->query("SELECT heart_rate, {$temperatureSelect} AS temperature, {$wetnessSelect} AS wetness, {$presenceSelect} AS presence_detection, timestamp FROM tbl_patient_data ORDER BY timestamp DESC, id DESC LIMIT 1");
    $data = $fallback ? $fallback->fetch_assoc() : null;
}

if (!$data) {
    echo json_encode([
        'status' => 'success',
        'heart_rate' => '--',
        'temperature' => '--',
        'wetness' => '--',
        'presence_detection' => '--',
        'temperature_status' => 'No sensor data',
        'wetness_status' => 'No sensor data',
        'presence_status' => 'No sensor data',
        'timestamp' => 'No data'
    ]);
    exit;
}

$temp = is_numeric($data['temperature']) ? (float)$data['temperature'] : null;
$wetness = trim((string)($data['wetness'] ?? '--'));
$presence = trim((string)($data['presence_detection'] ?? '--'));

$temperatureStatus = '--';
if ($temp !== null) {
    if ($temp < 35.5) $temperatureStatus = 'Low Temperature';
    elseif ($temp > 37.8) $temperatureStatus = 'High Temperature';
    else $temperatureStatus = 'Normal Temperature';
}

$wetnessStatus = in_array(strtolower($wetness), ['wet', 'detected', 'yes', '1'], true)
    ? 'Wetness detected'
    : 'No wetness detected';

$presenceStatus = in_array(strtolower($presence), ['present', 'detected', 'yes', '1'], true)
    ? 'Patient Present'
    : 'No presence detected';

echo json_encode([
    'status' => 'success',
    'heart_rate' => $data['heart_rate'] ?? '--',
    'temperature' => $data['temperature'] ?? '--',
    'wetness' => $wetness !== '' ? $wetness : '--',
    'presence_detection' => $presence !== '' ? $presence : '--',
    'temperature_status' => $temperatureStatus,
    'wetness_status' => $wetnessStatus,
    'presence_status' => $presenceStatus,
    'timestamp' => $data['timestamp'] ?? 'No data'
]);

$conn->close();
?>
