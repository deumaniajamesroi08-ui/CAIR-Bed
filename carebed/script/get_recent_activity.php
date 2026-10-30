<?php
include 'db_connection.php';
header('Content-Type: application/json');

$sql = "
    SELECT 
        d.id,
        d.patient_id,
        d.heart_rate,
        d.respiration,
        d.movement,
        d.sleep_quality,
        d.timestamp,
        p.fullname
    FROM tbl_patient_data d
    LEFT JOIN tbl_profile p ON d.patient_id = p.profile_id
    ORDER BY d.timestamp DESC
    LIMIT 5
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "error" => true,
        "message" => $conn->error
    ]);
    exit;
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $fullname = !empty($row['fullname']) ? $row['fullname'] : 'Unknown Patient';

    $nameParts = explode(' ', trim($fullname));
    $initials = '';

    foreach ($nameParts as $part) {
        if (!empty($part)) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }

    $initials = substr($initials, 0, 2);
    if (empty($initials)) {
        $initials = 'PT';
    }

    if (!is_null($row['heart_rate'])) {
        $data[] = [
            "initials" => $initials,
            "fullname" => $fullname,
            "message" => "Heart rate updated to " . $row['heart_rate'] . " BPM",
            "timestamp" => $row['timestamp']
        ];
    }

    if (!is_null($row['respiration'])) {
        $data[] = [
            "initials" => $initials,
            "fullname" => $fullname,
            "message" => "Respiration recorded at " . $row['respiration'] . " RPM",
            "timestamp" => $row['timestamp']
        ];
    }

    if (!empty($row['movement'])) {
        $data[] = [
            "initials" => $initials,
            "fullname" => $fullname,
            "message" => "Movement status changed to " . $row['movement'],
            "timestamp" => $row['timestamp']
        ];
    }

    if (!empty($row['sleep_quality'])) {
        $data[] = [
            "initials" => $initials,
            "fullname" => $fullname,
            "message" => "Sleep quality marked as " . $row['sleep_quality'],
            "timestamp" => $row['timestamp']
        ];
    }
}

usort($data, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});

$data = array_slice($data, 0, 5);

echo json_encode($data);

$conn->close();
?>