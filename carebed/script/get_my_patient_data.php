<?php
session_start();
include 'db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];


$userStmt = $conn->prepare("
    SELECT 
        u.id AS user_id,
        u.username,
        p.profile_id,
        p.fullname,
        p.age
    FROM tbl_user u
    LEFT JOIN tbl_profile p ON p.profile_id = u.id OR p.user_id = u.id
    WHERE u.id = ?
    LIMIT 1
");

if (!$userStmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User prepare failed: ' . $conn->error
    ]);
    exit;
}

$userStmt->bind_param('i', $user_id);
$userStmt->execute();
$userRow = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

if (!$userRow) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User record not found'
    ]);
    exit;
}

$displayName = !empty($userRow['fullname']) ? $userRow['fullname'] : $userRow['username'];
$profileId = !empty($userRow['profile_id']) ? (int)$userRow['profile_id'] : $user_id;

function has_column($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $res && $res->num_rows > 0;
}

$hasTemperature = has_column($conn, 'tbl_patient_data', 'temperature');
$hasWetness = has_column($conn, 'tbl_patient_data', 'wetness');
$hasPresence = has_column($conn, 'tbl_patient_data', 'presence_detection');

$temperatureSelect = $hasTemperature ? 'temperature' : '0 AS temperature';
$wetnessSelect = $hasWetness ? 'wetness' : "'Dry' AS wetness";
$presenceSelect = $hasPresence ? 'presence_detection' : "'Not Detected' AS presence_detection";

$dataSql = "
    SELECT
        id,
        patient_id,
        heart_rate,
        $temperatureSelect,
        $wetnessSelect,
        $presenceSelect,
        timestamp
    FROM tbl_patient_data
    ORDER BY id DESC
    LIMIT 1
";

$dataResult = $conn->query($dataSql);
$dataRow = $dataResult ? $dataResult->fetch_assoc() : null;

$heart = $dataRow['heart_rate'] ?? 0;
$temp = $dataRow['temperature'] ?? 0;
$wetness = $dataRow['wetness'] ?? '--';
$presence = $dataRow['presence_detection'] ?? '--';
$timestamp = $dataRow['timestamp'] ?? 'No data yet';

$status = 'Normal';
$statusClass = 'normal';

$heartNum = is_numeric($heart) ? (float)$heart : null;
$tempNum = is_numeric($temp) ? (float)$temp : null;
$wetLower = strtolower(trim((string)$wetness));
$presenceLower = strtolower(trim((string)$presence));

if (
    ($heartNum !== null && $heartNum > 0 && ($heartNum < 60 || $heartNum > 100)) ||
    ($tempNum !== null && $tempNum >= 38) ||
    $wetLower === 'wet' ||
    strpos($presenceLower, 'not') !== false
) {
    $status = 'Critical';
    $statusClass = 'critical';
} elseif (($tempNum !== null && $tempNum >= 37.5) || $wetLower === 'damp' || $wetLower === 'moist') {
    $status = 'Warning';
    $statusClass = 'warning';
}

echo json_encode([
    'status' => 'success',
    'patient' => [
        'user_id' => $user_id,
        'profile_id' => $profileId,
        'patient_name' => $displayName,
        'username' => $userRow['username'],
        'bed_id' => 'BED-' . str_pad($user_id, 3, '0', STR_PAD_LEFT),
        'age' => !empty($userRow['age']) ? $userRow['age'] : 'N/A',
        'heart_rate' => ($heart !== null && $heart !== '') ? $heart : 0,
        'temperature' => ($temp !== null && $temp !== '') ? $temp : 0,
        'wetness' => ($wetness !== null && $wetness !== '') ? $wetness : '--',
        'presence_detection' => ($presence !== null && $presence !== '') ? $presence : '--',
        'timestamp' => ($timestamp !== null && $timestamp !== '') ? $timestamp : 'No data yet',
        'condition_status' => $status,
        'condition_class' => $statusClass
    ]
]);

$conn->close();
?>
