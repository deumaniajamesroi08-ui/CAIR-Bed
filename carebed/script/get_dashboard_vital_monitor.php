<?php
include 'db_connection.php';
header('Content-Type: application/json');

function has_column($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $res && $res->num_rows > 0;
}

$hasTemperature = has_column($conn, 'tbl_patient_data', 'temperature');
$hasWetness = has_column($conn, 'tbl_patient_data', 'wetness');
$hasPresence = has_column($conn, 'tbl_patient_data', 'presence_detection');

$temperatureSelect = $hasTemperature ? 'd.temperature' : '0 AS temperature';
$wetnessSelect = $hasWetness ? 'd.wetness' : "'Dry' AS wetness";
$presenceSelect = $hasPresence ? 'd.presence_detection' : "'Not Detected' AS presence_detection";

$sqlLatest = "
    SELECT 
        d.id,
        d.patient_id,
        COALESCE(p.fullname, 'Unknown Patient') AS patient_name,
        d.heart_rate,
        $temperatureSelect,
        $wetnessSelect,
        $presenceSelect,
        d.timestamp
    FROM tbl_patient_data d
    LEFT JOIN tbl_profile p ON p.profile_id = d.patient_id
    ORDER BY d.id DESC
    LIMIT 1
";

$resultLatest = $conn->query($sqlLatest);
$latest = $resultLatest ? $resultLatest->fetch_assoc() : null;

if (!$latest) {
    echo json_encode([
        'status' => 'success',
        'latest' => [
            'patient_id' => null,
            'patient_name' => 'Waiting for patient data',
            'heart_rate' => 0,
            'temperature' => 0,
            'wetness' => 'Dry',
            'presence_detection' => 'Not Detected',
            'timestamp' => 'No sensor data yet'
        ],
        'history' => [
            'heart_rate' => array_fill(0, 12, 0),
            'temperature' => array_fill(0, 12, 0),
            'wetness' => array_fill(0, 12, 'Dry'),
            'presence' => array_fill(0, 12, 'Not Detected')
        ]
    ]);
    exit;
}

$historySql = "
    SELECT 
        heart_rate,
        " . ($hasTemperature ? 'temperature' : '0 AS temperature') . ",
        " . ($hasWetness ? 'wetness' : "'Dry' AS wetness") . ",
        " . ($hasPresence ? 'presence_detection' : "'Not Detected' AS presence_detection") . "
    FROM tbl_patient_data
    WHERE patient_id = " . (int)$latest['patient_id'] . "
    ORDER BY id DESC
    LIMIT 12
";

$historyResult = $conn->query($historySql);

$heart = [];
$temp = [];
$wet = [];
$presence = [];

if ($historyResult) {
    while ($row = $historyResult->fetch_assoc()) {
        $heart[] = $row['heart_rate'] !== null ? (float)$row['heart_rate'] : 0;
        $temp[] = $row['temperature'] !== null ? (float)$row['temperature'] : 0;
        $wet[] = $row['wetness'] ?: 'Dry';
        $presence[] = $row['presence_detection'] ?: 'Not Detected';
    }
}

$heart = array_reverse($heart);
$temp = array_reverse($temp);
$wet = array_reverse($wet);
$presence = array_reverse($presence);

while (count($heart) < 12) array_unshift($heart, 0);
while (count($temp) < 12) array_unshift($temp, 0);
while (count($wet) < 12) array_unshift($wet, 'Dry');
while (count($presence) < 12) array_unshift($presence, 'Not Detected');

$latest['heart_rate'] = $latest['heart_rate'] ?? 0;
$latest['temperature'] = $latest['temperature'] ?? 0;
$latest['wetness'] = $latest['wetness'] ?: 'Dry';
$latest['presence_detection'] = $latest['presence_detection'] ?: 'Not Detected';
$latest['timestamp'] = $latest['timestamp'] ?: 'No sensor data yet';

if (!$latest['patient_name']) {
    $latest['patient_name'] = 'Unknown Patient';
}

echo json_encode([
    'status' => 'success',
    'latest' => $latest,
    'history' => [
        'heart_rate' => $heart,
        'temperature' => $temp,
        'wetness' => $wet,
        'presence' => $presence
    ]
]);
?>
