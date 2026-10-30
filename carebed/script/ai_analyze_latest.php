<?php
session_start();
include 'db_connection.php';
require_once __DIR__ . '/../ai/cmd/main.php';


if (!isset($conn) && isset($GLOBALS['conn'])) {
    $conn = $GLOBALS['conn'];
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection not available.'
    ]);
    exit;
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

function ai_column_exists(mysqli $conn, string $table, string $column): bool {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return $res && $res->num_rows > 0;
}

$profile_id = $user_id;
$patientName = 'Unknown Patient';
$age = 0;

$stmt = $conn->prepare("
    SELECT profile_id, fullname, age
    FROM tbl_profile
    WHERE profile_id = ? OR user_id = ?
    LIMIT 1
");

if ($stmt) {
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $profile_id = (int) $row['profile_id'];
        $patientName = !empty($row['fullname']) ? $row['fullname'] : 'Unknown Patient';
        $age = is_numeric($row['age'] ?? null) ? (int) $row['age'] : 0;
    }

    $stmt->close();
}

$hasTemperature = ai_column_exists($conn, 'tbl_patient_data', 'temperature');
$hasWetness = ai_column_exists($conn, 'tbl_patient_data', 'wetness');
$hasPresence = ai_column_exists($conn, 'tbl_patient_data', 'presence_detection');

$temperatureSelect = $hasTemperature ? 'temperature' : 'respiration';
$wetnessSelect = $hasWetness ? 'wetness' : 'movement';
$presenceSelect = $hasPresence ? 'presence_detection' : 'sleep_quality';

$stmt = $conn->prepare("
    SELECT
        heart_rate,
        {$temperatureSelect} AS temperature,
        {$wetnessSelect} AS wetness,
        {$presenceSelect} AS presence_detection,
        timestamp
    FROM tbl_patient_data
    WHERE patient_id = ?
    ORDER BY timestamp DESC, id DESC
    LIMIT 1
");

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database prepare failed: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $profile_id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();
$stmt->close();

if (!$data) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No sensor data found for this logged-in user.'
    ]);
    exit;
}

$heartRate = (float) ($data['heart_rate'] ?? 0);
$temperature = (float) ($data['temperature'] ?? 0);
$wetness = $data['wetness'] ?? 'Dry';
$presenceDetection = $data['presence_detection'] ?? 'Unknown';

$payload = [
    'patient_name' => $patientName,
    'patient_age' => $age,
    'current_bpm' => (int) round($heartRate),
    'temperature' => $temperature,
    'wetness' => $wetness,
    'presence_detection' => $presenceDetection,
    'duration' => 'latest sensor reading'
];

try {
    $result = carebed_ai_analyze_payload($payload);
    $decoded = $result['ai_response'];
    $normalizedPayload = $result['normalized_payload'];
} catch (Throwable $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Analysis request failed.',
        'error' => $e->getMessage(),
        'payload' => $payload
    ]);
    exit;
}

$analysis = $decoded['analysis'] ?? json_encode($decoded);

$save = $conn->prepare("
    INSERT INTO tbl_ai_analysis
    (profile_id, heart_rate, temperature, wetness, analysis, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");

if ($save) {
    $save->bind_param("iddss", $profile_id, $heartRate, $temperature, $wetness, $analysis);
    $save->execute();
    $save->close();
}

echo json_encode([
    'status' => 'success',
    'payload' => $normalizedPayload,
    'ai_response' => $decoded
]);
?>
